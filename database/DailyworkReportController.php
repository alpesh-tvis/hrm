<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Project;
use App\Models\Employee;
use App\Models\User;
use App\Models\WorkReport;
use App\Models\WorkreportList;
use App\Models\WeekHour;
use App\Models\ShiftSettings;
use App\Models\Leave;
use App\Models\Holiday;
use Carbon\Carbon;
use DB;
use DateTime;
use DateInterval;
use Log;
use Cache;
class DailyworkReportController extends Controller
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

    public function index(Request $request)
    {   
        $role = Auth::user()->role;
        $user_id = Auth::id();
        $workdate = date('Y-m-d');
        $current_hour = Carbon::now()->format('H');

        $user_list = Employee::with('user:id,role')
        ->whereHas('user', function ($query) {
            $query->where('role', 1)->where('id', '!=', Auth::id());
        })
        ->whereNull('service_enddate')
        ->orderBy('first_name')
        ->get(['id', 'first_name']);

        if($request->user_list){
            $user_id = $request->user_list;
        }

        if($request->report_date){
            $workdate = $request->report_date;
        }
        
        $projects = Project::whereNull("end_date")
        ->whereRaw("FIND_IN_SET(?, employee_id)", [$user_id])
        ->orderBy("project_name")
        ->get(["id", "project_name"]);
       
        if($current_hour < 4){
            $workdate = date('Y-m-d',strtotime("-1 days"));
        }
        // $work_report = WorkReport::with('project')
        // ->where('user_id', $user_id)
        // ->where('work_date', $workdate)
        // ->orderBy('id')
        // ->get(['id', 'work_type', 'activity_type', 'project_id', 'description', 'emp_ids', 'created_at', 'work_time', 'timer_id']);

        $work_report = WorkReport::where('user_id', $user_id)
        ->where('work_date', $workdate)
        ->orderBy('id')
        ->get(['id', 'work_type', 'activity_type', 'project_id', 'description', 'emp_ids', 'created_at', 'work_time', 'timer_id']);


        $get_sift = DB::table('workreports')
        ->where('user_id', $user_id)
        ->where('work_date', $workdate)
        ->orderByDesc('id')
        ->value('sift');

        /*** Office hours Calculation ***/
        
        $office_hours_start_query = DB::table('workreports')
        ->where('user_id', $user_id)
        ->where('work_date', $workdate)
        ->value('created_at');
        
        $checkout_query = DB::table('workreports')
            ->where([
                ['user_id', $user_id],
                ['work_date', $workdate],
                ['work_type', 4]
            ])
        ->first(['created_at']);

        if(!empty($get_sift) && $get_sift == 1){
            $checkout_query = DB::table('workreports')
            ->where([
                ['user_id', $user_id],
                ['work_date', $workdate],
                ['work_type', 6]
            ])
            ->first(['created_at']);
        }
        
        if($office_hours_start_query){
            
            if($checkout_query){
                $office_hours = WorkReport::where('work_date', $workdate)
                ->where('user_id', $user_id)
                ->whereNotNull('work_time')
                ->selectRaw("SEC_TO_TIME(SUM(TIME_TO_SEC(work_time))) as total_time")
                ->value('total_time');
            }else{
                $office_hours = Carbon::parse($office_hours_start_query)
                    ->diff(now())
                    ->format('%H:%I:%S');
            }
        }else{
            $office_hours = '00:00:00';
        }
        /*** End Office hours Calculation ***/

        /*** Working Hours Calculation ***/
       
        $working_hours_query = WorkReport::where([
            ['work_date', '=', $workdate],
            ['user_id', '=', $user_id],
            ['work_time', '!=', null],
            ['work_type', '!=', 3]
        ])->sum(DB::raw("TIME_TO_SEC(work_time)"));

        $get_latest_working_hours_query = WorkReport::where([
            ['work_date', '=', $workdate],
            ['user_id', '=', $user_id],
            ['work_time', '=', null],
            ['work_type', '!=', 3]
        ])->latest('id')
        ->first(['created_at']);

        /*** Priyal ***/
       
        $processed_reports = []; 
        $work_type_flag = '';
        $work_time_val = 'null_val';
        $endbtn = ''; 
        $time = '';
        $pre_date = '';
        $work_arr = ["","General Activity","Project Work","Break","End","","Shift 1 End"];             
        $activity_arr = ["","Mail Check","Event","Interview","Skill Improvement","Free"];
        $break_arr = ["","Lunch","Water","Washroom","Call","Sleep","Snack","Other"];
        $project_activity_arr = ["","Regular Work","Requirement Check","Client Message","Client Meeting","Discussion","R&D","Help"];

        foreach ($work_report as $key => $work) {
            
            $work_type = $work_arr[$work->work_type] ?? '';
            $work_type_flag = $work_type; 

            $work_time_val = (!empty($work->work_time)) ? $work->work_time : 'null_val';

            $activity_name = '';
            if ($work->work_type == 1) $activity_name = $activity_arr[$work->activity] ?? '';
            if ($work->work_type == 2) $activity_name = $project_activity_arr[$work->activity] ?? '';
            if ($work->work_type == 3) $activity_name = $break_arr[$work->activity] ?? '';

            $helped_person = '';

            if ($work->work_type == 2 && $work->activity_type == 7 && !empty($work->emp_ids)) {
                $emp_data = DB::table('employees')->where('id', $work->emp_ids)->first();
                $helped_person = $emp_data->first_name ?? '';
            }
            //dd($work->activity_type, $work->emp_ids);       
            //$project = DB::table('projects')->where('id', $work->project_id)->first();
            //dd($project);
            $project = DB::table('projects')->where('id', $work->project_id)->first();
            $pre_date = DB::table('workreports')->where('user_id',Auth::user()->id)->where('id','>',$work->id)->orderBy('id', 'asc')->first();
            $processed_reports[] = [
                'work_type' => $work_type,
                'activity'  => $activity_name,
                'work_time' => $work_time_val,
                'helped_person' => $helped_person,
                'project_name' => $project->project_name ?? '' 
            ];
        }
        /*** mentioned in compact***/
        if($get_latest_working_hours_query){
            $running_working_hours = Carbon::parse($get_latest_working_hours_query->created_at);
            if($checkout_query){
                $working_hours = gmdate("H:i:s", $working_hours_query);
            }else{
                
                $current_datetime1 = Carbon::now();
                $working_hours_diff  = $running_working_hours->diff($current_datetime1)->format('%H:%I:%S');
                $working_hours1 = gmdate("H:i:s", $working_hours_query);
                $total_working_hours_temp = strtotime($working_hours1) - strtotime("00:00:00");

                $get_latest_1_entry = WorkReport::where('work_date', $workdate)->where('user_id',$user_id)->orderBy('id','desc')->first();
                
                if($get_latest_1_entry && $get_latest_1_entry->work_type == 3){
                    $working_hours = gmdate("H:i:s", $working_hours_query);
                }else{

                    $get_latest_working_hours = WorkReport::where('work_date', $workdate)->where('user_id',$user_id)->where('work_type','!=',3)->orderBy('id','desc')->first();
                    
                    if($get_latest_working_hours && $get_latest_working_hours->work_time == null){
                        $working_hours = date("H:i:s", strtotime($working_hours_diff) + $total_working_hours_temp);
                    }else{
                        $working_hours = gmdate("H:i:s", $working_hours_query);
                    }
                }
            }
            
        }else{
            $working_hours = gmdate("H:i:s", $working_hours_query);
        }
        /*** End Working Hours Calculation ***/

        /*** Break Hours Calculation ***/
        $break_query = WorkReport::where('work_date', $workdate)->where('user_id',$user_id)->where('work_time','!=',null)->where('work_type',3)->sum(DB::raw("TIME_TO_SEC(work_time)"));

        $get_latest_break_hours_query = WorkReport::where('work_date', $workdate)->where('user_id',$user_id)->where('work_time',null)->where('work_type',3)->first();

        if($get_latest_break_hours_query){
            $running_break_hours = Carbon::parse($get_latest_break_hours_query->created_at);
            $current_datetime1 = Carbon::now();
            $break_hours_diff  = $running_break_hours->diff($current_datetime1)->format('%H:%I:%S');
            
            $break_hours1 = gmdate("H:i:s", $break_query);
            $total_break_hours = strtotime($break_hours1) - strtotime("00:00:00");
            $break_hours = date("H:i:s", strtotime($break_hours_diff) + $total_break_hours);
        }else{
            $break_hours = gmdate("H:i:s", $break_query);
        }
        /*** End Break Hours Calculation ***/

        /*** Total week Working Hours Calculation ***/
        $currentDateForWeek = Carbon::parse($workdate);

        $monday = $currentDateForWeek->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $today = $currentDateForWeek->copy()->endOfDay();

        $total_working_hours_query1 = WorkReport::where('user_id',$user_id)
            ->whereBetween('work_date',[$monday, $today])
            ->whereNotNull('work_time')
            ->where('work_time','!=','')
            ->whereNotIn('work_type',[3,4])
            ->get();

        $all_seconds = 0;

        foreach ($total_working_hours_query1 as $time) {

            if (substr_count($time->work_time, ':') !== 2) continue;

            list($hour, $minute, $second) = explode(':', $time->work_time);

            $all_seconds += ((int)$hour * 3600);
            $all_seconds += ((int)$minute * 60);
            $all_seconds += (int)$second;
        }

        /** Add current running activity time **/
        $current_running = WorkReport::where('user_id', $user_id)
            ->whereBetween('work_date', [$monday, $today])
            ->whereNull('work_time')
            ->whereNotIn('work_type', [3,4])
            ->latest('id')
            ->first();

        if ($current_running) {
            $start = Carbon::parse($current_running->created_at);
            $now = Carbon::now();

            $all_seconds += $start->diffInSeconds($now);
        }
        /** End addition **/

        $total_working_hours = sprintf(
            '%02d:%02d:%02d',
            floor($all_seconds / 3600),
            floor(($all_seconds % 3600) / 60),
            $all_seconds % 60
        );

        //DD($total_working_hours_query1);
        $selected_date = $request->report_date ? Carbon::parse($request->report_date) : null;
        if($selected_date){
            $selectedmonday = $selected_date->copy()->startOfWeek(Carbon::MONDAY)->toDateString();

            $weekly_hours = WeekHour::where('user_id', $user_id)
                                ->whereDate('week_start_date', $selectedmonday) 
                                ->value('working_hours');   

            if ($weekly_hours) {
                $total_working_hours = $weekly_hours;
            }             
        }
        /*** End Total week Working Hours Calculation ***/

        /* Remaining Hours calculate */
        $Week_working_hours_query = WeekHour::where('user_id',$user_id)->orderBy('id','desc')->first();

        $Week_working_hours = $Week_working_hours_query->next_week_hours ?? "40:00:00";

        $total_week_working_hour = $Week_working_hours;
        $week_working_hour = $total_working_hours ?? "00:00:00";

        list($h1, $m1, $s1) = explode(':', $total_week_working_hour);
        list($h2, $m2, $s2) = explode(':', $week_working_hour);

        $remaining_seconds = ($h1*3600+$m1*60+$s1) - ($h2*3600+$m2*60+$s2);

        $remaining_hour = $remaining_seconds < 0 ? "00:00:00" : gmdate("H:i:s", $remaining_seconds);
        /* End Remaining Hours calculate */

        $enddate = DB::table('workreports')->where('user_id',$user_id)->where('work_date',$workdate)->where('work_type',4)->first();

        $get_latest_record = DB::table('workreports')->where('user_id',$user_id)->where('work_date',$workdate)->orderBy('id','desc')->first();

        $sift_type_get = DB::table('employees')->where('id',$user_id)->first();
        $sift_type = $sift_type_get->sift_type ?? 1;

        if($sift_type == 2){
            $sift_end_check = DB::table('workreports')->where('user_id',$user_id)->where('work_date',$workdate)->where('sift','!=',null)->first();

            if($sift_end_check){
                $sift = ($sift_end_check->sift == 1) ? 2 : 1;
            }else{
                $sift = 1;
            }
        } else {
            $sift = 'regular';
        }

        $week_startDate = now()->startOfWeek()->format('Y-m-d'); 
        $week_endDate = now()->endOfWeek()->format('Y-m-d');

        $sift_count = WorkReport::where('user_id',$user_id)
            ->whereBetween('work_date', [$week_startDate, $week_endDate])
            ->where('sift','!=',null)
            ->count();
       
        return view('dailyworkreport.list')->with(compact(
            'projects','work_report','user_list','office_hours','working_hours',
            'break_hours','enddate','total_working_hours','role',
            'remaining_hour','workdate','sift','get_latest_record',
            'sift_count','processed_reports', 'work_type_flag', 'work_time_val','endbtn' ,'time', 'pre_date'
        ));
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

        $userId = Auth::id();

        $request->user_id = $userId;
        $request->work_date = date('Y-m-d');
        $work_date = date('Y-m-d');
        $current_hour  =  date('H');
        $project_id = null;
        $activity_type = null;

        /* No value has been selected from the options 'General activity,' 'project work,' or 'break'. validation */
        if($request->work_type){

            if($request->work_type == ''){
                $request->validate(['work_type' => 'required']);
            }

            /* type 'General activity' */
            if($request->work_type == '1'){
                if($request->activity_type == ''){
                    $request->validate(['activity_type' => 'required']);
                }
            }

            /* type 'project work' */
            if($request->work_type == '2'){
                if($request->project_work == ''){
                    $request->validate(['project_work' => 'required']);
                }
                if($request->project_work !=''){
                    if($request->activity_type1 == ''){
                        $request->validate(['activity_type' => 'required']);
                    }
                    if($request->activity_type1 == '7'){
                        if($request->emp_ids == ''){
                            $request->validate(['emp_ids' => 'required'],
                            [
                            'emp_ids.required' => 'Please Select user'
                            ]);
                        }
                    }
                    if($request->has('timer_id')){
                        if($request->timer_id == ''){
                            $request->validate(['timer_id' => 'required'],
                                ['timer_id.required' => 'Please Select Timer']);
                        }    
                    }
                }
                $project_id = $request->project_work; 
                $activity_type = $request->activity_type1;
            }

            /* type 'break' */
            if($request->work_type == '3'){
                if($request->activity_type == ''){
                    $request->validate(['activity_type' => 'required']);
                }
            }
        }
        /* End validation */
        
        if($current_hour < 4){
            $work_date = date('Y-m-d',strtotime("-1 days"));
            $request->request->add(['created_at' => date('Y-m-d H:i:s')]);
            $request->request->add(['work_date' => $work_date]);
        }
        
        /* Sales Person not start when first shift */
        $current_time = date('H:i:s', time());
        $dayName = strtolower(Carbon::parse($work_date)->format('D'));
        $check_first_shift = ShiftSettings::where('employee_name', $userId)->where('shift', 1)->value($dayName);
        
        if($check_first_shift){
            $finalTime = Carbon::createFromTimeString($check_first_shift)->addHours(4)->toTimeString();
            $work_reportget = WorkReport::where('user_id',$userId)->where('work_date',$work_date)->doesntExist();

            if($work_reportget){
                if ($current_time > $finalTime) {

                    $insert_Work_report = WorkReport::create([
                        "user_id" => $userId,
                        "work_type" => '2',
                        "activity_type" => '1',
                        "project_id" => 'o1',
                        "description" => 'First Shift Leave',
                        "work_time" => '04:00:00',
                        "work_date" => $work_date
                    ]);

                    $insert_Work_report2 = WorkReport::create([
                        "user_id" => $userId,
                        "work_type" => '6',
                        "work_date" => $work_date,
                        "sift" => '1'
                    ]);

                    $leave_exists1 = Leave::where('user_id', $userId)->whereDate('leave_date', $work_date)->where('leave_status','FH')->exists();

                    if(!$leave_exists1){
                        Leave::create([
                            'user_id' => $userId,
                            'leave_date' => $work_date,
                            'leave_status' => 'FH',
                            'leave_reason' => "First Shift Leave",
                            'leave_type' => 'CL',
                            'status' => 'Approved'
                        ]);
                    }
                    $start_time = date('Y-m-d H:i:s');
                    WorkreportList::create([
                        'start_time'=>$start_time,
                        'work_date'=> $work_date,
                        'user_id'=>$userId,
                        'working_hours' => '00:00:00'
                    ]);
                    dd($start_time); 
                }
            }
        }    
        /* End Sales Person not start when first shift */

        if($request->work_type == '5'){
            $get_workreport = WorkReport::where('user_id',$userId)->orderBy('id', 'desc')->skip(1)->first();
            
            $insert_report = WorkReport::create([
                "user_id" => $get_workreport->user_id,
                "work_type" => $get_workreport->work_type,
                "activity_type" => $get_workreport->activity_type,
                "project_id" => $get_workreport->project_id,
                "description" => $get_workreport->description,
                "emp_ids" => $get_workreport->emp_ids,
                "timer_id" => $get_workreport->timer_id, 
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
                "work_date" => $get_workreport->work_date
            ]);
        }else if(!empty($request->start_activity )){
            $start_id = $request->start_activity;

            
        $get_workreport = WorkReport::where('user_id', $userId)
            ->where('id', $request->start_activity)
            ->first();

        $timer_id = $get_workreport->timer_id;

        $insert_report = WorkReport::create([
            "user_id" => $get_workreport->user_id,
            "work_type" => $get_workreport->work_type,
            "activity_type" => $get_workreport->activity_type,
            "project_id" => $get_workreport->project_id,
            "description" => $get_workreport->description,
            "emp_ids" => $get_workreport->emp_ids,
            "timer_id" => $timer_id, // exact copy
            "work_date" => $get_workreport->work_date
        ]);

        }else if($request->work_type == '6'){

            // Sales person sift
            $sift_end_check = DB::table('workreports')->where('user_id',$userId)->where('work_date',$work_date)->where('sift','!=',null)->first();

            if($sift_end_check){ //value not null
                if($sift_end_check->sift == 1){
                    $sift = 2;
                    $work_type = 4;
                }
            }else{
                $sift = 1;
                $work_type = $request->work_type;
            }
            

            $insert_report = WorkReport::create([
                "user_id" => $userId,
                "work_type" => $work_type,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
                "work_date" => $work_date,
                "sift" => $sift

            ]);
        } else if($request->day_on_again){
            $day_on = $request->day_on_again;
            
            WorkReport::where('id',$day_on)->update(
                [
                    "work_type" => "3",
                    "activity_type" => "7",
                    "description" => "Re Open Day On"
                ]
            );
        }
        else{

            $insert_report = WorkReport::create($request->all()+ ['user_id'=>$userId,'work_date'=>$request->work_date,'project_id'=>$project_id,'activity_type'=>$activity_type]);
            
        }
        
        try {
            if($insert_report){

                $today_date = date('Y-m-d');
                $get_last_record_count = WorkReport::where('user_id',$userId)->whereDate('work_date',$work_date)->latest()->count();
                
                if($get_last_record_count > 1)
                {
                    $get_last_record = WorkReport::where('user_id',$userId)->whereDate('work_date',$work_date)->latest()->first();
                    
                    $pre_date = WorkReport::where('user_id',$userId)->whereDate('work_date',$work_date)->where('id','<',$get_last_record->id)->orderBy('id', 'desc')->first();
                    $start = $get_last_record->created_at;
                    $end   = $pre_date->created_at;
                    $time  = $start->diff($end)->format('%H:%I:%S');
                    
                    if(empty($get_last_record_count->sift)){
                        if($pre_date->work_time == null){
                            if($pre_date->sift!='1'){
                                WorkReport::where('id',$pre_date->id)->update(['work_time'=>$time]);
                            }
                        }    
                    }

                }
            }
        } catch (\Exception $e) {
            Log::error('Insert failed', ['exception' => $e]);
            return redirect()->back()->withInput();
        }
            
        /*Add data in Work Report List*/
        $start_time = date('Y-m-d H:i:s');
        $get_WorkreportList = WorkreportList::where('user_id',$userId)->where('work_date',$work_date)->exists();
       
        if($get_WorkreportList == false){
            WorkreportList::create([
                'start_time'=>$start_time,
                'work_date'=> $work_date,
                'user_id'=>$userId,
                'working_hours' => '00:00:00'
            ]);
        }
        /*End data in WorkreportList*/

        if($get_WorkreportList == true){
            if($request->work_type == '4' || $request->work_type == '6'){
                
                $total_time = WorkReport::where('user_id',$userId)->where('work_date',$work_date)->where('work_type','!=',3)->sum(DB::raw("TIME_TO_SEC(work_time)"));
                $time = gmdate("H:i:s", $total_time);
                    
                /* Half day full day count */
                    
                $WorkingTime = Carbon::createFromFormat('H:i:s', $time);
                $WorkingHour = $WorkingTime->hour;

                $leave_exists = Leave::where('user_id', $userId)->whereDate('leave_date', $work_date)->exists();
                
                if($request->work_type == '4'){    
                    if($WorkingHour < 4){
                        $leave_status = "F";
                        $workreport_description = "Your working hours is less than 4 hours";
                        $workreport_time = "08:00:00";
                    }
                
                    if($WorkingHour > 3 && $WorkingHour < 6 ){
                       $leave_status = "SH";
                       $workreport_description = "Your working hours is between 4 to 6 hours";
                       $workreport_time = "04:00:00"; 
                    }
                
                    if($WorkingHour < 6){    
                        WorkReport::create([
                            "user_id" => $userId,
                            "work_type" => "2",
                            "activity_type" => "1",
                            "project_id" => "o1",
                            "description" => $workreport_description,
                            "work_date" => $work_date,
                            "work_time" => $workreport_time,
                        ]);

                        if(!$leave_exists){
                            if($userId != '15'){
                                Leave::create([
                                    'user_id' => $userId,
                                    'leave_date' => $work_date,
                                    'leave_status' => $leave_status,
                                    'leave_reason' => "Your Working Hours is ".$time,
                                    'leave_type' => 'CL',
                                    'status' => 'Approved'
                                ]);
                            }    
                        }
                    }
                }

                if($request->work_type == '6'){
                    if($WorkingHour < 2){
                        $leave_status = "FH";
                        $workreport_description = "Your working hours is less than 2 hours";
                        $workreport_time = "04:00:00";

                        WorkReport::create([
                            "user_id" => $userId,
                            "work_type" => "2",
                            "activity_type" => "1",
                            "project_id" => "o1",
                            "description" => $workreport_description,
                            "work_date" => $work_date,
                            "work_time" => $workreport_time,
                        ]);

                        if(!$leave_exists){
                            Leave::create([
                                'user_id' => $userId,
                                'leave_date' => $work_date,
                                'leave_status' => $leave_status,
                                'leave_reason' => "Your Working Hours is ".$time,
                                'leave_type' => 'CL',
                                'status' => 'Approved'
                            ]);
                        }
                    }
                }    
              
                /* End Half day full day count */
                $total_time = WorkReport::where('user_id',$userId)->where('work_date',$work_date)->where('work_type','!=',3)->sum(DB::raw("TIME_TO_SEC(work_time)"));
                $time = gmdate("H:i:s", $total_time);
                WorkreportList::where('user_id',$userId)
                    ->where('work_date',$work_date)
                    ->update(
                    [
                        'end_time'=>$start_time,
                        'working_hours'=>$time
                    ]);
            }
        }

        /* Add data in week_hours table */
        if($insert_report){
            $now = Carbon::now();
            $week_startDate = now()->startOfWeek()->format('Y-m-d'); 
            $week_endDate = now()->endOfWeek()->format('Y-m-d');

            $exists_WeekHour = WeekHour::where('user_id', $userId)
                ->where('week_start_date', $week_startDate)
                ->where('week_end_date', $week_endDate)
                ->exists();

            $total_hours = WeekHour::where('user_id',$userId)->where('week_start_date',$week_startDate)->where('week_end_date',$week_endDate)->first();    

            $records = WorkreportList::where('user_id',$userId)->whereBetween('work_date', [$week_startDate, $week_endDate])->get();

            if(!$exists_WeekHour){
                $insert_record = WeekHour::create([
                    "total_hours" => '40:00:00',
                    "week_start_date" => $week_startDate,
                    "week_end_date" => $week_endDate,
                    "user_id" => $userId,
                    "entry_type" => 'w'
                ]);
            }
            else
            {
                if($request->work_type == '4' || $request->work_type == '6'){
                    
                    $totalHours = 0;
                    $totalMinutes = 0;
                    $totalSeconds = 0;

                    foreach ($records as $record) {
                        list($hours, $minutes, $seconds) = explode(':', $record->working_hours);
                        $totalHours += $hours;
                        $totalMinutes += $minutes;
                        $totalSeconds += $seconds;
                    }

                    $totalMinutes += floor($totalSeconds / 60);
                    $totalSeconds %= 60;
                    $totalHours += floor($totalMinutes / 60);
                    $totalMinutes %= 60;

                    $totalTimeFormatted = sprintf('%02d:%02d:%02d', $totalHours, $totalMinutes, $totalSeconds);
                    
                    $Week_working_hours_query = WeekHour::where('user_id',$userId)->orderBy('id','desc')->skip(1)->first();
                
                    if(!empty($Week_working_hours_query->next_week_hours)){
                        $var1 = $Week_working_hours_query->next_week_hours;
                    }else{
                        $var1 = $total_hours->total_hours;
                    }
                
                    $var2 = $totalTimeFormatted;
                    
                    list($hours1, $minutes1, $seconds1) = explode(':', $var1);
                    list($hours2, $minutes2, $seconds2) = explode(':', $var2);

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

                    $result = ($negative ? '' : '-') . $resultHours . ':' . $resultMinutes . ':' . $resultSeconds;

                    // if($request->work_type == '4'){
                    /* Week Hour Update */
                    WeekHour::where('user_id', $userId)->where('week_start_date', $week_startDate)->where('week_end_date', $week_endDate)->update(['remaining_hours'=>$result,'working_hours'=>$var2,'entry_type'=>'w']);
                }    
            }
        }
        /* Add data in week_hours table End */  
        
        if($request->work_type == '3'){
            return redirect()->route('daily_work_report.index')->with('success', 'Break Started');
        }
        else if($request->work_type == '4'){
            return redirect()->route('daily_work_report.index')->with('success', 'Day end successfully');
        }else{
            return redirect()->route('daily_work_report.index')->with('success', 'New Activity Started');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(request()->ajax()) {

            $projects = Project::find($id);

            if($projects){
                if($projects->timer_id){
                    $ex_val = explode(',',$projects->timer_id);
                    $timerValues = [];
                    
                    $select = "<select class='form-control' name='timer_id'>";
                    $select .= "<option value='' >--- select option --</option>";

                    foreach($ex_val as $timer){
                        $check_timer_assign = WorkReport::where('timer_id',$timer)->where('work_time',null)->first();    
                        
                        $Employee = Employee::find($timer);
                        
                        $disabled = (!empty($check_timer_assign) && $check_timer_assign->timer_id == $timer) ? 'disabled' : '';
                        $disabled_class = (!empty($check_timer_assign) && $check_timer_assign->timer_id == $timer) ? 'not_available' : '';
                        if($disabled_class == 'not_available'){
                            $bg_color = 'red';
                            $color = 'white';
                        }else{
                            $bg_color = 'inherit';
                            $color = 'inherit';
                        }
                        $not_available = (!empty($check_timer_assign) && $check_timer_assign->timer_id == $timer) ? '(Not Available)' : '';
                        
                        $select .= "<option  style ='background-color:$bg_color;color:$color;' class='".$disabled_class."' value='".$timer."' $disabled>".$Employee->first_name. $not_available. "</option>";
                    }
                    
                    $select .= "<option style='color: #149aa3;'  value='wt' >Without Timer</option>";
                    $select .= "</select>";
                    return response()->json($select);
                }
            }    
        }
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
        $request->validate([
        'description' => 'required|string|max:255', 
        ], [
            'description.required' => 'The description field is required.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description may not be greater than 255 characters.',
        ]);
        $dailyWorkReport = WorkReport::find($id);
        $dailyWorkReport->description = $request->description;
        $dailyWorkReport->save();
        
        $project_id = $dailyWorkReport->project_id;
        $projectDetails = DB::table('projects')->where('id', $project_id)->first();
        
        if ($projectDetails) {
            $project_name = $projectDetails->project_name;
        } else {
            $project_name = 'Other Activity';
        }
       
       return redirect()->route('daily_work_report.index')->with('success', $project_name . ' Daily Work Report updated successfully.');
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
    public function work_report(){
        $role = Auth::user()->role;
        $table = WorkReport::groupBy('created_at')->pluck('created_at')->toArray();

        $work_report = WorkReport::where('work_date',date('Y-m-d'))->where('work_time',null)->orderBy('id','asc')->get();
        if($role == '2'){
        }
        return view('dailyworkreport.work_report')->with(compact('work_report'));
        
    }
    
    
}
