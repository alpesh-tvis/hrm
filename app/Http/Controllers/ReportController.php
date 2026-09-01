<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Project;
use App\Models\WorkReport;
use App\Models\User;
use DataTables;
use Response;
use Auth;
use DB;
use DateTime;
use Carbon\Carbon;

class ReportController extends Controller
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
    if ($request->ajax()) {
        // dd($request->all());
        // Fetch users based on selected projects
        if ($request->project_id) {
            $get_users_id = WorkReport::whereIn('project_id', $request->project_id)
                ->select('user_id')
                ->groupBy('user_id')
                ->pluck('user_id')
                ->toArray();

            $usersQuery = User::orderBy('name', 'asc');
            if (!empty($get_users_id)) {
                $usersQuery->whereIn('id', $get_users_id);
            }
            $users = $usersQuery->get();

            return response()->json([
                'success' => true,
                'data'    => $users
            ]);
        }

        // Fetch work reports based on date range
        if ($request->report_date) {
            $ex_report_date = explode('-', $request->report_date);
            $from_date = trim($ex_report_date[0]);
            $to_date   = trim($ex_report_date[1]);

            $from_date = \Carbon\Carbon::createFromFormat('d/m/Y', $from_date)->format('Y-m-d');
            $to_date   = \Carbon\Carbon::createFromFormat('d/m/Y', $to_date)->format('Y-m-d');

            $user_id = Auth::id();
            $emp = Employee::find($user_id);

            $results = WorkReport::select('*', DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(work_time))) as total_work_time'))
                ->whereNotNull('work_time')
                ->whereBetween('work_date', [$from_date, $to_date]);

            if ($emp->reporting_person != '1') {
                $results->where('user_id', $user_id);
            } else {
                if (!empty($request->employee) && !in_array('all', $request->employee)) {
                    $results->whereIn('user_id', $request->employee);
                }
            }

            $results->where('work_type', $request->task);

            // Filter by activity/project/break
            if ($request->task == 1 && !empty($request->activity_type) && !in_array('all', $request->activity_type)) {
                $results->whereIn('activity_type', $request->activity_type);
            }
            if ($request->task == 2) {
                if (!empty($request->project) && !in_array('all', $request->project)) {
                    $results->whereIn('project_id', $request->project);
                }
                if (!empty($request->project) && !in_array('o1', $request->project)) {
                    $results->whereIn('project_id', $request->project);
                }
                if (!empty($request->report_activities) && !in_array('all', $request->report_activities)) {
                    $results->whereIn('activity_type', $request->report_activities);
                }
            }
            if ($request->task == 3 && !empty($request->break_activity) && !in_array('all', $request->break_activity)) {
                $results->whereIn('activity_type', $request->break_activity);
            }

            $results->groupBy('description', 'work_date', 'work_type', 'activity_type', 'project_id', 'user_id', 'emp_ids')
                    ->orderBy('id', 'desc');

            $get_report = $results->get();



            // Map data for Datatables
            $project_activity_arr = ["","Regular Work","Requirement Check","Client Message","Client Meeting","Discussion","R&D","Help","Analysis"];
            $activity_arr = ["","Mail Check","Event","Interview","Skill Improvement","Free"];
            $break_activity_arr = ["","Lunch","Water","Washroom","Call","Sleep","Snack","Other"];

            $newArray = [];
            foreach ($get_report as $report) {
                $user = Employee::find($report->user_id);
                $project_name = $report->project_id && $report->project_id != 'o1' ? $report->projectName->project_name ?? 'Other' : 'Other';
                $report_project = $report->work_type == 1 ? 'General Activity' : ($report->work_type == 3 ? 'Break' : $project_name);

                $activity = $report->work_type == 1 ? $activity_arr[$report->activity_type] : ($report->work_type == 2 ? $project_activity_arr[$report->activity_type] : $break_activity_arr[$report->activity_type]);

                $helpPersonName = $report->emp_ids ? $report->helpPerson->first_name ?? '' : '';
                $timer_name = $report->timer_id ? ($report->timer_id == 'wt' ? 'Without Timer' : $report->timerName->first_name ?? '') : '';

                $newArray[] = [
                    "id" => $report->id,
                    "user_id"=> $report->user_id,
                    "work_type"=> $report->work_type,
                    "activity_type"=> $report->activity_type,
                    "project_id"=> $report->project_id,
                    "description"=> $report->description,
                    "emp_ids"=> $report->emp_ids,
                    "created_at"=> $report->created_at,
                    "updated_at"=> $report->updated_at,
                    "work_date"=> $report->work_date,
                    "work_time"=> $report->work_time,
                    "total_work_time"=> $report->total_work_time,
                    "project" => $report_project,
                    "type" => $activity,
                    "name" => $user->first_name.' '.$user->last_name,
                    "help_person" => $helpPersonName,
                    "timer" => $timer_name
                ];
            }

            return Datatables::of($newArray)->toJson();
        }
    }

    // Normal page load
    $role = Auth::user()->role;
    $user_id = Auth::id();
    $emp = Employee::find($user_id);

    if ($role == "2" || $emp->reporting_person == '1') {
        $project_ids = WorkReport::whereNotNull('project_id')->groupBy('project_id')->pluck('project_id')->toArray();
        $project_name = Project::whereIn('id', $project_ids)->orderBy('project_name','asc')->get();

        $user_ids = WorkReport::select('user_id')->groupBy('user_id')->pluck('user_id')->toArray();
        $users = Employee::whereIn('id', $user_ids)
            ->whereNull('service_enddate')
            ->where('department','!=',1)
            ->orWhere('id', 15)
            ->orderBy('first_name','asc')
            ->get();

        return view("reports.work-report", compact('project_name','users','emp'));
    }

    if ($role == "1" || $emp->reporting_person != '1') {
        $project_ids = WorkReport::where('user_id', $user_id)
            ->whereNotNull('project_id')
            ->groupBy('project_id')
            ->pluck('project_id')
            ->toArray();
        $project_name = Project::whereIn('id', $project_ids)->orderBy('project_name','asc')->get();

        return view("reports.work-report", compact('project_name','emp'));
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
    
    }
    public function late_coming(Request $request)
    {
        $today = Carbon::today();
        $startDate = Carbon::create($today->year, 4, 1);
        $user_id = Auth::user()->id;
        $today_date = date('Y-m-d');

        $reporting_person = DB::table('employees')
            ->where('id', $user_id)
            ->first(['reporting_person']);

        if ($reporting_person->reporting_person == '1') {
            $get_employees = DB::table('employees')
                ->select('id','first_name','last_name')
                ->whereNull('service_enddate')
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get();
                
            if ($request->has('empl')) {
                $user_id = $request->empl;
            }
        } else {
            $get_employees = null;
        }


        $lateComing = DB::table('work_report_list as wr')
            ->select(
                'wr.start_time',
                'wr.end_time',
                'wr.working_hours',
                DB::raw('DATE(wr.start_time) as work_date'),
                DB::raw('TIME(wr.start_time) as start_time_only'),
                DB::raw('TIME(wr.end_time) as end_time_only')
            )
            ->where('wr.user_id', $user_id)
            ->whereBetween('wr.start_time', ['2024-04-01 00:00:00', now()])
            ->whereTime('wr.start_time', '>', '10:00:00')
            ->whereNotExists(function ($query) use ($user_id) {
                $query->select(DB::raw(1))
                    ->from('leaves as l')
                    ->whereRaw('DATE(l.leave_date) = DATE(wr.start_time)')
                    ->where('l.user_id', $user_id);
            })
            ->whereNotExists(function ($query) use ($user_id) {
                $query->select(DB::raw(1))
                    ->from('mail_requests as mr')
                    ->whereRaw('DATE(mr.request_date) = DATE(wr.start_time)')
                    ->where('mr.user_id', $user_id)
                    ->where('mr.status', 'approved');
            })
            ->groupBy(DB::raw('DATE(wr.start_time), wr.start_time, wr.end_time, wr.working_hours'))
            ->orderBy('work_date', 'desc') 
            ->get();

        $lateComing = $lateComing->map(function($record) {
            $pTime = Carbon::createFromFormat('H:i:s', '10:00:00');
            $startTime = Carbon::createFromFormat('H:i:s', $record->start_time_only);

            $diff = $pTime->diff($startTime);

            $hours = $diff->h;
            $minutes = $diff->i;
            $seconds = $diff->s;

            $late_by = '';
            if ($hours > 0) $late_by .= $hours . ' Hour' . ($hours > 1 ? 's ' : ' ');
            if ($minutes > 0) $late_by .= $minutes . ' Min ';
            if ($seconds > 0) $late_by .= $seconds . ' Sec';
            if (empty($late_by)) $late_by = '0 seconds';

            $record->month = date('F', strtotime($record->work_date));
            $record->day = date('l', strtotime($record->work_date));
            $record->late_by = $late_by;

            return $record;
        });

        return view("reports.late-coming", compact('lateComing','get_employees'));
    }

    
    public function getShiftReport($shiftType, Request $request)
    {
        $user_id = Auth::user()->id;
        $today_date = date('Y-m-d');
        $results = [];

        $reporting_person = DB::table('employees')
            ->where('id', $user_id)
        ->first(['reporting_person']);

        $get_employees = null;

        if($reporting_person->reporting_person == 1){
            if($request->has('empl')){
                $user_id = $request->empl;
            }
            
            $get_employees = DB::table('employees')
            ->select('id','first_name','last_name')
            ->whereNull('service_enddate')
            ->where('department',1)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
        }

        $dates = DB::table('workreports')
            ->where('user_id', $user_id)
            ->whereBetween('work_date', ['2024-04-01', $today_date])
            ->where('sift', $shiftType)
            ->groupBy('work_date')
            ->pluck('work_date');

        $shift_time_data = DB::table('shift_settings')
            ->where('employee_name', $user_id)
            ->where('shift', $shiftType)
            ->first();
        
        // dd($shift_time_data);   
        $shift_time = $shift_time_data ? $shift_time_data->mon : null;
        $shift_add_time = (new DateTime($shift_time))->modify('+30 minutes')->format('H:i:s');

        if ($dates->isNotEmpty()) {
            foreach ($dates as $date)
            {
                if ($shiftType === 1) {
                    $firstEntry = DB::table('workreports as wr')
                    ->where('wr.user_id', $user_id)
                    ->where('wr.work_date', $date)
                    ->whereNotExists(function ($query) use ($user_id, $date) {
                        $query->select(DB::raw(1))
                              ->from('leaves as l')
                              ->where('l.user_id', $user_id)
                              ->where('l.leave_date', $date)
                              ->where('l.leave_status', 'FH');
                    })
                    ->orderBy('wr.id', 'asc') 
                    ->first();

                    if ($firstEntry) {
                        $results[] = $firstEntry;
                    }
                } else {
                    $firstEntry = DB::table('workreports as wr')
                    ->where('wr.user_id', $user_id)
                    ->where('wr.work_date', $date)
                    ->where('wr.sift', 1)
                    ->whereNotExists(function ($query) use ($user_id, $date) {
                        $query->select(DB::raw(1))
                              ->from('leaves as l')
                              ->where('l.user_id', $user_id)
                              ->where('l.leave_date', $date)
                              ->where('l.leave_status', 'SH');
                    })
                    ->orderBy('wr.id', 'asc') 
                    ->first();
                    if ($firstEntry) {
                        $nextEntry = DB::table('workreports')
                            ->where('user_id', $user_id)
                            ->where('work_date', $date)
                            ->where('id', '>', $firstEntry->id)
                            ->orderBy('created_at', 'asc')
                            ->first();
                    

                        if ($nextEntry) {
                            $results[] = $nextEntry;
                        }
                    }
                }
            }
        }

        // Filter late entries
        $get_first_shift  = [];
        foreach ($results as $result) {
            $created_at_time = date('H:i:s', strtotime($result->created_at));

            if ($created_at_time > $shift_add_time) {
                $result->created_at = date('h:i: A', strtotime($created_at_time));
                $get_first_shift [] = $result;
            }
        }

        $date = (new DateTime($shift_time))->format('h:i A');
        $first_shift = $shiftType ? $date : null;

        return view("reports.late-coming-first-shift")
            ->with(compact('get_first_shift', 'get_employees', 'first_shift'));
    }

    // To call for first shift
    public function first_shift(Request $request)
    {
        return $this->getShiftReport(1, $request);
    }

    // To call for second shift
    public function second_shift(Request $request)
    {
        return $this->getShiftReport(2, $request);
    }


    public function early_going(Request $request){
        $user_id = Auth::user()->id;
        $today_date = date('Y-m-d');

        $reporting_person = DB::table('employees')
            ->where('id', $user_id)
        ->first(['reporting_person']);


        if($reporting_person->reporting_person == '1'){
            
            $get_employees = DB::table('employees')
            ->select('id','first_name','last_name')
            ->whereNull('service_enddate')->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
            
            if($request->has('empl')){
                $user_id = $request->empl;
            }
        } else {
            $get_employees = null;
        }
        
        $earlygoing = DB::table('work_report_list')
        ->where('user_id', $user_id)
        ->whereBetween('work_date', ['2024-04-01', $today_date])
        ->whereBetween('working_hours', ['05:59:00', '07:30:00'])
        ->select(
            'id',
            DB::raw('TIME(start_time) as start_time'),
            DB::raw('TIME(end_time) as end_time'),
            'working_hours',
            'work_date',
            'user_id'
        )
        ->get();
        return view("reports.early-going")->with(compact('earlygoing','get_employees'));
    }
}
