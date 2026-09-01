<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Employee;
use App\Models\Lead;
use App\Models\Client;
use App\Models\BidRelation;
use Auth;
use Storage;
use file;
use Illuminate\Support\Facades\Cache;

class ProjectController extends Controller
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
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;
        $userid = $user->id;

        // Get current employee
        $employee = Employee::find($userid);

        // Get all ongoing projects
        $project = Project::whereNull('end_date')->latest()->get();

        // Check if the user is assigned to any project
        $project1 = Project::whereRaw("find_in_set($userid , employee_id)")->exists();

        
        $assign_upwork_profile = Project::select('timer_id')
            ->whereNotNull('timer_id')->get();

        $uniqueupworkprofileValues = [];

        foreach ($assign_upwork_profile as $instance) {
            $ids = explode(',', $instance->timer_id);
            $uniqueupworkprofileValues = array_merge($uniqueupworkprofileValues, $ids);
        }

        $uniqueupworkprofileValues = array_unique($uniqueupworkprofileValues);

        $timerEmployees = Employee::whereIn('id', $uniqueupworkprofileValues)->get()->keyBy('id');

        $upworkValues = [];
        foreach ($uniqueupworkprofileValues as $id) {
            if (isset($timerEmployees[$id])) {
                $upworkValues[] = $timerEmployees[$id]->first_name;
            }
        }

       
        $assign_employee_profile = Project::select('employee_id')
            ->whereNotNull('employee_id')->get();

        $uniqueUserprofileValues = [];

        foreach ($assign_employee_profile as $instance) {
            $ids = explode(',', $instance->employee_id);
            $uniqueUserprofileValues = array_merge($uniqueUserprofileValues, $ids);
        }

        $uniqueUserprofileValues = array_unique($uniqueUserprofileValues);

        $allEmployees = Employee::whereIn('id', $uniqueUserprofileValues)->get()->keyBy('id');

        $ass_empValues = [];
        foreach ($uniqueUserprofileValues as $id) {
            if (isset($allEmployees[$id])) {
                $ass_empValues[] = $allEmployees[$id]->first_name;
            }
        }
        $check_department = $user ? \App\Models\Employee::where('company_email', $user->email)->first() : null;
        
        if ($project1 || $role == 2 || $employee->reporting_person == 1 || $check_department->department == '1') {
            return view('project.index')->with(compact(
                'project',
                'upworkValues',
                'ass_empValues',
                'role',
                'userid',
                'employee',
                'check_department'
            ));
        } else {
            return redirect()->route('admin.index');
        }
    }

    public function project_complete()
    {
        $user = Auth::user();
        $employee = Employee::find($user->id);
        $check_department = $user ? \App\Models\Employee::where('company_email', $user->email)->first() : null;

        if (
            $user->role != 2 &&
            $employee->reporting_person != 1 &&
            ($check_department->department ?? '') != '1'
        ) {
            return redirect()->route('admin.index');
        }

        $projects = Project::whereNotNull('end_date')
            ->latest()
            ->get();

        if (
            $user->role != 2 &&
            ($check_department->department ?? '') != '1'
        ) {
            $projects = $projects->filter(function ($proj) use ($user) {
                $assigned_ids = explode(',', $proj->employee_id);
                return in_array($user->id, $assigned_ids);
            });
        }

        $projects->transform(function ($proj) {
            $proj->assignedEmployees = Employee::whereIn('id', explode(',', $proj->employee_id))->get();
            $proj->upworkProfiles = Employee::whereIn('id', explode(',', $proj->timer_id))->get();

            $bidRelation = \App\Models\BidRelation::where('project_id', $proj->id)->first();
            $proj->client = $bidRelation ? \App\Models\Client::find($bidRelation->client_id) : null;

            return $proj;
        });

        return view('project.complete_project', [
            'projects' => $projects,
            'user' => $user,
            'employee' => $employee,
            'check_department' => $check_department
        ]);
    }

     public function project_complete_edit($id){
        
        $user = Auth::user();
        $employee = Employee::where('id',$user->id)->first();
        $clients = Client::orderBy('id','DESC')->get();
        $bids = Lead::orderBy('id','DESC')->get();
        $check_department = $user ? \App\Models\Employee::where('company_email', $user->email)->first() : null;
        if($user->role== 2 || $employee->reporting_person == 1 || $check_department->department == '1')
        {
            $bidrelation = BidRelation::where('project_id',$id)->first();
            $project = Project::find($id);
            $emp_list = Employee::where('service_enddate',null)->get();

            $user_array = explode(',', $project->timer_id);
            $all_user = Employee::where('upwork_profile', '!=', '1')->whereIn('id', $user_array)->get();
            $emp_timer1 = Employee::where('upwork_profile', '1')->get();
            $emp_timer = $emp_timer1->concat($all_user);
            
            $timer_id = $project->timer_id ? explode(',', $project->timer_id) : [];
            $emp_id   = $project->employee_id ? explode(',', $project->employee_id) : [];
            $cred_data = unserialize($project->cred_type);
            $viewData = [
                'project' => $project,
                'emp_list' => $emp_list,
                'emp_timer' => $emp_timer,
                'clients' => $clients,
                'bids' => $bids,
                'bidrelation' => $bidrelation,

                'timer_id' => $timer_id,
                'emp_id' => $emp_id,

                'cred_data' => $cred_data,
                'type' => $cred_data['cred']['type'] ?? null,
                'type_stg' => $cred_data['cred']['type_stg'] ?? null,

                'cred_host' => $cred_data['cred']['host'] ?? null,
                'cred_port' => $cred_data['cred']['port'] ?? null,
                'cred_user' => $cred_data['cred']['user'] ?? null,
                'cred_password' => $cred_data['cred']['password'] ?? null,
                'cred_encrption' => $cred_data['cred']['encrption'] ?? null,
                'cred_putty_file' => $cred_data['cred']['putty_file'] ?? null,

                'cred_host_stg' => $cred_data['cred']['host_stg'] ?? null,
                'cred_port_stg' => $cred_data['cred']['port_stg'] ?? null,
                'cred_user_stg' => $cred_data['cred']['user_stg'] ?? null,
                'cred_password_stg' => $cred_data['cred']['password_stg'] ?? null,
                'cred_encrption_stg' => $cred_data['cred']['encrptionv'] ?? null,
                'cred_putty_file_stg' => $cred_data['cred']['putty_file_stg'] ?? null,

                'admin_url' => $cred_data['admin']['url'] ?? null,
                'admin_username' => $cred_data['admin']['username'] ?? null,
                'admin_password' => $cred_data['admin']['password'] ?? null,

                'admin_url_stg' => $cred_data['admin']['url_stg'] ?? null,
                'admin_username_stg' => $cred_data['admin']['username_stg'] ?? null,
                'admin_password_stg' => $cred_data['admin']['password_stg'] ?? null,

                'database_url' => $cred_data['database']['url'] ?? null,
                'database_username' => $cred_data['database']['username'] ?? null,
                'database_password' => $cred_data['database']['password'] ?? null,

                'database_url_stg' => $cred_data['database']['url_stg_stg'] ?? null,
                'database_username_stg' => $cred_data['database']['username_stg'] ?? null,
                'database_password_stg' => $cred_data['database']['password_stg'] ?? null,

                'domain_host_url_stg' => $cred_data['domain_host']['url_stg'] ?? null,
                'domain_host_username_stg' => $cred_data['domain_host']['username_stg'] ?? null,
                'domain_host_password_stg' => $cred_data['domain_host']['password_stg'] ?? null,

                'cpanel_hosting_url' => $cred_data['cpanel_hosting']['url'] ?? null,
                'cpanel_hosting_username' => $cred_data['cpanel_hosting']['username'] ?? null,
                'cpanel_hosting_password' => $cred_data['cpanel_hosting']['password'] ?? null,

                'cpanel_hosting_url_stg' => $cred_data['cpanel_hosting']['url_stg'] ?? null,
                'cpanel_hosting_username_stg' => $cred_data['cpanel_hosting']['username_stg'] ?? null,
                'cpanel_hosting_password_stg' => $cred_data['cpanel_hosting']['password_stg'] ?? null,
            ];
           return view('project.complete_project_edit', array_merge(compact('project','emp_list','emp_timer','clients','bids','bidrelation'),$viewData));
        }else{
            return redirect()->route('admin.index');
        }
     }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        $clients = Client::latest()->get();
        $bids = Lead::latest()->get();
        $employee = Employee::where('id',$user->id)->first();
        $check_department = $user ? \App\Models\Employee::where('company_email', $user->email)->first() : null;

        if($user->role== 2 || $employee->reporting_person == 1 || $check_department->department == '1')
        {
            $emp_list = Employee::where('service_enddate', null)->get();
            $emp_timer = Employee::where('upwork_profile','1')->get();
            return view('project.create')->with(compact('emp_list','emp_timer','clients','bids'));
        }
        else{
            return redirect()->route('admin.index');
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
        $validated = $request->validate([
            'project_name' => 'required|unique:projects|max:255',
            //'project_description' => 'required',
            'start_date'   => 'required',
            'staging_url' => 'nullable|url',
            'live_url' => 'nullable|url',
            'admin_url' => 'nullable|url',
            'admin_url_stg' => 'nullable|url',
            'database_url' => 'nullable|url',
            'database_url_stg' => 'nullable|url',
            'domain_url' => 'nullable|url',
            'domain_url_stg' => 'nullable|url',
            'cpanel_url' => 'nullable|url',
            'cpanel_url_stg' => 'nullable|url',
            'cred_host' => ($request->cred_type != '') ? 'required' : '',
            'cred_host_stg' => ($request->cred_type_stg != '') ? 'required' : '',
            'cred_port' => ($request->cred_type != '') ? 'required' : '',
            'cred_port_stg' => ($request->cred_type_stg != '') ? 'required' : '',
            'cred_user' => ($request->cred_type != '') ? 'required' : '',
            'cred_user_stg' => ($request->cred_type_stg != '') ? 'required' : '',
            'cred_password' => ($request->cred_type != '') ? 'required' : '',
            'cred_password_stg' => ($request->cred_type_stg != '') ? 'required' : '',
            'cred_encr' => ($request->cred_type == '1') ? 'required' : '',
            'cred_encr_stg' => ($request->cred_type_stg == '1') ? 'required' : '',
            'cred_putty' => ($request->cred_type == '3') ? 'required' : '',
            'cred_putty_stg' => ($request->cred_type_stg == '3') ? 'required' : ''
        ],
        [],
        [
            'project_name' => 'Project Name',
            'cred_host' => 'Credentials Host',
            'cred_host_stg' => 'Credentials Staging Host',
            'cred_port' => 'Credentials Port',
            'cred_port_stg' => 'Credentials Staging Port',
            'cred_user' => 'Credentials User',
            'cred_user_stg' => 'Credentials Staging User',
            'cred_password' => 'Credentials Password',
            'cred_password_stg' => 'Credentials Staging Password',
            'cred_encr' => 'Credentials Encrption',
            'cred_encr_stg' => 'Credentials Staging Encrption',
            'cred_putty' => 'Credentials Putty File',
            'cred_putty_stg' => 'Credentials Staging Putty File'
        ]);

        if ($request->hasFile('cred_putty')) {
            
            $file = $request->file('cred_putty');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('putty-file', $filename);
            // $path = $file->storeAs('public/putty-file', $filename);
            
        }else{
            $path = '';
        }

        if ($request->hasFile('cred_putty_stg')) {
            
            $file = $request->file('cred_putty_stg');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $path1 = $file->storeAs('putty-file', $filename);
            // $path = $file->storeAs('public/putty-file', $filename);
            
        }else{
            $path1 = '';
        }    

        
        
        $cread = array(
           "cred" => array (
                "type"          => $request->cred_type,
                "host"          => $request->cred_host,
                "port"          => $request->cred_port,
                "encrption"     => $request->cred_encr,
                "user"          => $request->cred_user,
                "password"      => $request->cred_password,
                "putty_file"    => $path,
                "type_stg"          => $request->cred_type_stg,
                "host_stg"          => $request->cred_host_stg,
                "port_stg"          => $request->cred_port_stg,
                "encrptionv"     => $request->cred_encr_stg,
                "user_stg"          => $request->cred_user_stg,
                "password_stg"      => $request->cred_password_stg,
                "putty_file_stg"    => $path1
            ),
            "admin" => array (
                "url"       => $request->admin_url,
                "username"  => $request->admin_username,
                "password"  => $request->admin_password,
                "url_stg"       => $request->admin_url_stg,
                "username_stg"  => $request->admin_username_stg,
                "password_stg"  => $request->admin_password_stg
            ),
            "database" => array (
                "url"       => $request->database_url,
                "username"  => $request->database_username,
                "password"  => $request->database_password,
                "url_stg_stg"       => $request->database_url_stg,
                "username_stg"  => $request->database_username_stg,
                "password_stg"  => $request->database_password_stg
            ),
            "domain_host" => array (
                "url"       => $request->domain_url,
                "username"  => $request->domain_username,
                "password"  => $request->domain_password,
                "url_stg"       => $request->domain_url_stg,
                "username_stg"  => $request->domain_username_stg,
                "password_stg"  => $request->domain_password_stg
            ),
            "cpanel_hosting" => array (
                "url"           => $request->cpanel_url,
                "username"      => $request->cpanel_username,
                "password"      => $request->cpanel_password,
                "url_stg"           => $request->cpanel_url_stg,
                "username_stg"      => $request->cpanel_username_stg,
                "password_stg"      => $request->cpanel_password_stg
            ),
        );

        // Timer Ids
        if($request->timer_id){
            $timer_id = implode(',',$request->timer_id);
        }else{
            $timer_id = $request->timer_id;
        }

        // Employee Ids
        if($request->employee_id){    
            $employee_id = implode(',',$request->employee_id);
        }else{
            $employee_id = $request->employee_id;
        }

        $user_id = Auth::id();
            
        $project1 = [
            'user_id'               => $user_id,
            'project_name'          => $request->project_name,
            'project_description'   => $request->project_description,
            'staging_url'           => $request->staging_url,
            'live_url'              => $request->live_url,
            'timer_id'              => $timer_id,
            'employee_id'           => $employee_id,
            'start_date'            => $request->start_date,
            'end_date'              => $request->end_date,
            'service_status'        => $request->service_status,
            'cred_type'             => serialize($cread)
        ];

        $input = $request->all();
        
        $project = Project::create($project1);
        

        if(!empty($request->bid_id)){
            $bid_id = $request->bid_id;
        }else{
            $bid_id = null;
        }
        if(!empty($request->client_id)){
            $client_id = $request->client_id;
        }else{
            $client_id = null;
        }
        

        BidRelation::create([
            'bid_id' =>$bid_id,
            'client_id' =>$client_id,
            'project_id' => $project->id
        ]);
        
        return redirect()->route('project.index')->with('success','Project Added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $project = Project::find($id);

        $cred_data = unserialize($project->cred_type);
        
        $ftp_str = '';
        $enc_str = '';
        
        if(!empty($project->timer_id)){
            $explode_timer = explode(',',$project->timer_id);
            $timer_details = Employee::whereIn('id',$explode_timer)->select('id','upwork_username','upwork_password')->get();
            
            
        }else{
            $timer_details = '';
        }
        
        if($cred_data['cred']['type']){

            $ftp_arr = ["","FTP","SFTP","SSH"];
            $ftp_str = $ftp_arr[$cred_data['cred']['type']];

            if($cred_data['cred']['type'] == '1'){
                $enc_arr = ["","Use explicit FTP over TLS if available","Require explicit FTP over TLS","Require implicit FTP over TLS","Only use plain FTP(insecure)"]; 
                $enc_str = $enc_arr[$cred_data['cred']['encrption']];
            }
        }

        $ftp_str1 = '';
        $enc_str1 = '';
        if($cred_data['cred']['type_stg']){

            $ftp_arr = ["","FTP","SFTP","SSH"];
            $ftp_str1 = $ftp_arr[$cred_data['cred']['type_stg']];

            if($cred_data['cred']['type_stg'] == '1'){
                $enc_arr = ["","Use explicit FTP over TLS if available","Require explicit FTP over TLS","Require implicit FTP over TLS","Only use plain FTP(insecure)"]; 
                $enc_str1 = $enc_arr[$cred_data['cred']['encrptionv']];
            }
        }

        return response()->json([
            'data' => $project,
            'ftpdata' => $ftp_str,
            'ftpdata_stg' => $ftp_str1,
            'cred_data' => $cred_data,
            'encrption' => $enc_str,
            'encrption_stg' => $enc_str1,
            'timer' => $timer_details 
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Auth::user();
        $employee = Employee::where('id',$user->id)->first();
        // $clients = Client::orderBy('id','DESC')->get();
        // $bids = Lead::orderBy('id','DESC')->get();
        $clients = Client::latest()->get();
        $bids = Lead::latest()->get();
        $check_department = $user ? \App\Models\Employee::where('company_email', $user->email)->first() : null;

        if($user->role== 2 || $employee->reporting_person == 1 || $check_department->department == 1)
        {
            $bidrelation = BidRelation::where('project_id',$id)->first();
            $project = Project::find($id);
            $emp_list = Employee::where('service_enddate',null)->get();

            $user_array = explode(',', $project->timer_id);
            $all_user = Employee::where('upwork_profile', '!=', '1')->whereIn('id', $user_array)->get();
            $emp_timer1 = Employee::where('upwork_profile', '1')->get();
            $emp_timer = $emp_timer1->concat($all_user);
        
            $cread = array(
                "cred" => array (
                    "type"          => null,
                    "host"          => null,
                    "port"          => null,
                    "encrption"     => null,
                    "user"          => null,
                    "password"      => null,
                    "putty_file"    => null,
                    "type_stg"          => null,
                    "host_stg"          => null,
                    "port_stg"          => null,
                    "encrptionv"     => null,
                    "user_stg"          => null,
                    "password_stg"      => null,
                    "putty_file_stg"    => null
                ),
                "admin" => array (
                    "url"       => null,
                    "username"  => null,
                    "password"  => null,
                    "url_stg"       => null,
                    "username_stg"  => null,
                    "password_stg"  => null
                ),
                "database" => array (
                    "url"       => null,
                    "username"  => null,
                    "password"  => null,
                    "url_stg_stg"       => null,
                    "username_stg"  => null,
                    "password_stg"  => null
                ),
                "domain_host" => array (
                    "url"       => null,
                    "username"  => null,
                    "password"  => null,
                    "url_stg"       => null,
                    "username_stg"  => null,
                    "password_stg"  => null
                ),
                "cpanel_hosting" => array (
                    "url"           => null,
                    "username"      => null,
                    "password"      => null,
                    "url_stg"           => null,
                    "username_stg"      => null,
                    "password_stg"      => null
                ),
            );



            $cred_data = $cread;
            if($project->cred_type){
                $cred_data = unserialize($project->cred_type);
            }

            $timer_id = $project->timer_id ? explode(',', $project->timer_id) : [];
            $emp_id   = $project->employee_id ? explode(',', $project->employee_id) : [];
            $viewData = [
                'project' => $project,
                'emp_list' => $emp_list,
                'emp_timer' => $emp_timer,
                'clients' => $clients,
                'bids' => $bids,
                'bidrelation' => $bidrelation,

                'timer_id' => $timer_id,
                'emp_id' => $emp_id,

                'cred_data' => $cred_data,
                'type' => $cred_data['cred']['type'] ?? null,
                'type_stg' => $cred_data['cred']['type_stg'] ?? null,

                'cred_host' => $cred_data['cred']['host'] ?? null,
                'cred_port' => $cred_data['cred']['port'] ?? null,
                'cred_user' => $cred_data['cred']['user'] ?? null,
                'cred_password' => $cred_data['cred']['password'] ?? null,
                'cred_encrption' => $cred_data['cred']['encrption'] ?? null,
                'cred_putty_file' => $cred_data['cred']['putty_file'] ?? null,

                'cred_host_stg' => $cred_data['cred']['host_stg'] ?? null,
                'cred_port_stg' => $cred_data['cred']['port_stg'] ?? null,
                'cred_user_stg' => $cred_data['cred']['user_stg'] ?? null,
                'cred_password_stg' => $cred_data['cred']['password_stg'] ?? null,
                'cred_encrption_stg' => $cred_data['cred']['encrptionv'] ?? null,
                'cred_putty_file_stg' => $cred_data['cred']['putty_file_stg'] ?? null,

                'admin_url' => $cred_data['admin']['url'] ?? null,
                'admin_username' => $cred_data['admin']['username'] ?? null,
                'admin_password' => $cred_data['admin']['password'] ?? null,

                'admin_url_stg' => $cred_data['admin']['url_stg'] ?? null,
                'admin_username_stg' => $cred_data['admin']['username_stg'] ?? null,
                'admin_password_stg' => $cred_data['admin']['password_stg'] ?? null,

                'database_url' => $cred_data['database']['url'] ?? null,
                'database_username' => $cred_data['database']['username'] ?? null,
                'database_password' => $cred_data['database']['password'] ?? null,

                'database_url_stg' => $cred_data['database']['url_stg_stg'] ?? null,
                'database_username_stg' => $cred_data['database']['username_stg'] ?? null,
                'database_password_stg' => $cred_data['database']['password_stg'] ?? null,

                'domain_host_url_stg' => $cred_data['domain_host']['url_stg'] ?? null,
                'domain_host_username_stg' => $cred_data['domain_host']['username_stg'] ?? null,
                'domain_host_password_stg' => $cred_data['domain_host']['password_stg'] ?? null,

                'cpanel_hosting_url' => $cred_data['cpanel_hosting']['url'] ?? null,
                'cpanel_hosting_username' => $cred_data['cpanel_hosting']['username'] ?? null,
                'cpanel_hosting_password' => $cred_data['cpanel_hosting']['password'] ?? null,

                'cpanel_hosting_url_stg' => $cred_data['cpanel_hosting']['url_stg'] ?? null,
                'cpanel_hosting_username_stg' => $cred_data['cpanel_hosting']['username_stg'] ?? null,
                'cpanel_hosting_password_stg' => $cred_data['cpanel_hosting']['password_stg'] ?? null,
            ];

           return view('project.create', $viewData);
        }else{
            return redirect()->route('admin.index');
        }
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
    $project = Project::find($id);
    $cread_data = unserialize($project->cred_type);

    $validated = $request->validate([
        'project_name' => 'required|max:255',
        'start_date'   => 'required',
        'staging_url' => 'nullable|url',
        'live_url' => 'nullable|url',
        'admin_url' => 'nullable|url',
        'admin_url_stg' => 'nullable|url',
        'database_url' => 'nullable|url',
        'database_url_stg' => 'nullable|url',
        'domain_url' => 'nullable|url',
        'domain_url_stg' => 'nullable|url',
        'cpanel_url' => 'nullable|url',
        'cpanel_url_stg' => 'nullable|url',
        'cred_host' => ($request->cred_type != '') ? 'required' : '',
        'cred_host_stg' => ($request->cred_type_stg != '') ? 'required' : '',
        'cred_port' => ($request->cred_type != '') ? 'required' : '',
        'cred_port_stg' => ($request->cred_type_stg != '') ? 'required' : '',
        'cred_user' => ($request->cred_type != '') ? 'required' : '',
        'cred_user_stg' => ($request->cred_type_stg != '') ? 'required' : '',
        'cred_password' => ($request->cred_type != '') ? 'required' : '',
        'cred_password_stg' => ($request->cred_type_stg != '') ? 'required' : '',
        'cred_encr' => ($request->cred_type == '1') ? 'required' : '',
        'cred_encr_stg' => ($request->cred_type_stg == '1') ? 'required' : ''
    ],
    [],
    [
        'project_name' => 'Project Name',
        'cred_host' => 'Credentials Host',
        'cred_host_stg' => 'Credentials Staging Host',
        'cred_port' => 'Credentials Port',
        'cred_port_stg' => 'Credentials Staging Port',
        'cred_user' => 'Credentials User',
        'cred_user_stg' => 'Credentials Staging User',
        'cred_password' => 'Credentials Password',
        'cred_password_stg' => 'Credentials Staging Password',
        'cred_encr' => 'Credentials Encrption',
        'cred_encr_stg' => 'Credentials Staging Encrption',
        'cred_putty' => 'Credentials Putty File'
    ]);

    if ($request->hasFile('cred_putty')) {

        $file = $request->file('cred_putty');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('putty-file', $filename);

        if (!empty($cread_data['cred']['putty_file'] ?? null)) {
            Storage::delete($cread_data['cred']['putty_file']);
        }

    } else {
        $path = $cread_data['cred']['putty_file'] ?? null;
    }

    if ($request->hasFile('cred_putty_stg')) {

        $file = $request->file('cred_putty_stg');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $path1 = $file->storeAs('putty-file', $filename);

        if (!empty($cread_data['cred']['putty_file_stg'] ?? null)) {
            Storage::delete($cread_data['cred']['putty_file_stg']);
        }

    } else {
        $path1 = $cread_data['cred']['putty_file_stg'] ?? null;
    }

    $cread = array(
        "cred" => array (
            "type"          => $request->cred_type,
            "host"          => $request->cred_host,
            "port"          => $request->cred_port,
            "encrption"     => $request->cred_encr,
            "user"          => $request->cred_user,
            "password"      => $request->cred_password,
            "putty_file"    => $path,

            "type_stg"      => $request->cred_type_stg,
            "host_stg"      => $request->cred_host_stg,
            "port_stg"      => $request->cred_port_stg,
            "encrptionv"    => $request->cred_encr_stg,
            "user_stg"      => $request->cred_user_stg,
            "password_stg"  => $request->cred_password_stg,
            "putty_file_stg"=> $path1
        ),

        "admin" => array (
            "url"       => $request->admin_url,
            "username"  => $request->admin_username,
            "password"  => $request->admin_password,
            "url_stg"   => $request->admin_url_stg,
            "username_stg"  => $request->admin_username_stg,
            "password_stg"  => $request->admin_password_stg
        ),

        "database" => array (
            "url"       => $request->database_url,
            "username"  => $request->database_username,
            "password"  => $request->database_password,
            "url_stg_stg" => $request->database_url_stg,
            "username_stg" => $request->database_username_stg,
            "password_stg" => $request->database_password_stg
        ),

        "domain_host" => array (
            "url"       => $request->domain_url,
            "username"  => $request->domain_username,
            "password"  => $request->domain_password,
            "url_stg"   => $request->domain_url_stg,
            "username_stg" => $request->domain_username_stg,
            "password_stg" => $request->domain_password_stg
        ),

        "cpanel_hosting" => array (
            "url"       => $request->cpanel_url,
            "username"  => $request->cpanel_username,
            "password"  => $request->cpanel_password,
            "url_stg"   => $request->cpanel_url_stg,
            "username_stg" => $request->cpanel_username_stg,
            "password_stg" => $request->cpanel_password_stg
        ),
    );

    $timer_id = $request->timer_id ? implode(',', $request->timer_id) : null;
    $employee_id = $request->employee_id ? implode(',', $request->employee_id) : null;

    $project1 = [
        'user_id' => Auth::id(),
        'project_name' => $request->project_name,
        'project_description' => $request->project_description,
        'staging_url' => $request->staging_url,
        'live_url' => $request->live_url,
        'timer_id' => $timer_id,
        'employee_id' => $employee_id,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'service_status' => $request->service_status,
        'cred_type' => serialize($cread)
    ];

    Project::where('id',$id)->update($project1);

    $bid_id = $request->bid_id ?: null;
    $client_id = $request->client_id ?: null;

    $check_project_id_exits = BidRelation::where('project_id',$id)->exists();

    if($check_project_id_exits){
        BidRelation::where('project_id',$id)->update([
            'bid_id' =>$bid_id,
            'client_id' =>$client_id,
            'project_id' => $id
        ]);
    } else {
        BidRelation::create([
            'bid_id' =>$bid_id,
            'client_id' =>$client_id,
            'project_id' => $project->id
        ]);
    }

    return redirect()->route('project.index')->with('success','Project Update successfully.');
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
    public function download_putty($path)
    {
        $str = storage_path('/app/public/putty-file/'.$path);
        return response()->download($str);
    }
}
