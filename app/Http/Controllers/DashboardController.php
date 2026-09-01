<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkReport;
use App\Models\Project;
use App\Models\Employee;
use App\Models\WorkreportList;
use App\Models\User;
use App\Models\WeekHour;
use App\Models\Leave;
use App\Models\LeaveSetting;
use App\Models\ExtraDays;
use App\Models\MailRequest;
use Carbon\Carbon;
use DB;
use DateTime;
use DateInterval;
use Auth;


class DashboardController extends Controller
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
        $user = Auth::user();
        $user->hasVerifiedEmail();

        $fincYear = $this->financial_year();

        $dates = $this->getFinancialYearDates();
        if($user->role == '2'){
            $today_date = date('Y-m-d');
            $previus_date = date('Y-m-d', strtotime('-7 days'));
            $get_project_id = WorkReport::whereBetween('work_date', [$previus_date, $today_date])->where('project_id','!=',null)->select('project_id')->groupBy('project_id')->get()->toArray();
            $project_name = Project::whereIn('id',$get_project_id)->orderBy('project_name','asc')->get();
        }    

        $user_id = Auth::id();
        /* PL Count */
        $leaveCounts = Leave::selectRaw("
            SUM(CASE WHEN leave_type = 'PL' AND leave_status != 'F' AND status = 'Approved' THEN 0.5 ELSE 0 END) AS half_PL_count,
            SUM(CASE WHEN leave_type = 'PL' AND leave_status = 'F' AND status = 'Approved' THEN 1 ELSE 0 END) AS full_PL_count,
            SUM(CASE WHEN leave_type = 'SL' AND leave_status != 'F' AND status = 'Approved' THEN 0.5 ELSE 0 END) AS half_SL_count,
            SUM(CASE WHEN leave_type = 'SL' AND leave_status = 'F' AND status = 'Approved' THEN 1 ELSE 0 END) AS full_SL_count,
            SUM(CASE WHEN leave_type = 'CL' AND leave_status != 'F' AND status = 'Approved' THEN 0.5 ELSE 0 END) AS half_CL_count,
            SUM(CASE WHEN leave_type = 'CL' AND leave_status = 'F' AND status = 'Approved' THEN 1 ELSE 0 END) AS full_CL_count
        ")->where('user_id', $user_id)
        ->whereBetween('leave_date', [$dates['start_date'], $dates['end_date']])
        ->first();

        // dd($leaveCounts);
        $total_Pl = 0;
        $total_Sl = 0;
        $total_Cl = 0;

        if($leaveCounts->half_PL_count){
            $formatted_half_PL = rtrim(rtrim($leaveCounts->half_PL_count, '0'), '.');
            $total_Pl = $leaveCounts->full_PL_count + $formatted_half_PL;
        }

        if($leaveCounts->half_SL_count){
            $formatted_half_SL = rtrim(rtrim($leaveCounts->half_SL_count, '0'), '.');
            $total_Sl = $leaveCounts->full_SL_count + $formatted_half_SL;
        }    

        if($leaveCounts->half_CL_count){
            $formatted_half_CL = rtrim(rtrim($leaveCounts->half_CL_count, '0'), '.');
            $total_Cl = $leaveCounts->full_CL_count + $formatted_half_CL;
        }    
        /* End Pl Count */

       $latestWeekHours = WeekHour::where('user_id', $user_id)
            ->orderBy('id', 'desc')
        ->get();

        $week_office_hours_entry = $latestWeekHours->firstWhere('next_week_hours', '!=', null);
        $Week_office_hours = $week_office_hours_entry ? $week_office_hours_entry->next_week_hours : '40:00:00';

        $week_startDate = now()->startOfWeek()->format('Y-m-d');
        $week_endDate = now()->endOfWeek()->format('Y-m-d');

        $exists_WeekHour = $latestWeekHours->firstWhere('week_start_date', $week_startDate)
            && $latestWeekHours->firstWhere('week_end_date', $week_endDate);

        $Week_working_hours_query = $exists_WeekHour 
            ? $latestWeekHours->skip(1)->first()  // Get the second most recent entry
            : $latestWeekHours->first();          // Get the most recent entry


        if(!empty($Week_working_hours_query->next_week_hours)){
            $Week_working_hours = $Week_working_hours_query->next_week_hours;
        }else{
            $Week_working_hours = "40:00:00";
        }
        
        /*** Total week Working Hours Calculation ***/
        $workdate = date('Y-m-d');
        $monday = date('Y-m-d', strtotime('monday this week'));
        $saturday = date('Y-m-d', strtotime('saturday this week'));
        $today = date('Y-m-d');
        
        $total_working_hours_query1 = WorkReport::where('user_id',$user_id)->whereBetween('work_date',[$monday, $today])->where('work_time','!=',null)->where('work_type','!=',3)->where('work_type','!=',4)->get();
                
        $all_seconds = 0;
        if($total_working_hours_query1){
            foreach ($total_working_hours_query1 as $time) {
                list($hour, $minute, $second) = explode(':', $time->work_time);
                $all_seconds += $hour * 3600;
                $all_seconds += $minute * 60;
                $all_seconds += $second;
            }
            
            $total_minutes = floor($all_seconds/60);

            $hours = floor($total_minutes / 60);
            $minutes = $total_minutes % 60;
            $seconds = $all_seconds % 60;
            if($hours < 10){
                $hours = "0".$hours;
            }
            if($minutes < 10){
                $minutes = "0".$minutes;
            }
            if($seconds < 10){
                $seconds = "0".$seconds;
            }
            $total_hours = $hours.':'.$minutes.':'.$seconds;

            $get_latest_total_working_hours_query = WorkReport::where('work_date', $workdate)->where('user_id',$user_id)->where('work_time',null)->where('work_type','!=',3)->where('work_type','!=',4)->where('work_type','!=',6)->first();
            if($get_latest_total_working_hours_query){
                $current_datetime1=Carbon::parse(date("Y-m-d H:i:s"));
                $running_total_working_hours = Carbon::parse($get_latest_total_working_hours_query->created_at);
                $total_working_hours_diff = $running_total_working_hours->diff($current_datetime1)->format('%H:%I:%S');
                
                $time =  $total_hours;
                $time2 = $total_working_hours_diff;
                    
                list ($hour1, $min1, $sec1) = explode(':', $time);
                list ($hour2, $min2, $sec2) = explode(':', $time2);
                
                $total_sec = $sec1+$sec2;
                $sumSec = $total_sec%60;
                $extra_min = ($total_sec-$sumSec)/60;
                //counting number of minutes and getting extra hours outs
                $total_min= $min1+$min2+$extra_min;
                $sumMin = $total_min%60;
                $extra_hr = ($total_min-$sumMin)/60;
                //counting number of hours
                $sumHour = $hour1 + $hour2 + $extra_hr;
                    
                if($sumHour < 10){
                    $hour = "0".$sumHour;
                }else{
                    $hour = $sumHour;
                }

                if($sumMin < 10){
                    $minute = "0".$sumMin;
                }else{
                    $minute = $sumMin;
                }
                if($sumSec < 10){
                    $second = "0".$sumSec;
                }else{
                    $second = $sumSec;
                }
                $total_working_hours = $hour.':'.$minute.':'.$second;
                
            }else{
                
                $total_working_hours = $total_hours;
            }
        }    

        /*** End Total week Working Hours Calculation ***/
        // Remaining Hours
        $total_working_hour_format = $total_working_hours;
            
        $total_week_working_hour = $Week_working_hours;
        $week_working_hour = $total_working_hour_format;
        

        list($hours1, $minutes1, $seconds1) = explode(':', $total_week_working_hour);
        list($hours2, $minutes2, $seconds2) = explode(':', $week_working_hour);

        $totalSeconds1 = $hours1 * 3600 + $minutes1 * 60 + $seconds1;
        $totalSeconds2 = $hours2 * 3600 + $minutes2 * 60 + $seconds2;

        $resultSeconds = $totalSeconds1 - $totalSeconds2;

        if ($resultSeconds < 0) {
            $resultSeconds = -$resultSeconds;
            $negative = true;
        } else {
            $negative = false;
        }

        $resultHours = floor($resultSeconds / 3600);
        $resultSeconds %= 3600;
        $resultMinutes = floor($resultSeconds / 60);
        $resultSeconds %= 60;
        if($resultHours < 10){
            $resultHours = "0".$resultHours;
        }
        if($resultMinutes < 10){
            $resultMinutes = "0".$resultMinutes;
        }
        if($resultSeconds < 10){
            $resultSeconds = "0".$resultSeconds;
        }
        $remaining_hour = ($negative ? '-' : '') . $resultHours . ':' . $resultMinutes . ':' . $resultSeconds;
        
        if($negative){
            $remaining_hour  = "00:00:00";
        }

        $today_entry = DB::table('workreports')
            ->where('user_id', $user_id)
            ->where('work_date', $workdate)
            ->orderBy('id', 'desc') // Order by 'id' to get the latest entry
        ->first();

        if ($today_entry) {
            // Check if there is an entry with work_type 4 for the end date
            $enddate = $today_entry->work_type == 4 ? 'end' : 'notend';

            // Check the work_type of the latest entry (already retrieved by the query)
            $break_check = $today_entry->work_type;
        } else {
            $enddate = 'notend';
            $break_check = 'not_start';
        }

        
        /* FROM ADMIN SIDE ADDED Leave & CHECK CURRENT YEAR*/
        
        $date = date('m');
        if ($date > 3) {
            $year = date('Y')."-".(date('Y') +1);
        }
        else {
            $year = (date('Y')-1)."-".date('Y');
        }

        $leaveSettings = LeaveSetting::where('employee_id', $user_id)
            ->where('financial_year', $year)
            ->select('paid_leave', 'sick_leave', 'casual_leave', 'previous_year_leave', 'remain_prev_yl', 'extra_days', 'remain_extdays')
        ->first();

        $all_count_PL = $leaveSettings->paid_leave ?? 0;
        $all_count_SL = $leaveSettings->sick_leave ?? 0;
        $all_count_CL = $leaveSettings->casual_leave ?? 0;
        $all_count_PRE = $leaveSettings->previous_year_leave ?? 0;
        $remain_prev_yl = $leaveSettings->remain_prev_yl ?? 0;
        $all_count_EXT_DAY = $leaveSettings->extra_days ?? 0;
        $remain_extdays = $leaveSettings->remain_extdays ?? 0;
        $remainLeavePl = $all_count_PL - $total_Pl;
        $remainLeaveSl = $all_count_SL - $total_Sl;
        $remainLeaveCl = $all_count_CL - $total_Cl;
        $extra_day = $all_count_EXT_DAY;
        $extra_day = (float) $extra_day;

        $total_taken_leave = 0;
        if ($remainLeaveCl > 0) {
            $total_taken_leave += $remainLeaveCl;
        }
        if ($remainLeavePl > 0) {
            $total_taken_leave += $remainLeavePl;
        }
        if($remainLeaveSl > 0){
            $total_taken_leave += $remainLeaveSl;
        }
        $remainLeave = ($remainLeaveCl > 0 ? $remainLeaveCl : 0) + ($remainLeaveSl > 0 ? $remainLeaveSl : 0) + ($remainLeavePl > 0 ? $remainLeavePl : 0);

        $totalTakenLeave =  $total_Cl + $total_Sl + $total_Pl;
        $remainYearLeave =  $remain_prev_yl + $remain_extdays + $remainLeave;
        $countPYL = $all_count_PRE;
        // dd($leaveSettings);
        $all_leave = $all_count_PL + $all_count_SL + $all_count_CL + $all_count_PRE + $all_count_EXT_DAY;

        /* FROM ADMIN SIDE ADDED Leave & CHECK CURRENT YEAR */
        $assign_project = Project::where('employee_id', 'like', '%'.$user->id.'%')->whereNull('end_date')->count();
   
        $startDate = date("Y-m-d");
        //$startDate = date("Y-m-d", strtotime($startDate1 . " -1 days"));
        $endDate = date("Y-m-d", strtotime($startDate . " +15 days"));

        $recent_emp_leave = Leave::with('employee')
                                 //->whereBetween('leave_date', [now(), now()->addDays(15)])
                                 ->whereBetween('leave_date', [today(),today()->addDays(15)])
                                 ->where('status','Approved')
                                 ->get()
                                 ->map(function ($leave){
                                     $statusMap = [
                                        'F' => 'Full Day',
                                        'FH'=> 'First Half',
                                        'SH' => 'Second Half'
                                     ];
                $leave->display_status = $statusMap[$leave->leave_status] ?? 'Unknown';
                $leave->user_name = $leave->user_name ?? 'N/A';

                $leave->formatted_date = \Carbon\Carbon::parse($leave->leave_date)->format('l, F j, Y');

                return $leave;

        });

        $emp_leave_setting = DB::table('leave_setting')->where('employee_id', $user_id)->where('financial_year', $fincYear)->first();


        $recent_latecoming_earlycoming = MailRequest::with('user')
        ->where('user_id', $user_id)
        ->where('status', 'pending')
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($request) {
            $request->display_date = \Carbon\Carbon::parse($request->request_date)
                ->format('l, F j, Y');
            return $request;
        });


        $all_latecoming_earlycoming = MailRequest::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
        $current_user_roll = auth()->user()->role;

        if($user->role == '2'){
            return view('dashboard.admindashboard')->with(compact('project_name', 'recent_emp_leave', 'all_latecoming_earlycoming', 'current_user_roll'));
        }
        else
        {
        return view('dashboard.employeedashboard')->with(compact('total_Pl', 'total_Sl', 'total_Cl','Week_office_hours','remaining_hour','enddate','break_check','assign_project', 'all_count_PL', 'all_count_SL', 'all_count_CL', 'all_count_PRE', 'remain_prev_yl', 'all_count_EXT_DAY', 'remain_extdays', 'all_leave', 'recent_emp_leave', 'recent_latecoming_earlycoming', 'emp_leave_setting', 'remainLeavePl','remainLeaveSl','remainLeaveCl','totalTakenLeave', 'remainYearLeave','countPYL','extra_day'));
        }   
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $today_date = date('Y-m-d');
        $previus_date = date('Y-m-d', strtotime('-7 days'));
        $get_projects = WorkReport::whereBetween('work_date', [$previus_date, $today_date])->where('project_id',$request->id)->where('work_time','!=',null)->orderBy('id','desc')->get();

        $newArray= array();
        foreach($get_projects as $project)
        {
            $user = User::where('id',$project->user_id)->first();
            $project['name'] = $user->name;
            $newArray[] = $project;
            
        }
        $projects = $newArray;
        return response()->json(['success' => $projects]);    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
