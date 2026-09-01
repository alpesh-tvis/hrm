<?php

namespace App\Http\Controllers;

use App\Models\ExtraDays;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\User;
use App\Models\LeaveSetting;
use Illuminate\Http\Request;
use Auth;
use DB;
use Carbon\Carbon;

class ExtraDaysController extends Controller
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

    public function index()
    {
        $currentadmin_id = Auth::id();
        $current_user_roll = auth()->user()->role;
        $financial_years = DB::table('extra_days')->select('financial_year')->groupBy('financial_year')->orderBy('financial_year', 'desc')->get();
        
        foreach ($financial_years as $yearval) {
         $checkcurrent_year = $yearval;            
        } 

        // $employeerec = DB::table('employees')->where('service_enddate', null)->where('id','!=', 1)->get();
        $employeerec = DB::table('employees')
        ->whereNull('service_enddate')
        ->whereNotIn('id', [1, 15]) 
        ->get();

        if($current_user_roll == '2'){
        $employeeExtraDays = ExtraDays::all();
        }else{
        $employeeExtraDays = ExtraDays::where('employee_id', $currentadmin_id)->orderBy('id', 'desc')->get();
        }

        return view('extra-days.index', compact('employeeExtraDays', 'financial_years', 'employeerec', 'current_user_roll'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {   
        $currentadmin_id = Auth::id();
        $current_user_roll = auth()->user()->role;
        $fincyear = $this->financial_year();
        // $employeerec = DB::table('employees')->where('service_enddate', null)->where('id','!=', 1)->get();
        $employeerec = DB::table('employees')
        ->whereNull('service_enddate')
        ->whereNotIn('id', [1, 15]) 
        ->get();
        if($current_user_roll == 2)
            return view('extra-days.create', compact('employeerec'));
        else{
            return redirect()->back()->with('error', 'You do not have permission to access this page.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   
        $fincyear = $this->financial_year();
        [$startYear, $endYear] = explode('-', $fincyear);

        $startOfFinancialYear = Carbon::createFromDate($startYear, 4, 1)->startOfDay(); 
        $endOfFinancialYear = Carbon::createFromDate($endYear, 3, 31)->endOfDay(); 

        $request->validate([
            'emplyoee_id' => 'required|integer|exists:employees,id', 
            'date' => [
            'required',
            'date',
                function ($attribute, $value, $fail) use ($startOfFinancialYear, $endOfFinancialYear) {
                    $selectedDate = Carbon::parse($value);
                    if ($selectedDate->lt($startOfFinancialYear) || $selectedDate->gt($endOfFinancialYear)) {

                        $fail('The date must be within the current financial year (' . $startOfFinancialYear->format('Y-m-d') . ' to ' . $endOfFinancialYear->format('Y-m-d') . ').');

                    }
                }
            ],  

            'reason_of_work_description' => 'required|string|max:2555',             
            'extra_day' => 'required|numeric|min:0.5',
 
        ]);

        $currentadmin_id = Auth::id();
        
        $emplyoee_id = $request->emplyoee_id;
        
        $inputdata = [];

        if ($emplyoee_id) {
            
            $selectedEmployee = DB::table('employees')->find($emplyoee_id);
            $empName = $selectedEmployee->first_name . ' ' . $selectedEmployee->last_name;

            if (!empty($selectedEmployee)) {
                $date = $request->date;
                $reason_of_work_description = $request->reason_of_work_description;
                $extra_day = $request->extra_day;

                /*$extra_day = $request->input('extra_day');
                $extra_day = rtrim(rtrim($extra_day, '0'), '.');*/

                $fincyear = $this->financial_year();
                $createdtime = Carbon::now();
                $updatetime = now();
                
                $inputdata = [
                    'employee_id' => $emplyoee_id,
                    'employee_name' => $empName, 
                    'date' => $date,
                    'reason_of_work_description' => $reason_of_work_description,
                    'extra_days' => $extra_day,
                    'financial_year' => $fincyear,
                    'created_at' => $createdtime,
                    'updated_at' => $updatetime,
                ];

                $empName = $selectedEmployee->first_name;
                
                ExtraDays::insert($inputdata);
                
                //Count ExtraDays Leave
                $employeeExtraDays = ExtraDays::where('employee_id', $currentadmin_id)->sum('extra_days');
                LeaveSetting::where('employee_id', $emplyoee_id)
                    ->where('financial_year', $fincyear)
                    ->update([
                        'extra_days' => $employeeExtraDays,
                        'remain_extdays' => DB::raw("remain_extdays + $employeeExtraDays"),
                    ]);
              
               return redirect()->back()->with('success', $empName . ' Extra days created recorded successfully🎉😄.');

            } else {
               
                return redirect()->back()->with('error', 'Employee not found.');
            }
        } else {
           
            return redirect()->back()->with('error', 'No employee selected.');
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(ExtraDays $extraDay)
    {   
        $extradayShow = ExtraDays::get();
        return view('extra_days_show')->with(compact('extradayShow'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExtraDays $extraDay, Request $request)
    {   
        $extraDayid = $request->id;
        $extraDay = ExtraDays::find($extraDayid);
        $current_user_roll = auth()->user()->role;

        //$employeerec = DB::table('employees')->where('service_enddate', null)->where('id','!=', 1)->get();
        $employeerec = DB::table('employees')
        ->whereNull('service_enddate')
        ->whereNotIn('id', [1, 15]) 
        ->get();

        $selectedEmployeeId = $extraDay ? $extraDay->employee_id : null;
        
        if($current_user_roll == 2)
            return view('extra-days.edit', compact('extraDay', 'employeerec', 'selectedEmployeeId'));
        else{
            return redirect()->back()->with('error', 'You do not have permission to access this page.');
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {   
        $id = $request->id;
        $current_user_roll = auth()->user()->role;
        $currentadmin_id = Auth::id();
        $emplyoee_id = $request->emplyoee_id;
        $extraDay = ExtraDays::find($id);
        if (!$extraDay) {
        return redirect()->back()->with('error', 'Record not found.');
        }

        $fincyear = $this->financial_year();
        [$startYear, $endYear] = explode('-', $fincyear);
        $startOfFinancialYear = Carbon::createFromDate($startYear, 4, 1)->startOfDay(); 
        $endOfFinancialYear = Carbon::createFromDate($endYear, 3, 31)->endOfDay();

        $request->validate([
        'date' => [
            'required',
            'date',
            function ($attribute, $value, $fail) use ($startOfFinancialYear, $endOfFinancialYear) {
                $selectedDate = Carbon::parse($value);
                if ($selectedDate->lt($startOfFinancialYear) || $selectedDate->gt($endOfFinancialYear)) {
                    $fail('The date must be within the current financial year (' . $startOfFinancialYear->format('Y-m-d') . ' to ' . $endOfFinancialYear->format('Y-m-d') . ').');
                }
            }
        ],  
        'extra_day' =>  'required','numeric','regex:/^(\d+(\.\d)?)$/','min:0.5',
        //'extra_day' => 'required|numeric|min:0.5',
        ]);
        $extra_day = $request->input('extra_day');
        $extra_day = rtrim(rtrim($extra_day, '0'), '.');

        $extraDay->update([
        //'employee_id' => $request->input('emplyoee_id'),
        'date' => $request->input('date'),
        'extra_days' => $extra_day,
        'reason_of_work_description' => $request->input('reason_of_work_description')
        ]);
        $empName = $request->input('employee_id');

         //Count ExtraDays Leave
        $employeeExtraDays = ExtraDays::where('employee_id', $currentadmin_id)->sum('extra_days');
        LeaveSetting::where('employee_id', $emplyoee_id)
            ->where('financial_year', $fincyear)
            ->update([
                'extra_days' => $employeeExtraDays,
                'remain_extdays' => DB::raw("remain_extdays + $employeeExtraDays"),
            ]);

        if($current_user_roll == 2)
           return redirect()->route('extra_days_edit', $extraDay->id)->with('success', ' Record updated successfully😊.');
        else{
            return redirect()->back()->with('error', 'You do not have permission to access this page.');
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
     public function destroy(Request $request, $id){
        ExtraDays::find($id)->delete();
        $trash_can_emoji = "\u{1F5D9}";
        return response()->json(['destroy_extra_day_success' => 'Extra Day row deleted successfully! '.  $trash_can_emoji]);
    }

}
