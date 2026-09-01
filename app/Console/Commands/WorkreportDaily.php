<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\WorkreportList;
use App\Models\WorkReport;
use App\Models\User;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Holiday;
use App\Models\WeekHour;
use App\Models\ShiftSettings;
use Carbon\Carbon;

class WorkreportDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'work:report-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily work report';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info('Daily work report generated and sent successfully.');
        $todayDate = date('Y-m-d');
        // $todayDate = "2024-07-29";
        $startTime = '10:00:00';
        $endTime = '06:00:00';
        
        $inactive_user = Employee::select('id')->where('service_enddate','!=',null)->get();
        // $user_list1 = User::where('role', '1')->whereNotIn('id', $inactive_user)->get()->pluck('id')->toArray();
        $user_list1 = User::where('id','!=','15')->where('role', '1')->whereNotIn('id', $inactive_user)->get()->pluck('id')->toArray();
        $WorkreportList1 = WorkreportList::where('work_date', $todayDate)->get()->pluck('user_id')->toArray();

        $start_datetime = $todayDate . ' ' . $startTime;
        $end_datetime = $todayDate . ' ' . $endTime;
        $result = array_diff($user_list1, $WorkreportList1);

        /* Start New code 13-06-2024 */
            
        $dayName = strtolower(Carbon::parse($todayDate)->format('D'));
            
        $get_sales_persons = ShiftSettings::where($dayName,'!=',null)->groupBy('employee_name')->orderBy('employee_name', 'desc')->pluck('employee_name')->toArray();

        if($get_sales_persons){
            foreach($get_sales_persons as $person){
               
                    
   
                $check_second_shift = ShiftSettings::select($dayName,'id','employee_name')->where('employee_name', $person)->where('shift', 2)->first();
                    
                    

                if($check_second_shift->$dayName != null){

                    $check_second_shift_workreport = WorkReport::where('work_date',$todayDate)->where('user_id',$check_second_shift->employee_name)->where('sift',1)->first();

                    if ($check_second_shift_workreport) {

                        $before_workreport = WorkReport::where('work_date', $todayDate)->where('user_id', $check_second_shift_workreport->user_id)->where('id', '>', $check_second_shift_workreport->id)->orderBy('id', 'desc')->first();
                        if($before_workreport == null && !in_array($person, $result)){
                           $result[] = intval($check_second_shift_workreport->user_id);
                        }
                        /* New code 19-07-2024 */
                        if($before_workreport)
                        {
                            if($before_workreport->work_type == 2 && $before_workreport->project_id == 'o1' && $before_workreport->work_time!= '' && !in_array($person, $result))
                            {
                                $result[] = intval($before_workreport->user_id);
                            }    
                        }
                        /* End New code 19-07-2024 */  
                           
                    } 
                }
            }

        }
        
        /* End New code 13-06-2024 */   

        foreach($result as $res){
            
           $employee = Employee::find($res);

            $isHourlyEmployee = $employee && $employee->role == 1 && $employee->sift_type == 3;

             $user = $isHourlyEmployee ? User::find($res) : null;

             if($isHourlyEmployee && $user){

                $userTotalHours      = $user->total_hours;
                $minFullDayHour      = (float) $user->min_full_day_hour;
                $minHalfDayHour      = (float) $user->min_half_day_hour;
                $maxCarryForward     = (float) $user->max_carry_forward;

                $wholeHours = floor($userTotalHours);
                $minutes = round(($userTotalHours - $wholeHours) * 60);

                if($minutes >= 60){
                    $wholeHours++;
                    $minutes = 0;
                }

                $userTotalHoursTime = sprintf('%02d:%02d:00', $wholeHours, $minutes);

            }
            /* Start New code 13-06-2024 */  
            $exists_entry_day = WorkReport::where('work_date', $todayDate)->where('user_id', $res)->count();

            $check_first_shift = ShiftSettings::select($dayName,'id','employee_name')->where('employee_name', $res)->where('shift', 1)->first();

            $check_second_shift = ShiftSettings::select($dayName,'id','employee_name')->where('employee_name', $res)->where('shift', 2)->first();

            $check_first_shift_workreport = WorkReport::where('work_date',$todayDate)->where('user_id',$res)->where('sift',1)->first();

            $check_second_shift_workreport = WorkReport::where('work_date',$todayDate)->where('user_id',$res)->where('sift',1)->first();


            if($check_second_shift_workreport){
                $before_workreport = WorkReport::where('work_date', $todayDate)->where('user_id', $res)->where('id', '>', $check_second_shift_workreport->id)->orderBy('id', 'desc')->first();
            }else{
                $before_workreport = "none above";
            }

            $holiday_exists = Holiday::whereDate("holiday_date", $todayDate)->exists();
            // Full Day on leave
            if($exists_entry_day < 1){

                WorkReport::create([
                    "user_id" => $res,
                    "work_type" => "2",
                    "activity_type" => "1",
                    "project_id" => "o1",
                    "description" => "on leave",
                    "work_date" => $todayDate,
                    //"work_time" => "08:00:00"
                    "work_time" => $isHourlyEmployee
                        ? sprintf('%02d:%02d:00', floor($minFullDayHour), round(($minFullDayHour - floor($minFullDayHour)) * 60))
                        : "08:00:00"
                ]);


                WorkreportList::create([
                    "start_time" => $start_datetime,
                    "end_time" => $end_datetime,
                    //"working_hours" => "08:00:00",
                    "working_hours" => $isHourlyEmployee
                        ? sprintf('%02d:%02d:00', floor($minFullDayHour), round(($minFullDayHour - floor($minFullDayHour)) * 60))
                        : "08:00:00",
                    "user_id" => $res,
                    "work_date" => $todayDate
                ]);

                $leave_exists = Leave::where("user_id", $res)->whereDate("leave_date", $todayDate)->exists();

                

                if(!$leave_exists){
                    
                    if(!$holiday_exists){
                        Leave::create([
                            "user_id" => $res,
                            "leave_date" => $todayDate,
                            "leave_status" => "F",
                            "leave_reason" => "on leave",
                            "leave_type" => "CL",
                            "status" => "Approved"
                        ]);
                    }    
                }
                else
                {
                    $full_day_leave_exists = Leave::where("user_id", $res)
                    ->whereDate("leave_date", $todayDate)
                    ->where('leave_status', "F")
                    ->exists();
                    if (!$full_day_leave_exists) {
                        $First_half_leave_exists = Leave::where("user_id", $res)->whereDate("leave_date", $todayDate)->where('leave_status',"FH")->exists();
                        $second_half_leave_exists = Leave::where("user_id", $res)->whereDate("leave_date", $todayDate)->where('leave_status',"SH")->exists();
                        
                        if(!$First_half_leave_exists){
                            if(!$holiday_exists){
                                Leave::create([
                                    "user_id" => $res,
                                    "leave_date" => $todayDate,
                                    "leave_status" => "FH",
                                    "leave_reason" => "First Half",
                                    "leave_type" => "CL",
                                    "status" => "Approved"
                                ]);
                            }    
                        }
                        if(!$second_half_leave_exists){
                            if(!$holiday_exists){
                                Leave::create([
                                    "user_id" => $res,
                                    "leave_date" => $todayDate,
                                    "leave_status" => "SH",
                                    "leave_reason" => "Second Half",
                                    "leave_type" => "CL",
                                    "status" => "Approved"
                                ]);
                            }    
                        }
                    }  
                }
            }
            elseif($check_first_shift->$dayName != null && $check_first_shift_workreport == null){ //First Shift leave
                
                if($check_first_shift_workreport == null ){


                    $insert_Work_report = WorkReport::create([
                        "user_id" => $res,
                        "work_type" => '2',
                        "activity_type" => '1',
                        "project_id" => 'o1',
                        "description" => 'First Shift Leave',
                        //"work_time" => '04:00:00',
                        "work_time" => $isHourlyEmployee
                        ? sprintf('%02d:%02d:00', floor($minHalfDayHour), round(($minHalfDayHour - floor($minHalfDayHour)) * 60))
                        : '04:00:00',
                        "work_date" => $todayDate
                    ]);

                    $insert_Work_report2 = WorkReport::create([
                        "user_id" => $res,
                        "work_type" => '6',
                        "work_date" => $todayDate,
                        "sift" => '1'
                    ]);

                    $leave_exists1 = Leave::where('user_id', $res)->whereDate('leave_date', $todayDate)->where('leave_status','FH')->exists();

                    if(!$leave_exists1){
                        if(!$holiday_exists){
                            Leave::create([
                                'user_id' => $res,
                                'leave_date' => $todayDate,
                                'leave_status' => 'FH',
                                'leave_reason' => "First Shift Leave",
                                'leave_type' => 'CL',
                                'status' => 'Approved'
                            ]);
                        }    
                    }

                    $start_time = date('Y-m-d H:i:s');
                    
                }    
            }
            elseif($check_second_shift->$dayName != null && $before_workreport == null){ //Second half leave
                
                if ($check_second_shift_workreport){
                    
                    if($before_workreport == null ){
                        $insert_Work_report = WorkReport::create([
                            "user_id" => $res,
                            "work_type" => '2',
                            "activity_type" => '1',
                            "project_id" => 'o1',
                            "description" => 'Second Shift Leave',
                            //"work_time" => '04:00:00',
                            "work_time" => $isHourlyEmployee
                                ? sprintf('%02d:%02d:00', floor($minHalfDayHour), round(($minHalfDayHour - floor($minHalfDayHour)) * 60))
                                : '04:00:00',
                            "work_date" => $todayDate
                        ]);
                        $insert_Work_report2 = WorkReport::create([
                            "user_id" => $res,
                            "work_type" => '4',
                            "work_date" => $todayDate,
                            "sift" => '2'
                        ]);
                        $leave_exists1 = Leave::where('user_id', $res)->whereDate('leave_date', $todayDate)->where('leave_status','SH')->exists();
                        if(!$leave_exists1){
                            if(!$holiday_exists){
                                Leave::create([
                                    'user_id' => $res,
                                    'leave_date' => $todayDate,
                                    'leave_status' => 'SH',
                                    'leave_reason' => "Second Shift Leave",
                                    'leave_type' => 'CL',
                                    'status' => 'Approved'
                                ]);
                            }    
                        }
                        $start_time = date('Y-m-d H:i:s');
                        
                    }
                } 
            }
            /* New code 19-07-2024 */
            elseif($check_second_shift->$dayName != null && $before_workreport){ //Second half leave
                if ($check_second_shift_workreport){
                    if($before_workreport->work_time !=''){
                        $insert_Work_report = WorkReport::create([
                            "user_id" => $res,
                            "work_type" => '2',
                            "activity_type" => '1',
                            "project_id" => 'o1',
                            "description" => 'Second Shift Leave',
                            //"work_time" => '04:00:00',
                            "work_time" => $isHourlyEmployee
                            ? sprintf('%02d:%02d:00', floor($minHalfDayHour), round(($minHalfDayHour - floor($minHalfDayHour)) * 60))
                            : '04:00:00',
                            "work_date" => $todayDate
                        ]);
                        $insert_Work_report2 = WorkReport::create([
                            "user_id" => $res,
                            "work_type" => '4',
                            "work_date" => $todayDate,
                            "sift" => '2'
                        ]);
                        $leave_exists1 = Leave::where('user_id', $res)->whereDate('leave_date', $todayDate)->where('leave_status','SH')->exists();
                        if(!$leave_exists1){
                            if(!$holiday_exists){
                                Leave::create([
                                    'user_id' => $res,
                                    'leave_date' => $todayDate,
                                    'leave_status' => 'SH',
                                    'leave_reason' => "Second Shift Leave",
                                    'leave_type' => 'CL',
                                    'status' => 'Approved'
                                ]);
                            }    
                        }
                        $start_time = date('Y-m-d H:i:s');
                        
                    }
                } 
            }
            /* End New code 19-07-2024 */

            /* End New code 13-06-2024 */   


            /*Add Hours in weekhours table*/
            $now = Carbon::now();
            $week_startDate = now()->startOfWeek()->format('Y-m-d'); 
            // $week_startDate = "2024-06-17";
            $week_endDate = now()->endOfWeek()->format('Y-m-d');
            // $week_endDate = "2024-06-23";

            $exists_WeekHour = WeekHour::where('user_id', $res)
                ->where('week_start_date', $week_startDate)
                ->where('week_end_date', $week_endDate)
                ->exists();

                
            //$total_hours = WeekHour::where('user_id',$res)->first();   
            $total_hours = WeekHour::where('user_id',$res)
                                    ->where('week_start_date',$week_startDate)
                                    ->where('week_end_date',$week_endDate)
                                    ->first();
            // $prevWeekHour = WeekHour::where('user_id',$res)->orderBy('id', 'desc')->skip(1)->take(1)->first();

            // if ($isHourlyEmployee) {
            //     $week_total_hour = $employee->total_working_hours ?? "40:00:00";
            // } else {
            //     if (!empty($prevWeekHour?->next_week_hours)) {
            //         $week_total_hour = $prevWeekHour->next_week_hours;
            //     } else {
            //         $week_total_hour = "40:00:00";
            //     }
            // }else{
            //     $week_total_hour = $prevWeekHour->next_week_hours;
            // }
            $prevWeekHour = WeekHour::where('user_id',$res)
                    ->orderBy('id','desc')
                    ->skip(1)
                    ->first();

            // if($isHourlyEmployee){

            //     $week_total_hour = $total_hours->total_hours ?? "40:00:00";

            // }else{

            //     if(!empty($prevWeekHour?->next_week_hours)){
            //         $week_total_hour = $prevWeekHour->next_week_hours;
            //     }else{
            //         $week_total_hour = "40:00:00";
            //     }

            // }

            if($isHourlyEmployee){

                //$week_total_hour = $userTotalHours; 
                $week_total_hour = $userTotalHoursTime;

            }else{

                if(!empty($prevWeekHour?->next_week_hours)){
                    $week_total_hour = $prevWeekHour->next_week_hours;
                }else{
                    $week_total_hour = "40:00:00";
                }

            }
            
            $records = WorkreportList::where('user_id',$res)->whereBetween('work_date', [$week_startDate, $week_endDate])->get();


            // if(!$exists_WeekHour){
            //     if($isHourlyEmployee){

            //     $hourly_setting = WeekHour::where('user_id',$res)
            //         ->orderBy('id','desc')
            //         ->first();

            //     WeekHour::create([
            //         "total_hours"       => $hourly_setting->total_hours ?? "40:00:00",
            //         "working_hours"     => "00:00:00",
            //         "remaining_hours"   => "00:00:00",
            //         "min_full_day_hour" => $hourly_setting->min_full_day_hour ?? 8,
            //         "min_half_day_hour" => $hourly_setting->min_half_day_hour ?? 4,
            //         "max_carry_forward" => $hourly_setting->max_carry_forward ?? 2,
            //         "week_start_date"   => $week_startDate,
            //         "week_end_date"     => $week_endDate,
            //         "user_id"           => $res,
            //         "entry_type"        => "c"
            //     ]);

            // }else{

            //         WeekHour::create([
            //             "total_hours"     => "40:00:00",
            //             "week_start_date" => $week_startDate,
            //             "week_end_date"   => $week_endDate,
            //             "user_id"         => $res,
            //             "entry_type"      => "c"
            //         ]);

            //     }

            // }
            if(!$exists_WeekHour){

                if($isHourlyEmployee){

                    WeekHour::create([
                        "total_hours"       => $userTotalHoursTime,
                        "working_hours"     => "00:00:00",
                        "remaining_hours"   => "00:00:00",
                        "min_full_day_hour" => $minFullDayHour,
                        "min_half_day_hour" => $minHalfDayHour,
                        "max_carry_forward" => $maxCarryForward,
                        "week_start_date"   => $week_startDate,
                        "week_end_date"     => $week_endDate,
                        "user_id"           => $res,
                        "entry_type"        => "c"
                    ]);

                }else{

                    // BAKI NON-HOURLY EMPLOYEES KA CODE SAME
                    WeekHour::create([
                        "total_hours"     => "40:00:00",
                        "week_start_date" => $week_startDate,
                        "week_end_date"   => $week_endDate,
                        "user_id"         => $res,
                        "entry_type"      => "c"
                    ]);

                }
            }
            else
            {

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
                
                $var1 = $week_total_hour;
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
                if($isHourlyEmployee){

                    //$maxCarryForward = $user->max_carry_forward ?? 2;
                    $maxCarryForward = $maxCarryForward ?? 2;
                    // Admin ne hours me value dali hai
                    $maxCarrySeconds = $maxCarryForward * 3600;

                    $currentRemainingSeconds = ($resultHours * 3600) 
                        + ($resultMinutes * 60) 
                        + $resultSeconds;


                    if(!$negative && $currentRemainingSeconds > $maxCarrySeconds){

                        $currentRemainingSeconds = $maxCarrySeconds;

                        $resultHours = floor($currentRemainingSeconds / 3600);
                        $currentRemainingSeconds %= 3600;

                        $resultMinutes = floor($currentRemainingSeconds / 60);
                        $resultSeconds = $currentRemainingSeconds % 60;


                        $result = ($negative ? '' : '-') 
                            . sprintf('%02d:%02d:%02d', 
                                $resultHours,
                                $resultMinutes,
                                $resultSeconds
                            );
                    }
                }

                /* Week Hour Update */
                //WeekHour::where('user_id', $res)->where('week_start_date', $week_startDate)->where('week_end_date', $week_endDate)->update(['remaining_hours'=>$result,'working_hours'=>$var2,'entry_type'=>'c']);

                if($isHourlyEmployee){

                    WeekHour::where('user_id',$res)
                        ->where('week_start_date',$week_startDate)
                        ->where('week_end_date',$week_endDate)
                        ->update([
                            'working_hours'   => $var2,
                            'remaining_hours' => $result,
                            'entry_type'      => 'c'
                        ]);

                }else{

                    WeekHour::where('user_id',$res)
                        ->where('week_start_date',$week_startDate)
                        ->where('week_end_date',$week_endDate)
                        ->update([
                            'remaining_hours' => $result,
                            'working_hours'   => $var2,
                            'entry_type'      => 'c'
                        ]);

                }
            }
        }
    }
}
