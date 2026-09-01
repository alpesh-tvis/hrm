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
use App\Models\Leave;
use Carbon\Carbon;
use DB;
use DateTime;
use DateInterval;
use Log;
class EmployeeDashboardReportController extends Controller
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
        $current_hour = date('H');
        dd($role);
        
        
        $inactive_user = Employee::select('id')->where('service_enddate','!=',null)->get();
        $user_list = User::where('role','1')->where('id', '!=', auth()->id())->whereNotIn('id',$inactive_user)->get(); 
        
        if($request->user_list){
            $user_id = $request->user_list;
        }
            
        $projects = Project::select("projects.*")->whereRaw("find_in_set('".$user_id."',projects.employee_id)")->orderBy('project_name','asc')->get();

        if($current_hour < 4){
            $workdate = date('Y-m-d',strtotime("-1 days"));
        }

        $work_report = WorkReport::where('user_id',$user_id)->where('work_date',$workdate)->orderBy('id','asc')->get();
        
        /*** Office hours Calculation ***/
        $office_hours_start_query = DB::table('workreports')->where('user_id',$user_id)->where('work_date',$workdate)->first();
            
        if($office_hours_start_query){
            $office_start=Carbon::parse($office_hours_start_query->created_at);
            $checkout_query = DB::table('workreports')->where('user_id',$user_id)->where('work_date',$workdate)->where('work_type',4)->first();
            
            if($checkout_query){
                $current_datetime=Carbon::parse($checkout_query->created_at);
                $office_hours_query = WorkReport::where('work_date', $workdate)->where('user_id',$user_id)->where('work_time','!=',null)->sum(DB::raw("TIME_TO_SEC(work_time)"));
                $office_hours = gmdate("H:i:s", $office_hours_query);
                
            }else{
                
                $current_datetime=Carbon::parse(date("Y-m-d H:i:s"));
                $office_hours  = $office_start->diff($current_datetime)->format('%H:%I:%S');
            }
        }else{
            
            $office_hours = '00:00:00';
        }
            
        /*** End Office hours Calculation ***/

        /*** Working Hours Calculation ***/
        $working_hours_query = WorkReport::where('work_date', $workdate)->where('user_id',$user_id)->where('work_time','!=',null)->where('work_type','!=',3)->sum(DB::raw("TIME_TO_SEC(work_time)"));
        
        $get_latest_working_hours_query = WorkReport::where('work_date', $workdate)->where('user_id',$user_id)->where('work_time',null)->where('work_type','!=',3)->orderBy('id','desc')->first();
        
        if($get_latest_working_hours_query){
            $running_working_hours=Carbon::parse($get_latest_working_hours_query->created_at);

            $checkout_query = DB::table('workreports')->where('user_id',$user_id)->where('work_date',$workdate)->where('work_type',4)->first();
            
            if($checkout_query){
                $working_hours = gmdate("H:i:s", $working_hours_query);
            }else{
                $current_datetime1=Carbon::parse(date("Y-m-d H:i:s"));
                $working_hours_diff  = $running_working_hours->diff($current_datetime1)->format('%H:%I:%S');
                $working_hours1 = gmdate("H:i:s", $working_hours_query);
                $working_hours2 = $working_hours_diff;
                $total_working_hours = strtotime($working_hours1)-strtotime("00:00:00");
                $working_hours = date("H:i:s",strtotime($working_hours2)+$total_working_hours);
        }
            
        }else{
            $working_hours = gmdate("H:i:s", $working_hours_query);
        }
        /*** End Working Hours Calculation ***/

        /*** Break Hours Calculation ***/
        $break_query = WorkReport::where('work_date', $workdate)->where('user_id',$user_id)->where('work_time','!=',null)->where('work_type',3)->sum(DB::raw("TIME_TO_SEC(work_time)"));

        $get_latest_break_hours_query = WorkReport::where('work_date', $workdate)->where('user_id',$user_id)->where('work_time',null)->where('work_type',3)->first();
        if($get_latest_break_hours_query){
            $running_break_hours=Carbon::parse($get_latest_break_hours_query->created_at);
            $current_datetime1=Carbon::parse(date("Y-m-d H:i:s"));
            $break_hours_diff  = $running_break_hours->diff($current_datetime1)->format('%H:%I:%S');
            
            $break_hours1 = gmdate("H:i:s", $break_query);
            $break_hours2 = $break_hours_diff;

            $total_break_hours = strtotime($break_hours1)-strtotime("00:00:00");
            $break_hours = date("H:i:s",strtotime($break_hours2)+$total_break_hours);
        }else{
            $break_hours = gmdate("H:i:s", $break_query);
        }
        /*** End Break Hours Calculation ***/
        
        /*** Total week Working Hours Calculation ***/
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

            $get_latest_total_working_hours_query = WorkReport::where('work_date', $workdate)->where('user_id',$user_id)->where('work_time',null)->where('work_type','!=',3)->where('work_type','!=',4)->first();
            if($get_latest_total_working_hours_query){
                $current_datetime1=Carbon::parse(date("Y-m-d H:i:s"));
                $running_total_working_hours=Carbon::parse($get_latest_total_working_hours_query->created_at);
                $total_working_hours_diff  = $running_total_working_hours->diff($current_datetime1)->format('%H:%I:%S');
                
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

        /* Remaining Hours calculate */
        $Week_office_hours = WeekHour::where('user_id',$user_id)->orderBy('id','desc')->first();
        
        $now = Carbon::now();
        $week_startDate = now()->startOfWeek()->format('Y-m-d'); 
        $week_endDate = now()->endOfWeek()->format('Y-m-d');

        $exists_WeekHour = WeekHour::where('user_id', $user_id)
                ->where('week_start_date', $week_startDate)
                ->where('week_end_date', $week_endDate)
                ->exists(); 

        if($exists_WeekHour == true){
            $Week_working_hours_query = WeekHour::where('user_id',$user_id)->orderBy('id','desc')->skip(1)->first();

        }else{
            $Week_working_hours_query = WeekHour::where('user_id',$user_id)->orderBy('id','desc')->first();
        }


        if(!empty($Week_working_hours_query->next_week_hours)){
            $Week_working_hours = $Week_working_hours_query->next_week_hours;
        }else{
            $Week_working_hours = "40:00:00";
        }
        
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
        if($user_id == 14){
           
        }    

        
        
        /* End Remaining Hours calculate */
        $enddate = DB::table('workreports')->where('user_id',$user_id)->where('work_date',$workdate)->where('work_type',4)->first();
        
        return view('dashboard.employeedashboard')->with(compact('projects','work_report','user_list','office_hours','working_hours','break_hours','enddate','total_working_hours','role','Week_office_hours','Week_working_hours','remaining_hour'));
    }

    
    
}
