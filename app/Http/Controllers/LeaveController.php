<?php
namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Employee;
use App\Models\User;
use App\Models\SalaryDeduction; 
use App\Models\LeaveSetting;
use Illuminate\Http\Request;
use App\Mail\LeaveRequestMail;
use App\Mail\LeaveStatusChange;
use Auth;
use Carbon\Carbon;
use DB;
use Mail;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    
    public function getFinancialYearDates($year = null) {
        $year = $year ?? date('Y');
        $month = date('m');

        // Determine financial year start and end
        if ($month < 4) {
            $startYear = $year - 1;
            $endYear = $year;
        } else {
            $startYear = $year;
            $endYear = $year + 1;
        }

        return [
            'start_date' => "{$startYear}-04-01",
            'end_date'   => "{$endYear}-03-31"
        ];
    }

    public function index(Request $request)
    {
        $role = Auth::user()->role;
        $user_id = Auth::id();
        $emp_details = Employee::find($user_id);

        $fin_year = date("m") >= 4 ? date("Y"). '-' . (date("Y")+1) : (date("Y") - 1). '-' . date("Y");
        $currentYear = Carbon::now()->year;

        $results = Leave::whereYear('leave_date', $currentYear)
        ->where(DB::raw('MONTH(leave_date)'), '>', 3)
        ->orderBy('leave_date', 'desc')
        ->first();

        $dates = $this->getFinancialYearDates();
        
        $employees = Employee::get();

        if($request->users){
            $user_id = $request->users;
        }

        /* Cl Count */
        $count_half_CL =  Leave::where('user_id',$user_id)->where('leave_type','CL')->where('leave_status','!=','F')->where('status','Approved')->whereBetween('leave_date', [$dates['start_date'], $dates['end_date']])->count(); 

        if($count_half_CL == 0){
            $formatted_half_CL = 0;
        }
        else{
            $total_half_CL = $count_half_CL * 0.5;
            $formatted_half_CL = rtrim(rtrim($total_half_CL, '0'), '.');
        } 
        
        $count_full_CL =  Leave::where('user_id',$user_id)->where('leave_type','CL')->where('leave_status','F')->where('status','Approved')->whereBetween('leave_date', [$dates['start_date'], $dates['end_date']])->count(); // Full CL Count
        
        $total_Cl = $count_full_CL + $formatted_half_CL; // Total CL Count

        /* End Cl Count */

        /* PL Count */
        $count_half_PL =  Leave::where('user_id',$user_id)->where('leave_type','PL')->where('leave_status','!=','F')->where('status','Approved')->whereBetween('leave_date', [$dates['start_date'], $dates['end_date']])->count(); // Half PL Count

        if($count_half_PL == 0){
            $formatted_half_PL = 0;
        }
        else{
            $total_half_PL = $count_half_PL * 0.5;
            $formatted_half_PL = rtrim(rtrim($total_half_PL, '0'), '.');
        }
        
        $count_full_PL =  Leave::where('user_id',$user_id)->where('leave_type','PL')->where('leave_status','F')->where('status','Approved')->whereBetween('leave_date', [$dates['start_date'], $dates['end_date']])->count(); // Full PL Count
        
        $total_Pl = $count_full_PL + $formatted_half_PL; // Total PL Count

        /* End Pl Count */

        /* SL Count */
        $count_half_SL =  Leave::where('user_id',$user_id)->where('leave_type','SL')->where('leave_status','!=','F')->where('status','Approved')->whereBetween('leave_date', [$dates['start_date'], $dates['end_date']])->count(); // Half SL Count

        if($count_half_SL == 0){
            $formatted_half_SL = 0;
        }
        else{
            $total_half_SL = $count_half_SL * 0.5;
            $formatted_half_SL = rtrim(rtrim($total_half_SL, '0'), '.');
        }
        
        $count_full_SL =  Leave::where('user_id',$user_id)->where('leave_type','SL')->where('leave_status','F')->where('status','Approved')->whereBetween('leave_date', [$dates['start_date'], $dates['end_date']])->count(); // Full SL Count
        
        $total_Sl = $count_full_SL + $formatted_half_SL; // Total SL Count
        /* End Sl Count */

        $employees_list = array();
        if($role == '1' && $emp_details->reporting_person != 1){
            $leaves = Leave::where('user_id',$user_id)->orderBy('id','desc')->get();
        }else{
            $leaves = Leave::orderBy('id','desc')->get();
            
            $uniqueUserIds = $leaves->unique('user_id')->pluck('user_id');

            $employees_list = Employee::whereIn('id', $uniqueUserIds)
                         ->select('id', 'first_name', 'last_name')
                         ->where('service_enddate',null)
                         ->orderBy('first_name')->orderBy('last_name')
                         ->get();
        }
          
        return view('leave.list')->with(compact('leaves','role','total_Cl','total_Pl','total_Sl','employees','results','employees_list'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $role = Auth::user()->role;
        // role 2 admin
        if($role == 2){
            $employees = Employee::select('id','first_name','last_name')->where('service_enddate',null)->orderBy('first_name')->orderBy('last_name')->get();
            return view('leave.leave')->with(compact('employees','role'));
        }else{
            return view('leave.leave')->with(compact('role'));;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $admin_Email = "parth@tvistech.com";
        $role = Auth::user()->role;
        if($role == 2){
            $userId = $request->user_id;
            $leave_status = 'Approved';
        }
        if($role == 1){
            $userId = auth()->user()->id;
            $leave_status = 'Pending';
        }

        $employee = Employee::find($userId);
        $userName = $employee->first_name.' '.$employee->last_name;

        $reporting_person = Employee::where('id',$employee->reporting_person)->first();
        $reporting_person_email = $reporting_person->company_email;

        $user = User::find($userId);
        $userEmail = $user->email;
        
        $validatedData = $request->validate([
            'leave' => 'required|array',
            'leave.*.leave_reason' => 'required', 
        ]);

        if ($request->has('leave')) {
            $leaveDetails = '';
            
            $leave_status_arr = [ 
                'F'=>'Full Day', 
                'FH'=>'First Half', 
                'SH'=>'Second Half'
            ];

            $leave_type = [
                'PL'=>'Paid', 
                'SL'=>'Sick', 
                'CL'=>'Casual'
            ];
            foreach ($request->leave as $value) {

                $date = Carbon::createFromFormat('d/m/Y', $value['leave_date']);
                $formattedDate = $date->format('Y-m-d');

                if (Leave::where('user_id', $userId)->whereDate('leave_date', $formattedDate)->exists())
                {
                    return redirect()->route('leave.create')->withErrors(['error' => 'You have already submitted a leave request for this day.']);
                }
                
                $leave_type1 = $request->leave_type;
                $leave_status1 = $value['leave_status'];

                $leave = Leave::create([
                    'user_id' => $userId,
                    'leave_date' => $formattedDate,
                    'leave_status' => $value['leave_status'],
                    'leave_reason' => $value['leave_reason'],
                    'leave_type' => $request->leave_type,
                    'status' => $leave_status
                ]);
                
                $leave_date = date("F j, Y, l", strtotime($formattedDate));
                
                $leaveDetails .= "<div><strong>Date:</strong> {$leave_date} </div>";
                $leaveDetails .= "<div><strong>Leave Status:</strong> {$leave_status_arr[$leave_status1]} </div>";
                $leaveDetails .= "<div><strong>Leave Type:</strong> {$leave_type[$leave_type1]} </div>";
                $leaveDetails .= "<div style='margin-bottom:10px;'><strong>Reason:</strong> {$value['leave_reason']}</div>";
            }
            
            $data = [
                'userName' => $userName,
                'leaveDetails' => $leaveDetails,
                'user_email' => $userEmail
            ];
            
            
            //Send a notification email to the user
            try {
                
                \Log::info('Sending leave email', [
                    'admin' => $admin_Email,
                    'cc' => $reporting_person_email,
                    'bcc' => $userEmail
                ]);

                $mail = Mail::to($admin_Email);

                if (!empty($reporting_person_email)) {
                    $mail->cc($reporting_person_email);
                }

                if (!empty($userEmail)) {
                    $mail->bcc($userEmail);
                }

                $mail->send(new LeaveRequestMail($data));
            } catch (\Exception $e) {
                // Log the error but don't stop execution
                \Log::error('Leave request email failed: ' . $e->getMessage());
                //$emailStatus = 'Leave request saved, but email notification could not be sent.';
                return redirect()->route('leave.create')->withErrors(['error' => 'Leave request saved, but email notification could not be sent.']);
            }
         }    
            return redirect()->route('leave.create')->with('success', 'Leave request submitted successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    
    public function show(Request $request,  $id)
    {   
        $leave = Leave::find($id);
        $leave_user_id = $leave->user_id;
        
        $old_leave_status = $leave->status;
        $get_status_request = $request->status;
        
        if($old_leave_status == 'Pending' || $old_leave_status == 'Rejected' || $old_leave_status == 'Cancelled'){
            if($request->status == 'Approved'){
                /*if (method_exists($this, 'sal_ded_process_func')) {
                 $this->sal_ded_process_func($request, $id);
                }else {
                 dd('Function sal_ded_process_func does not exist.');
                }*/
            }
        }
        
        //exit();

        $leave->status = $request->status;
        $admin_email = "parth@tvistech.com";

        $emp_details = Employee::find($leave->user_id);
        $reporting_person_details = Employee::find($emp_details->reporting_person);
        
        $current_user_email = Auth::user()->email;
        $current_user_id =  Auth::user()->id;
        $user_email = $emp_details->company_email;
        $reporting_person_email = $reporting_person_details->company_email;
        
        $current_user_details = Employee::find($current_user_id);
        $userName = $current_user_details->first_name.' '.$current_user_details->last_name;

        $leave_date = date("F j, Y, l", strtotime($leave->leave_date));

        if($leave->leave_status == 'FH'){
            $leave_status = "First Half";
        }
        if($leave->leave_status == 'SH'){
            $leave_status = "Second Half";
        }
        if($leave->leave_status == 'F'){
            $leave_status = "Full Day";
        }
        if($leave->leave_status == ''){
            $leave_status = "";
        }
        $date=date_create($leave->leave_date);
        if (date_format($date,"m") >= 4) {
            $financial_year = (date_format($date,"Y")) . '-' . (date_format($date,"Y")+1);
        } else {
            $financial_year = (date_format($date,"Y")-1) . '-' . date_format($date,"Y");
        }

        $data = [
            "leave_date" => $leave_date,
            "leave_status" => $leave_status,
            "reporting_person_email" => $reporting_person_email,
            "user_email" => $user_email,
            "current_user_email" => $current_user_email,
            "userName" => $userName,
            "status"    => $leave->status,
            "emp_details" => $emp_details->first_name.' '.$emp_details->last_name
        ];
        $leave->save();

        // if($current_user_id != '1'){
        //     Mail::to([$user_email, $admin_email])
        //     ->bcc($current_user_email)
        //     ->send(new LeaveStatusChange($data));
        // }
        // if($current_user_id == 1){
        //     Mail::to([$user_email, $reporting_person_email])
        //     ->bcc($admin_email)
        //     ->send(new LeaveStatusChange($data));
        // }

        if($leave){
            return response()->json(['success' => 1]);
        }
    }
    
    public function salary_deduction_process(Request $request, $id){
        $financialY = $this->financial_year();
        $leave = Leave::find($id);
        $leave_user_id = $leave->user_id;
        $current_user_roll = auth()->user()->role;
        $get_leavesettings = LeaveSetting::where('employee_id', $leave_user_id)->where('financial_year', $financialY)->get();

        foreach ($get_leavesettings as $setting) {
            $total_per_year_casualLeaves = $setting->casual_leave;
            $total_per_year_paidLeave = $setting->paid_leave;
            $total_per_year_sickLeave = $setting->sick_leave;
            $total_per_year_pre_yearLeave = $setting->previous_year_leave;
            $remain_prev_yl = $setting->remain_prev_yl;
            $total_per_year_extraLeave = $setting->extra_days;
            $remain_extdays = $setting->remain_extdays;
            $financial_year = $setting->financial_year;
        }
        
        /* Cl Count */
        $pending_leaves_CL = Leave::where('user_id', $leave_user_id)
            ->where('leave_type', 'CL')  
            ->where('status', 'Pending')  
            ->get();  
        if ($pending_leaves_CL->isNotEmpty()) {
            foreach ($pending_leaves_CL as $leave) {
                $leave->status = 'Approved';  
                $leave->save();  
            }
        }
        $count_half_CL = Leave::where('user_id', $leave_user_id)
            ->where('leave_type', 'CL')  
            ->where('leave_status', '!=', 'F')  
            ->where('status', 'Approved')  
            ->latest('created_at')  
            ->count();  
        if ($count_half_CL == 0) {
            $formatted_half_CL = 0;
        } else {
            $total_half_CL = $count_half_CL * 0.5;
            $formatted_half_CL = rtrim(rtrim($total_half_CL, '0'), '.');  
        }
        $count_full_CL = Leave::where('user_id', $leave_user_id)
            ->where('leave_type', 'CL') 
            ->where('leave_status', 'F')  
            ->where('status', 'Approved')  
            ->latest('created_at')  
            ->count();  
        $taken_cl_leave = $count_full_CL + $formatted_half_CL;  
        $remaining_cl_leave = $total_per_year_casualLeaves - $taken_cl_leave; 
      
        /* CL Update */
        $update_cl_leave = LeaveSetting::where('employee_id', $leave_user_id)->where('financial_year', $financialY)
            ->update([
                'taken_cl_leave' => $taken_cl_leave, 
                'remaining_cl_leave' => $remaining_cl_leave, 
            ]);
        /* End CL Count */
        
        /* Start Get SL leave */
        $pending_leaves_SL = Leave::where('user_id', $leave_user_id)
            ->where('leave_type', 'SL')  
            ->where('status', 'Pending')  
            ->get();  
        if ($pending_leaves_SL->isNotEmpty()) {
            foreach ($pending_leaves_SL as $leave) {
                $leave->status = 'Approved';  
                $leave->save();  
            }
        }
        $count_half_SL = Leave::where('user_id', $leave_user_id)
            ->where('leave_type', 'SL')  
            ->where('leave_status', '!=', 'F')  
            ->where('status', 'Approved')  
            ->latest('created_at')  
            ->count();  
        if ($count_half_SL == 0) {
            $formatted_half_SL = 0;
        } else {
            $total_half_SL = $count_half_SL * 0.5;
            $formatted_half_SL = rtrim(rtrim($total_half_SL, '0'), '.');  
        }
        $count_full_SL = Leave::where('user_id', $leave_user_id)
            ->where('leave_type', 'SL') 
            ->where('leave_status', 'F')  
            ->where('status', 'Approved')  
            ->latest('created_at')  
            ->count();  
        $taken_sl_leave = $count_full_SL + $formatted_half_SL;  
        $remaining_sl_leave = $total_per_year_sickLeave - $taken_sl_leave; 
       
        $update_sl_leave = LeaveSetting::where('employee_id', $leave_user_id)->where('financial_year', $financialY)
            ->update([
                'taken_sl_leave' => $taken_sl_leave, 
                'remaining_sl_leave' => $remaining_sl_leave, 
            ]);
        /* End SL Count */

        /* Start Get PL leave */
        $pending_leaves_PL = Leave::where('user_id', $leave_user_id)
            ->where('leave_type', 'PL')  
            ->where('status', 'Pending')  
            ->get();  
        if ($pending_leaves_PL->isNotEmpty()) {
            foreach ($pending_leaves_PL as $leave) {
                $leave->status = 'Approved';  
                $leave->save();  
            }
        }
        $count_half_PL = Leave::where('user_id', $leave_user_id)
            ->where('leave_type', 'PL')  
            ->where('leave_status', '!=', 'F')  
            ->where('status', 'Approved')  
            ->latest('created_at')  
            ->count();  
        if ($count_half_PL == 0) {
            $formatted_half_PL = 0;
        } else {
            $total_half_PL = $count_half_PL * 0.5;
            $formatted_half_PL = rtrim(rtrim($total_half_PL, '0'), '.');  
        }
        $count_full_PL = Leave::where('user_id', $leave_user_id)
            ->where('leave_type', 'PL') 
            ->where('leave_status', 'F')  
            ->where('status', 'Approved')  
            ->latest('created_at')  
            ->count();  
        $taken_pl_leave = $count_full_PL + $formatted_half_PL;  
        $remaining_pl_leave = $total_per_year_paidLeave - $taken_pl_leave;
      
        $update_pl_leave = LeaveSetting::where('employee_id', $leave_user_id)->where('financial_year', $financialY)
            ->update([
                'taken_pl_leave' => $taken_pl_leave, 
                'remaining_pl_leave' => $remaining_pl_leave, 
            ]);
        /* End PL Count */

        /* Check Sick leave SL Adjust */
        $salary_deduction_sl_leave = 0; 
        if ($total_per_year_sickLeave < $taken_sl_leave) {
            $over_sl_leave = $taken_sl_leave - $total_per_year_sickLeave;
            if ($total_per_year_pre_yearLeave > 0) {
                $over_sl_leave = max(0, $over_sl_leave - $total_per_year_pre_yearLeave);
            }
            if ($total_per_year_extraLeave > 0) {
                $salary_deduction_sl_leave = max(0, $over_sl_leave - $total_per_year_extraLeave);
            } else {
                $salary_deduction_sl_leave = $over_sl_leave;
            }
            if ($salary_deduction_sl_leave > 0) {
                $salary_deduction_sl_leave;
            } else {
                // No salary deduction required
            }
        }
        $salary_leave_pre_leave =  $salary_deduction_sl_leave;
        if ($remain_prev_yl > 0 ) {
           $salary_deduction_sl_leave;
        }elseif($remain_extdays > 0){
           $salary_deduction_sl_leave;
        }
        
        /* update previous and extra leave */
        /*$adj_sl_leave = LeaveSetting::where('employee_id', $leave_user_id)->where('financial_year', $financialY)
            ->update([
                'previous_year_leave' => $taken_pl_leave, 
                'remain_extdays' => $remaining_pl_leave, 
            ]);*/

        
        /* Check Casual Leave CL Adjust */
        $salary_deduction_cl_leave = 0;
        if ($taken_cl_leave > $total_per_year_casualLeaves) {
            $over_cl_leave = $taken_cl_leave - $total_per_year_casualLeaves;
            if ($total_per_year_pre_yearLeave > 0) {
                $over_cl_leave = max(0, $over_cl_leave - $total_per_year_pre_yearLeave);
            }
            if ($remain_prev_yl > 0) {
                 $salary_deduction_cl_leave = max(0, $over_cl_leave - $remain_prev_yl);
            }
            /*dd($salary_deduction_cl_leave);
            exit();*/

            if ($total_per_year_extraLeave > 0) {
                $over_cl_leave = max(0, $over_cl_leave - $total_per_year_extraLeave);
                //dd('Extra '.$over_cl_leave);
            }

            if ($remain_extdays > 0) {
               $salary_deduction_cl_leave = max(0, $over_cl_leave - $remain_extdays);
            }
            $salary_deduction_cl_leave = $over_cl_leave;
            if ($salary_deduction_cl_leave > 0) {
                $salary_deduction_cl_leave;
            }else {
                //dd('No Salary Deduction Required.'); 
            }
        }

        /*$adj_cl_leave = LeaveSetting::where('employee_id', $leave_user_id)->where('financial_year', $financialY)
            ->update([
                'previous_year_leave' => $taken_cl_leave, 
                'remain_extdays' => $remaining_cl_leave, 
            ]);*/
        
        /* Check Paid Leave PL Adjust */
        $salary_deduction_pl_leave = 0; 
        if ($total_per_year_paidLeave < $taken_pl_leave) {
            $over_pl_leave = $taken_pl_leave - $total_per_year_paidLeave;
            
            if ($total_per_year_pre_yearLeave > 0) {
                $over_pl_leave = max(0, $over_pl_leave - $total_per_year_pre_yearLeave);
            }
            
            //update previous year leave
            /*$update_pre_year_leave = LeaveSetting::where('employee_id', $leave_user_id)->where('financial_year', $financialY)
              ->update([
                       'previous_year_leave' => $previous_year_leave,
                       'remain_prev_yl' => $remain_prev_yl,
                       ]);*/
            
            if ($remain_prev_yl > 0) {
                $over_pl_leave = max(0, $over_pl_leave - $remain_prev_yl);
            }
            //update remaining previous year leave

            if ($total_per_year_extraLeave > 0) {
                $salary_deduction_pl_leave = max(0, $over_pl_leave - $total_per_year_extraLeave);
            }elseif($remain_extdays > 0){
                $salary_deduction_pl_leave = max(0, $over_pl_leave - $remain_extdays);
            } else {
                $salary_deduction_pl_leave = $over_pl_leave;
            
            }
            //update extra leave
            if ($salary_deduction_pl_leave > 0) {
                $salary_deduction_pl_leave;
            } else {
                //dd('No Salary Deduction Required.'); 
            }
        }

        /* Get Emplyoee Name */
        $employeeData = Employee::find($leave_user_id);
        if ($employeeData) {
        $employeename = $employeeData->first_name;
        }

        /* Check leave type */
        if ($salary_deduction_pl_leave) {
            $leave_type = 'PL';
            $salary_deduction = $salary_deduction_pl_leave;
            $reason = 'No Any PL Previous Year Leave Extra Leave Available';
        }elseif ($salary_deduction_sl_leave) {
            $leave_type = 'SL';
            $salary_deduction = $salary_deduction_sl_leave;
            $reason = 'No Any SL Previous Year Leave Extra Leave Available';
        }elseif ($salary_deduction_cl_leave) {
            $leave_type = 'CL';
            $salary_deduction = $salary_deduction_cl_leave;
            $reason = 'No Any CL Previous Year Leave Extra Leave Available';
        }else{
            $leave_type = '';
            $salary_deduction = '';
            $reason = '';
        }

        $lastFewLeavesQuery = Leave::where('user_id', $leave_user_id)
        ->where('leave_type', $leave_type)
        ->where('status', 'Approved')
        ->orderBy('leave_date', 'desc');
        
        /*$integerPart = floor($salary_deduction);
        $fractionalPart = $salary_deduction - $integerPart; 
        $lastFewLeaves = $lastFewLeavesQuery->take($integerPart)->get();*/
        
        $salary_deduction = (float)$salary_deduction; 
        $integerPart = floor($salary_deduction);
        $fractionalPart = $salary_deduction - $integerPart;

        $lastFewLeaves = $lastFewLeavesQuery->take($integerPart)->get();
        if ($fractionalPart > 0) {
            $extraLeave = $lastFewLeavesQuery->skip($integerPart)->first();
            if ($extraLeave) {
                if ($fractionalPart == 0.5) {
                    $extraLeave->leave_status = 'FH';  
                    $extraLeave->leave_days = 0.5;
                }
              $lastFewLeaves->push($extraLeave);
            }
        } 
       
        $lastFewLeaves = Leave::where('user_id', $leave_user_id)
            ->where('leave_type', $leave_type)
            ->where('status', 'Approved')
            ->orderBy('leave_date', 'desc');

        if ($salary_deduction == 0.5) {
            $lastFewLeaves = $lastFewLeaves->take(1);
        } elseif (fmod($salary_deduction, 1) == 0.5) {
            $integer_part = (int) $salary_deduction;
            $lastFewLeaves = $lastFewLeaves->take($integer_part + 1);
        } else {
            $lastFewLeaves = $lastFewLeaves->take((int) $salary_deduction);
        }
        $lastFewLeaves = $lastFewLeaves->get();

        $salary_deductions_data = [];
        foreach ($lastFewLeaves as $leave) {
            $leave_status = $leave->leave_status;

            if ($salary_deduction == 0.5) {
                $leave_day = 'Half Day';
            } elseif ($leave_status == 'F') {
                $leave_day = 'Full Day';
            } elseif ($leave_status == 'FH') {
                $leave_day = 'First Half';
            } elseif ($leave_status == 'SH') {
                $leave_day = 'Second Half';
            } else {
                continue;
            }

            $leave_type = $leave->leave_type;
            $formattedDate = Carbon::parse($leave->leave_date)->format('Y-m-d');
            $month = Carbon::parse($leave->leave_date)->format('F');
            
            $salary_deductions_data = [
                'employee_id' => $leave_user_id,
                'employee_name' => $employeename,
                'leave_type' => $leave_type,
                'salary_deduction' => $leave_day,
                'reason' => $reason,
                'date' => $formattedDate,
                'month' => $month,
                'financial_year' => $financialY,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            break;
        }
        
        if (!empty($salary_deductions_data)) {
            try {
                SalaryDeduction::insert($salary_deductions_data);
            } catch (\Exception $e) {
                Log::error('Error inserting salary deductions: ' . $e->getMessage());
            }
        }
    
    }

    public function sal_ded_process_func(Request $request, $id){
        $financialY = $this->financial_year();
        $leave = Leave::find($id);
        $leave_user_id = $leave->user_id;
        $current_user_roll = auth()->user()->role;
        
        //ALL Per Year Leave setting data
        $get_leavesettings = LeaveSetting::where('employee_id', $leave_user_id)->where('financial_year', $financialY)->get();
        foreach ($get_leavesettings as $settingsdata) {
            $total_per_year_casualLeaves = $settingsdata->casual_leave;
            $total_per_year_paidLeave = $settingsdata->paid_leave;
            $total_per_year_sickLeave = $settingsdata->sick_leave;
            $total_per_year_pre_yearLeave = $settingsdata->previous_year_leave;
            $remain_prev_yl = $settingsdata->remain_prev_yl;
            $total_per_year_extraLeave = $settingsdata->extra_days;
            $remain_extdays = $settingsdata->remain_extdays;
            $financial_year = $settingsdata->financial_year;
        }

        //GET ALL LEAVE
        $all_approved_leaves = Leave::where('user_id', $leave_user_id)
            ->where('status', 'Approved')  
            ->get();
        $all_leave_count = $all_approved_leaves->count();    
         
        //GET CL LEAVE
        $count_half_CL = Leave::where('user_id', $leave_user_id)
            ->where('leave_type', 'CL')  
            ->where('leave_status', '!=', 'F')  
            ->where('status', 'Approved')  
            ->latest('created_at')  
            ->count();  
        if ($count_half_CL == 0) {
            $formatted_half_CL = 0;
        } else {
            $total_half_CL = $count_half_CL * 0.5;
            $formatted_half_CL = rtrim(rtrim($total_half_CL, '0'), '.');  
        }
        $count_full_CL = Leave::where('user_id', $leave_user_id)
            ->where('leave_type', 'CL') 
            ->where('leave_status', 'F')  
            ->where('status', 'Approved')  
            ->latest('created_at')  
            ->count(); 
        $taken_cl_leave = $count_full_CL + $formatted_half_CL;
        //dd($taken_cl_leave);

        if ($taken_cl_leave > $total_per_year_casualLeaves) {
            $remaining_cl_leave = $total_per_year_casualLeaves - $taken_cl_leave; 
            $remaining_cl_leave =  round($remaining_cl_leave);
            //dd($remaining_cl_leave);

            $lastFewCLLeavesQuery = Leave::where('user_id', $leave_user_id)
            ->where('leave_type', $leave_type)
            ->where('status', 'Approved')
            ->limit('status', 'Approved')
            ->orderBy('leave_date', 'desc');
        } 

        //GET PL LEAVE
        $count_half_PL = Leave::where('user_id', $leave_user_id)
            ->where('leave_type', 'PL')  
            ->where('leave_status', '!=', 'F')  
            ->where('status', 'Approved')  
            ->latest('created_at')  
            ->count();  
        if ($count_half_PL == 0) {
            $formatted_half_PL = 0;
        } else {
            $total_half_PL = $count_half_PL * 0.5;
            $formatted_half_PL = rtrim(rtrim($total_half_PL, '0'), '.');  
        }
        $count_full_PL = Leave::where('user_id', $leave_user_id)
            ->where('leave_type', 'PL') 
            ->where('leave_status', 'F')  
            ->where('status', 'Approved')  
            ->latest('created_at')  
            ->count(); 
        $taken_pl_leave = $count_full_PL + $formatted_half_PL;    
    
        //GET SL LEAVE
       $count_half_SL = Leave::where('user_id', $leave_user_id)
            ->where('leave_type', 'SL')  
            ->where('leave_status', '!=', 'F')  
            ->where('status', 'Approved')  
            ->latest('created_at')  
            ->count();  
        if ($count_half_SL == 0) {
            $formatted_half_SL = 0;
        } else {
            $total_half_SL = $count_half_SL * 0.5;
            $formatted_half_SL = rtrim(rtrim($total_half_SL, '0'), '.');  
        }
        $count_full_SL = Leave::where('user_id', $leave_user_id)
            ->where('leave_type', 'SL') 
            ->where('leave_status', 'F')  
            ->where('status', 'Approved')  
            ->latest('created_at')  
            ->count(); 
        $taken_sl_leave = $count_full_SL + $formatted_half_SL;
        //dd($taken_sl_leave);
    
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,  $id)
    {
        $leave = Leave::find($id);
        
        return view('leave.edit', compact('leave'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $leave = Leave::find($id);
        $leave->leave_type = $request->leave_type;
        $leave->leave_status = $request->leave_status;
        $leave->leave_date = $request->leave_date;
        $leave->leave_reason = $request->leave_reason;
        $leave->update();
        return redirect('/leave')->with('success', 'Leave Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $leave = Leave::find($id);
        if($request->delete_comment == 1 || $request->delete_comment == "1"){
            $leave->delete();
            return redirect('/leave')->with('success', 'Leave deleted successfully');
        }
        if($request->can_leave == "can_leave" ){
            $leave->status = 'Cancelled'; 
            $leave->save();
            return redirect('/leave')->with('success', 'Leave Cancelled successfully');
        }
    }

    /*public function leave_details(Request $request)
    {
        $today = Carbon::today();

        // Financial year calculation
        if ($today->month >= 4) {
            // If month is April (4) or after, financial year starts this year
            $startDate = Carbon::create($today->year, 4, 1);
            $endDate = Carbon::create($today->year + 1, 3, 31);
        } else {
            // If month is Jan-Mar, financial year started last year
            $startDate = Carbon::create($today->year - 1, 4, 1);
            $endDate = Carbon::create($today->year, 3, 31);
        }

        $user_id = Auth::user()->id;
        $current_user_roll = auth()->user()->role;
        
        if ($current_user_roll == '2') {
            $user_id = $request->user_list;
        }

        $employeerec = DB::table('employees')
        ->whereNull('service_enddate')
        ->whereNotIn('id', [1, 15]) 
        ->get();
        
        // if($current_user_roll == '2'){
        //     $user_id = $request->user_list;
        // }

        //$emp_details = Employee::find($user_id);

        $leaveDates = Leave::select('leave_date')->where('user_id',$user_id)->where('status','Approved')->whereBetween('leave_date', [$startDate, $endDate])->get()->pluck('leave_date')->toArray();
        
          
        $empRecord = Employee::find($user_id);
        //dd($empRecord);
        
        $months = [];
        
        foreach ($leaveDates as $leaveDate) {
            $monthName = Carbon::parse($leaveDate)->format('F');
            if (!in_array($monthName, $months)) {
                $months[] = $monthName;
            }
        }

        $leave_details = [];
        
        foreach ($months as $month) {
            $count_F = 0;
            $count_SH_FH = 0;
            
            $startOfMonth = Carbon::createFromFormat('F', $month)->year($today->year)->startOfMonth();
            $endOfMonth = $startOfMonth->copy()->endOfMonth();

            $monthlyLeaves = Leave::where('user_id', $user_id)
                ->where('status', 'Approved')
                ->whereBetween('leave_date', [$startOfMonth, $endOfMonth])
                ->orderBy('leave_date','asc')
                ->get(['leave_date', 'user_id', 'status','leave_type','leave_status']);

            
            foreach ($monthlyLeaves as $leave) {
                if ($leave->leave_status == 'F') {
                    $count_F++;
                } elseif ($leave->leave_status == 'SH' || $leave->leave_status == 'FH') {
                    $count_SH_FH++;
                }
            }  
              
            $leave_details[$month] = $monthlyLeaves->map(function ($leave) use ($count_F, $count_SH_FH) {
                
                $total_leave = $count_F + $count_SH_FH/2;
                return [
                    'leave_date' => Carbon::parse($leave->leave_date)->format('l, F j, Y'),
                    'user_id' => $leave->user_id,
                    'status' => $leave->status,
                    'leave_type' => $leave->leave_type,
                    'leave_status' => $leave->leave_status,
                    'full_leave' => $count_F,
                    'half_leave' => $count_SH_FH,
                    'total_leave' => $total_leave
                ];
            })->toArray();

            
        }
     return view('leave.leave-detail', compact('leave_details', 'current_user_roll', 'employeerec', 'empRecord'));
    }*/

    public function leave_details(Request $request)
    {
        $today = Carbon::today();

        // Financial year calculation
        if ($today->month >= 4) {
            $startDate = Carbon::create($today->year, 4, 1);
            $endDate   = Carbon::create($today->year + 1, 3, 31);
        } else {
            $startDate = Carbon::create($today->year - 1, 4, 1);
            $endDate   = Carbon::create($today->year, 3, 31);
        }

        $user_id = Auth::user()->id;
        $current_user_roll = auth()->user()->role;

        if ($current_user_roll == '2') {
            $user_id = $request->user_list;
        }

        $employeerec = DB::table('employees')
            ->whereNull('service_enddate')
            ->whereNotIn('id', [1, 15])
            ->get();

        $empRecord = Employee::find($user_id);

        $leave_details = [];

        //  Loop month by month in financial year
        $currentMonth = $startDate->copy();

        while ($currentMonth <= $endDate) {

            $monthName = $currentMonth->format('F Y');
            $startOfMonth = $currentMonth->copy()->startOfMonth();
            $endOfMonth   = $currentMonth->copy()->endOfMonth();

            $monthlyLeaves = Leave::where('user_id', $user_id)
                ->where('status', 'Approved')
                ->whereBetween('leave_date', [$startOfMonth, $endOfMonth])
                ->orderBy('leave_date', 'asc')
                ->get();

            $count_F = 0;
            $count_SH_FH = 0;

            foreach ($monthlyLeaves as $leave) {
                if ($leave->leave_status == 'F') {
                    $count_F++;
                } elseif (in_array($leave->leave_status, ['SH', 'FH'])) {
                    $count_SH_FH++;
                }
            }

            if ($monthlyLeaves->count() > 0) {
                $leave_details[$monthName] = $monthlyLeaves->map(function ($leave) use ($count_F, $count_SH_FH) {
                    return [
                        'leave_date'  => Carbon::parse($leave->leave_date)->format('l, d M Y'),
                        'leave_type'  => $leave->leave_type,
                        'leave_status'=> $leave->leave_status,
                        'full_leave'  => $count_F,
                        'half_leave'  => $count_SH_FH,
                        'total_leave' => $count_F + ($count_SH_FH / 2),
                    ];
                })->toArray();
            }

            $currentMonth->addMonth();
        }

        return view('leave.leave-detail', compact(
            'leave_details',
            'current_user_roll',
            'employeerec',
            'empRecord'
        ));
    }

}
