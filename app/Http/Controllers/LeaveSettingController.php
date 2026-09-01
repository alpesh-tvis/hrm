<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Employee;
use App\Models\User;
use App\Models\LeaveSetting;
use App\Models\SalaryDeduction; 
use App\Models\LeaveSalDeduction; 
use Illuminate\Http\Request;
use Auth;
use DB;
use Carbon\Carbon;

class LeaveSettingController extends Controller
{
    public function __construct(){
        $this->middleware(['auth','verified']);
    }
    public function financial_year(){
        $date = date('m');
        if ($date > 3) {
        $year = date('Y')."-".(date('Y') +1);
        }
        else {
        $year = (date('Y')-1)."-".date('Y');
        }
        return $year;
    }

    public function leave_setting(){
        $currentadmin_id = Auth::id();
        $fincyear = $this->financial_year();
        $year = $this->financial_year();

        $employeerecord = Employee::all()->except(Auth::id());


        /* New code */
            $financial_years = LeaveSetting::select('financial_year')->distinct()->pluck('financial_year')
            ->toArray();
            // dd($financial_years);
        /* End new code */
        $empdata = DB::table('employees')->get();
        // dd($empdata);
        $getdataleavesetting = [];

        foreach ($empdata as $emp) {
            $employeeId = $emp->id;

           if ($employeeId != 15 && $employeeId != 1) {
                $getdataleavesetting1 = DB::table('leave_setting')
                    ->leftJoin('employees', 'employees.id', '=', 'leave_setting.employee_id')
                    ->select('leave_setting.id', 'leave_setting.*')
                    ->whereNull('employees.service_enddate')
                    ->where('leave_setting.employee_id', $employeeId)
                    ->orderBy('leave_setting.emp_name')
                    ->get();

                $getdataleavesetting[$employeeId] = $getdataleavesetting1;
            }
        }

        $check_already_data = LeaveSetting::where('financial_year', $fincyear)->exists();
        
        $financial_years = DB::table('leave_setting')->select('financial_year')->groupBy('financial_year')->orderBy('financial_year', 'desc')->get();
        
        // foreach ($financial_years as $yearval) {
        //     $checkcurrent_year = $yearval;            
        // } 
        // dd($checkcurrent_year);
        // dd($checkcurrent_year);
        
        if( $currentadmin_id == 1){
            
        return view('leave.leave_setting')->with(compact('employeerecord', 'getdataleavesetting', 'financial_years', 'fincyear', 'year', 'check_already_data'));
        }
        else{
        return redirect()->back();
        }
    }

    public function leave_create(){
        $currentadmin_id = Auth::id();
        $fincyear = $this->financial_year();

        $check_already_fin = LeaveSetting::where('financial_year', $fincyear)->exists();

        // dd($check_already_fin);
        
        $employeerecord = DB::table('employees')->where('service_enddate', null)->where('id','!=', 1)->whereNotIn('id', DB::table('leave_setting')->where('financial_year', $fincyear)->pluck('employee_id'))->get();

        // dd($employeerecord);
        
        if($currentadmin_id == 1){
        return view('leave.leave_add_setting')->with(compact('employeerecord', 'check_already_fin'));
        }
        else{
        return redirect()->back();
        }
    }

    public function store(Request $request){
        $currentadmin_id = Auth::id();
        $fincyear = $this->financial_year();

        // $employeerec = DB::table('employees')->where('service_enddate', null)->where('id','!=', 1)->get();
        $employeerec = DB::table('employees')->where('service_enddate', null)->where('id','!=', 1)->whereNotIn('id', DB::table('leave_setting')->where('financial_year', $fincyear)->pluck('employee_id'))->get();

        // dd($employeerec);
        
        $sick_leave = $request->sick_leave;
        $paid_leave = $request->paid_leave;
        $casual_leave = $request->casual_leave;
        $previous_year_leave = "0";
        $extra_days = "0";
        
        $inputdata = array();
        $createdtime = Carbon::now();
        $updatetime = now();
        foreach($employeerec as $emprecordval){
         
            if(!empty($employeerec)) {


                // $fincyear = $this->financial_year();

               

                $inputdata[] =[
                    'employee_id' => $emprecordval->id,
                    'emp_name' => $emprecordval->first_name,
                    'sick_leave' => $sick_leave,
                    'paid_leave' => $paid_leave,
                    'casual_leave' => $casual_leave,
                    'previous_year_leave' => $previous_year_leave,
                    'extra_days' => $extra_days,
                    'financial_year' => $fincyear,
                    'created_at' => $createdtime,
                    'updated_at' => $updatetime,
                        
                ];                 

            }

        }


        if (!empty($inputdata)) {
            LeaveSetting::insert($inputdata);
            return redirect()->back()->with('success', 'Leave submitted successfully.');
        } else {
            return redirect()->back()->with('info', 'No eligible employees found or all employees already have leave settings for this year.');
        }
        // if (LeaveSetting::where('financial_year', $fincyear)->exists()) {
          // return redirect()->back()->with('already_existsdata', ' This financial year leave already exists.');
        // } else {
          // LeaveSetting::insert($inputdata);
          // return redirect()->back()->with('success', ' Leave submitted successfully');
        // }
    }

    public function settings_show(){
       $getleavesetting = LeaveSetting::get();
       return view('leave_setting')->with(compact('getleavesetting'));
    }

    public function leave_edit(Request $request){
        $currentadmin_id = Auth::id();
       
        $id = $request->id;
        $employeedata = LeaveSetting::find($request->id);
        if($currentadmin_id == 1){
        return view('leave.leave_edit_setting', compact('employeedata'));
        }
        else{
        return redirect()->back();
        }
        
    }
    public function setting_update(Request $request, $id){
        $id = $request->id;
        $settingupdate = LeaveSetting::find($id);
        
        if ($id) {
        $settingupdate->id = $request->id;
        $settingupdate->employee_id = $request->employee_id;
        $settingupdate->emp_name = $request->emp_name;
        $settingupdate->sick_leave = $request->sick_leave;
        $settingupdate->paid_leave = $request->paid_leave;
        $settingupdate->casual_leave = $request->casual_leave;
        $settingupdate->previous_year_leave = $request->previous_year_leave;
        $settingupdate->extra_days = $request->extra_days;
        $settingupdate->financial_year = $request->financial_year;
        }
        $empname =  $request->emp_name;
        
        $settingupdate->update();
         
        return redirect()->back()->with('success', $empname . ' Leave updated successfully');
    }

    public function main_setting_edit_come_new_member(){
       
       return view('leave.leave_update_setting');
 
    }
    public function main_setting_update_come_new_memeber(Request $request){
        $fincyear = $this->financial_year();
        $sick_leave = $request->sick_leave;
        $paid_leave = $request->paid_leave;
        $casual_leave = $request->casual_leave;
        
        // $getmain_setting_data = LeaveSetting::all();
        $getmain_setting_data = LeaveSetting::where('financial_year', $fincyear)->get();


        //$updatedata = array();
        foreach ($getmain_setting_data as $leavevalue) {
            $id = $leavevalue->id;
            $employee_id = $leavevalue->employee_id;
            $emp_name = $leavevalue->emp_name;
            $previous_year_leave = $leavevalue->previous_year_leave;
            $extra_days = $leavevalue->extra_days;
            $financial_year = $leavevalue->financial_year;
            $created_at = $leavevalue->created_at;


            $updatetime = now();
            $up_setting_data = LeaveSetting::where('id', '=', $id);
            
            $up_setting_data = $up_setting_data->update([
                    'sick_leave' => $sick_leave,
                    'paid_leave' => $paid_leave,
                    'casual_leave' => $casual_leave,
                    'updated_at' => now(),
                ]);

        }

        $employeerec = DB::table('employees')->where('service_enddate', null)->where('id','!=', 1)->get()->toArray();
        $allsetting_data = DB::table('leave_setting')->get()->toArray();

        $arra_da = array();
        $arra_nameda = array();
        foreach($employeerec as $valid){
            $arra_da[] = $valid->id;
            $arra_nameda[] = $valid->first_name;
        }
       
        $sett_arr = array();
        foreach($allsetting_data as $pd) {
            $sett_arr[] = $pd->employee_id;
        }
        
        $differences_data = array_diff($arra_da, $sett_arr);

        foreach ($differences_data as $valueid) {
            $arra_id = $valueid;
            
            $setting_data = Employee::where('id', '=', $arra_id)->select('first_name')->first();
            
            foreach ($setting_data as $firstvalue) {
               $firstname = $firstvalue;
            }
            

            //if ($ermpdid != $employee_id) {
                $new_memeber_insert =  DB::table('leave_setting')->insert(
                    array(
                        'employee_id'     =>   $arra_id, 
                        'emp_name'        =>   $setting_data['first_name'],
                        'sick_leave'      =>   $sick_leave,
                        'paid_leave'      =>   $paid_leave,
                        'casual_leave'    =>   $casual_leave,
                        'previous_year_leave'   =>  $previous_year_leave,
                        'extra_days'   =>  $extra_days,
                        'financial_year'  =>   $fincyear,
                        'created_at'      =>   $created_at,
                        'updated_at'      =>   $updatetime,
                    )
                );
               
            //}

        }
        
        return redirect()->back()->with('update_sett_success', $fincyear. ' Leave record updated successfully.');
    
    }
    public function setting_destroy(Request $request, $id){
        LeaveSetting::find($id)->delete();
        return response()->json(['removeleave_success' => 'Leave uetting row deleted successfully!']);
    }

}
