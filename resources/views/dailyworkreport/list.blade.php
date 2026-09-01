@extends('admin.master')
@section('content')
<section class="content workReport">
    <div class="container-fluid">
        <div class="row info-detail ">
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-3">
                <div class="small-box">
                    <div class="inner">
                        <h3 id="countdown">{{$office_hours ? $office_hours : 0}}</h3>
                        <p>Office Hours</p>
                    </div>
                    <div class="icon">
                        <img src="{{asset('img/office-icon.png')}}">
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-3">
                <div class="small-box ">
                    <div class="inner">
                        <h3 id="countdown_working_hours">{{$working_hours ? $working_hours : 0}}</h3>
                        <p>Working hours</p>
                    </div>
                    <div class="icon">
                        <img src="{{asset('img/hours-icon.png')}}">
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-3">
                <div class="small-box ">
                    <div class="inner">
                        <h3 id="countdown_break_hours">{{$break_hours ? $break_hours : 0}}</h3>
                        <p>Break</p>
                    </div>
                    <div class="icon">
                        <img src="{{asset('img/break-icon.png')}}">
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-3">
                <div class="small-box">
                    <div class="inner">
                        <h3 id="total_working_hours">{{$total_working_hours ? $total_working_hours : ''}}</h3>
                        <p>Week Working Hours</p>
                    </div>
                    <div class="icon">
                        <img src="{{asset('img/wheek-icon.png')}}">
                    </div>
                </div>
            </div>
        </div>
        @php
            $u_id = Auth::user()->id;
        @endphp
        
        @php
            $endbtn = 'notend';
            $check_entry_exists = DB::table('workreports')->where('user_id',Auth::user()->id)->where('work_date',$workdate)->exists();

            if($check_entry_exists){
                $get_first_entry = DB::table('workreports')->where('user_id',Auth::user()->id)->where('work_date',$workdate)->orderby('id','desc')->first();
            }
        @endphp
        
        @if($role == 1 || $role == 3)
            <div class="row">
                <div class="col-md-12 pad0">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if (session('warning'))
                        <div class="alert alert-warning">
                            {{ session('warning') }}
                        </div>
                    @endif
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                    @endif

                    {{--@if( Session::has("success") )
                        <div class="alert alert-success alert-block" role="alert">
                        <button class="close" data-dismiss="alert"></button>
                            {{ Session::get("success") }}
                        </div>
                    @endif--}}
                    
                    @if(empty($enddate))
                        @if(empty($_GET))
                            @if($sift_count < 10)
                                @php
                                    $endbtn = 'notend';
                                @endphp
                                <form method="post" action="{{route('daily_work_report.store')}}" name="daily_report" class="daily_report">
                                    @csrf
                                    <div class="card-primary task_row">
                                        <label>Select task</label>
                                        <div class="work-type">
                                            <div class="float-sm-left">
                                                <input type="hidden" name="work_type" value="">
                                                <input  type="radio" id="ga" name="work_type" value="1">
                                                <label for="ga" class="btn">General Activity</label>
                                                <input  type="radio" id="pw" name="work_type" value="2">
                                                <label for="pw" class="btn">Project Work</label>
                                                <input  type="radio" id="break" name="work_type" value="3">
                                                <label for="break" class="btn">Break</label>

                                                @if($role == 2)
                                                    <input  type="radio" id="pre_activity" name="work_type" value="5">
                                                    <label for="pre_activity" class="pre_activity btn">Previous Activity</label>
                                                @endif
                                                
                                                @if($sift !='regular')
                                                    @if($check_entry_exists)
                                                        @if(empty($get_first_entry->sift))
                                                            <input  type="radio" id="sift-{{$sift}}" name="work_type" value="6">
                                                            <label for="sift-{{$sift}}" class="custom-checkout">Shift-{{$sift}}</label>
                                                        @endif
                                                    @endif    
                                                @else
                                                    <input  type="radio" id="end_day" name="work_type" value="4">
                                                    <label for="end_day" class="custom-checkout">DAY OFF</label>    
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="card-body">
                                            <div class="row ga-type" style="display: none;">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Activity Type</label>
                                                        <div class="work-type">
                                                            <div class="float-sm-left">
                                                                <input  type="radio" id="mail_check" name="activity_type" value="1">
                                                                <label for="mail_check" class="btn disble-color" >Mail Check</label>
                                                                <input  type="radio" id="event" name="activity_type" value="2">
                                                                <label for="event" class="btn disble-color">Event</label>
                                                                <input  type="radio" id="interview" name="activity_type" value="3">
                                                                <label for="interview" class="btn disble-color">Interview</label>
                                                                <input  type="radio" id="si" name="activity_type" value="4">
                                                                <label for="si" class="btn disble-color">skill  improvement</label>
                                                                <input  type="radio" id="free" name="activity_type" value="5">
                                                                <label for="free" class="btn disble-color">Free</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row break" style="display: none;">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Break Type</label>
                                                        <div class="work-type">
                                                            <div class="float-sm-left">
                                                                <input  type="radio" id="lunch" name="activity_type" value="1">
                                                                <label for="lunch" class="btn disble-color">Lunch</label>
                                                                <input  type="radio" id="water" name="activity_type" value="2">
                                                                <label for="water" class="btn disble-color">Water</label>
                                                                <input  type="radio" id="washroom" name="activity_type" value="3">
                                                                <label for="washroom" class="btn disble-color">Washroom</label>
                                                                <input  type="radio" id="call" name="activity_type" value="4">
                                                                <label for="call" class="btn disble-color">Call</label>
                                                                <input  type="radio" id="sleep" name="activity_type" value="5">
                                                                <label for="sleep" class="btn disble-color">Sleep</label>
                                                                <input  type="radio" id="snack" name="activity_type" value="6">
                                                                <label for="snack" class="btn disble-color">Snack</label>
                                                                <input  type="radio" id="other_break" name="activity_type" value="7">
                                                                <label for="other_break" class="btn disble-color">Other</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row project-work" style="display: none;">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="department">Projects</label>
                                                        <select class="form-control" name="project_work">
                                                            <option value="">-- Select Project --</option>
                                                            @foreach($projects as $project)
                                                            <option value="{{$project->id}}">{{$project->project_name}}</option>
                                                            @endforeach
                                                            <option value="o1">Other</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 p_activity" style="display:none">
                                                    <div class="form-group">
                                                        <label for="emp_ids">Activities</label>
                                                        <select class="form-control" name="activity_type1">
                                                            <option value="">-- Select Activity --</option>
                                                            <option value="1">Regular Work</option>
                                                            <option value="2">Requirement Check</option>
                                                            <option value="3">Client Message</option>
                                                            <option value="4">Client Meeting</option>
                                                            <option value="5">Disscussion</option>
                                                            <option value="6">R&D</option>
                                                            <option value="7">Help</option>
                                                            <option value="8">Analysis</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 u_timer">
                                                    <div class="form-group">
                                                        <label for="emp_ids">Timers</label>
                                                        <div class="u_timer_select"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 user_list" style="display:none">
                                                    <div class="form-group">
                                                        <label for="emp_ids">Users</label>
                                                        <select class="form-control" name="emp_ids">
                                                            <option value="">-- Select User --</option>
                                                            @foreach($user_list as $user)
                                                            <option value="{{$user->id}}">{{$user->first_name}} {{$user->last_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row all_desc" style="display: none;">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="department">Description</label>
                                                        <textarea name="description" class="form-control"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if(empty($enddate))
                                        <div class="work_submit">
                                            <input type="submit" name="work_submit" value="Submit" class="btn btn-primary submit">
                                        </div>
                                        @endif
                                    </div>
                                </form>
                            @endif    
                        @endif    
                    @else
                        @php
                            $endbtn = 'end';
                        @endphp
                    @endif
                </div>
            </div>
        @endif
        <div class="row"><div class="col-sm-12">
        <form method="get" action="{{route('daily_work_report.index')}}" name="daily_report">
            <div class="row d-flex justify-content-end">
                @if($role == 2)
                    @if($enddate)
                        @if($enddate->work_type == 4)
                            @php
                                $endbtn = 'end';
                            @endphp
                        @endif
                    @endif
                    <div class="col-auto">
                        
                        <select class="form-control" name="user_list">
                            <option value="">-- Select User --</option>
                            @foreach($user_list as $user)

                                <option value="{{$user->id}}" {{ isset($_GET['user_list']) ? ($user->id == $_GET['user_list'] ? 'selected' : '') : '' }}> {{$user->first_name}} </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                
                @if($role == 1 || $role == 2 || $role == 3)
                    <div class="col-auto">
                        <input type="date" name="report_date" value="<?php if(isset($_GET['report_date'])){ echo $_GET['report_date']; }else{ echo date('Y-m-d'); }?>" class="form-control" max="<?= date('Y-m-d'); ?>" format="dd/mm/yyyy" id="disableFuturedate">
                    </div>
                @endif
                @if($role == 2 )
                <div class="col-auto">
                    <input type="submit" name="work_search" value="Search" class="btn btn-primary submit">
                </div>
                @else
                <div class="col-auto">
                    <input type="submit" name="work_search" value="Search" class="btn btn-primary submit" id="searchbtn">
                </div>
                @endif
                <?php
                    if(isset($_GET['report_date']) || isset($_GET['user_list'])){ ?>
                        <div class="col-auto">
                            <a id="clear_search" class="btn btn-danger">Clear search</a>
                        </div>
                        <?php
                    }
                ?>
            </div>
        </form>
        
        <table id="work_report" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Type</th>
                    <th>Project Name</th>
                    <th>Activity</th>
                    <th>Start Time</th>
                    <th>Description</th>
                    <th>Hours</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $work_arr = ["","General Activity","Project Work","Break","End","","Sift 1 End"]; 
                    
                    $activity_arr = ["","Mail Check","Event","Interview","Skill Improvement","Free"];
                    $break_arr = ["","Lunch","Water","Washroom","Call","Sleep","Snack","Other"];
                    
                    $project_activity_arr = ["","Regular Work","Requirement Check","Client Message","Client
                    Meeting","Disscussion","R&D","Help","Analysis"];
                    $work_type_flag = '';
                    $work_time_val = '';
                    $time = '';
                    $index = 1;
                @endphp
                
                @foreach($work_report as $work)
                    
                    @php
                        $work_type = $work_arr[$work->work_type];
                        $work_type_flag = $work_type;
                        if($work->work_time !=''){
                            $work_time_val = $work->work_time;
                        }else{
                            $work_time_val = 'null_val';
                        }

                        if(!empty($work->activity_type)){
                            if($work->work_type === '1'){
                                $activity = $activity_arr[$work->activity_type];
                            }
                            if($work->work_type === '3'){
                                $activity = $break_arr[$work->activity_type];
                            }
                            if($work->work_type === '2'){
                                $activity = $project_activity_arr[$work->activity_type];
                            }
                            if($work->work_type === '4'){
                                $activity = 'Day End';
                            }
                        }else{
                            $activity = '';
                        }

                        if($activity == 'Help'){
                            if($work->emp_ids){
                                $emp_data = DB::table('employees')->where('id',$work->emp_ids)->first();
                                if($emp_data->first_name){
                                    $helped_person = $emp_data->first_name;
                                }else{
                                    $helped_person = '';
                                }
                            }else{
                                $helped_person = '';
                            }    

                        }else{
                            $helped_person = '';
                        }
                
                        $pre_date = DB::table('workreports')->where('user_id',Auth::user()->id)->where('id','>',$work->id)->orderBy('id', 'asc')->first();


                
                        if($pre_date){
                            $start = new Carbon\Carbon($work->created_at);
                            $end   = new Carbon\Carbon($pre_date->created_at);
                            $time  = $start->diff($end)->format('%H:%I:%S');
                            }else{
                            if($work->work_type === '4' || $work->work_type === '6'){
                            $time  = '-';
                            }else{
                            $start = new Carbon\Carbon($work->created_at);
                            $end   = new Carbon\Carbon(now());
                            $time  = $start->diff($end)->format('%H:%I:%S');
                        }
                    }
                    $project_name = DB::table('projects')->where('id',$work->project_id)->first();
            
                @endphp
                <tr>
                    <td>{{$index++}}</td>
                    <td>{{$work_type}}</td>
                    <td>{{!empty($project_name->project_name) ? $project_name->project_name : ''}}</td>
                    <td>{{$activity}} {{$helped_person ? ':'.$helped_person : ''}}

                    @if ($work->timer_id != null)
                        <?php
                            $timer_name = \App\Models\Employee::find($work->timer_id);
                        ?>
                        @if ($timer_name)
                            <small class="badge badge-success p-2">{{ $timer_name->first_name }}</small>
                        @endif
                        @if($work->timer_id == 'wt')
                            <small class="badge badge-success p-2">No Timer</small>
                        @endif
                    @endif    
                    
                    </td>
                    <td>{{$work->created_at->format('h:i:s A')}}</td>
                    <td>{{$work->description ? $work->description : ''}}</td>
                    <td id="{{!$pre_date ? 'run_timer' : ''}}">{{$time ? $work->work_time : '-'}}</td>
                    <td>
                        @if($pre_date) {{-- ONLY PAST ENTRIES --}}
                            @if(empty($enddate))
                                @if(empty($_GET))
                                    @if($sift_count < 10)

                                        <form action="{{ route('daily_work_report.store')}}" 
                                              method="post" 
                                              style="display:inline"
                                              onsubmit="return confirm('Are you sure you want to Start this Activity?');">

                                            {{csrf_field()}}
                                            <input type="hidden" name="start_activity" value="{{$work->id}}">

                                            <button class="btn-primary-rv start-btn" type="submit">
                                                Start
                                            </button>

                                        </form>

                                    @endif    
                                @endif
                            @endif
                        @endif

                        @if($role == 2)
                            @if($work->work_type == 4)
                                @if(isset($_GET['report_date']) && $_GET['report_date'] == date('Y-m-d'))

                                    <form action="{{ route('daily_work_report.store',$work->id) }}" method="post" style="display:inline" onsubmit="return confirm('Are you sure you want to Day ON Again ?');">
                                        {{csrf_field()}}
                                        <input type="hidden" name="day_on_again" value="{{$work->id}}">
                                            <button class="btn btn-primary show-alert-delete-box" name="day_on_again_smt" type="submit">Day ON ?</button>
                                                    
                                    </form>
                                @endif
                            @endif

                        @endif

                    @if(!isset($_GET['report_date']) && !empty($project_name->project_name) ? $project_name->project_name.': ' : '')
                        <div id="edit-modal-{{$work->id}}" class="modal fade modal-top-right" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-top-right">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title">Update Description : Daily Work Report</h5>

                                        <button type="button" class="close" data-dismiss="modal">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>

                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <form method="post"
                                          action="{{ route('daily_work_report.update',$work->id) }}"
                                          class="form-horizontal">

                                        @csrf
                                        @method('PUT')

                                        <div class="modal-body">
                                            <div class="card-body">
                                                <div class="row">

                                                    {{-- DESCRIPTION --}}
                                                    <div class="col-md-12">
                                                        <div class="form-group description">
                                                            <label>Description</label>
                                                            <textarea class="form-control" name="description">{{ $work->description }}</textarea>
                                                        </div>
                                                    </div>

                                                   @if(!$pre_date && !empty($work->timer_id))
                                                <div class="col-md-5 edit_timer_box">
                                                    <div class="form-group">
                                                        <label>Timers</label>
                                                        <div class="edit_timer_select"></div>
                                                    </div>
                                                </div>
                                            @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit"
                                                    id="daily_work_report_submit"
                                                    class="btn btn-primary">
                                                Update
                                            </button>
                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>

                        {{-- edit button --}}
                        <a href="#edit-modal-{{$work->id}}" class="btn btn-info btn-success" data-toggle="modal" data-project-id="{{ $work->project_id }}" data-timer-id="{{ $work->timer_id }}">
                            <i class="fas fa-edit"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
                
            </tbody>
            <tfoot>
            <tr>
                <th>No</th>
                <th>Type</th>
                <th>Project Name</th>
                <th>Activity</th>
                <th>Start Time</th>
                <th>Description</th>
                <th>Hours</th>
                <th>Action</th>
            </tr>
            </tfoot>
        </table>
    </div>
</section>
@endsection
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>

<script src="{{asset('plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/jszip/jszip.min.js')}}"></script>
<script src="{{asset('plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>

<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer=""></script>
<script type="text/javascript">
//disable search button     
 jQuery(function () {
    function getCurrentDate() {
        const today = new Date();
        const day = String(today.getDate()).padStart(2, '0');
        const month = String(today.getMonth() + 1).padStart(2, '0'); 
        const year = today.getFullYear();
        return `${year}-${month}-${day}`; 
    }

    const currentDate = getCurrentDate();
    
    if ($('#disableFuturedate').val() === currentDate) {
        $('#searchbtn').attr('disabled', 'disabled');
    }

    $('#disableFuturedate').on('change', function() {
        const selectedDate = $(this).val();
        if (selectedDate === currentDate) {
            $('#searchbtn').attr('disabled', 'disabled');
        } else {
            $('#searchbtn').removeAttr('disabled');
        }
    });

    // Disable notification when description is updated
    setTimeout(function(){
        $("div.alert").remove();
    }, 10000);
});

jQuery(function () {
    jQuery.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    jQuery( "#ga" ).on( "click", function() {
        jQuery(".ga-type").show();
        jQuery(".break").hide();
        jQuery(".project-work").hide();
        jQuery(".all_desc").hide();
        jQuery("input[name='activity_type']").prop('checked', false);
    });

    jQuery( "#break" ).on( "click", function() {
        jQuery(".break").show();
        jQuery(".ga-type").hide();
        jQuery(".project-work").hide();
        jQuery(".all_desc").hide();
        jQuery("input[name='activity_type']").prop('checked', false);
    });

    jQuery( "#pw" ).on( "click", function() {
        jQuery(".break").hide();
        jQuery(".ga-type").hide();
        jQuery(".project-work").show();
        jQuery(".all_desc").hide();
        jQuery(".user_list").hide();
        jQuery("select[name='project_work']").val('').change();
        jQuery("select[name='emp_ids']").val('').change();
    });

    jQuery("#pre_activity").on("click", function() {
        jQuery(".break").hide();
        jQuery(".ga-type").hide();
        jQuery(".all_desc").hide();
        jQuery(".project-work").hide();
        jQuery(".user_list").hide();
        jQuery("input[name='activity_type']").prop('checked', false);
    });

    jQuery( "#end_day" ).on( "click", function() {
        if (confirm('Are you sure your want to day end?')) {
            jQuery(".work_submit input[type='submit']").trigger("click");
        }else{
            jQuery("input[name='work_type']").prop('checked', false);
        }
    });

    jQuery( "#sift-1" ).on( "click", function() {
        if (confirm('Are you sure your want to Sift 1 end?')) {
            jQuery(".work_submit input[type='submit']").trigger("click");
        }else{
            jQuery("input[name='work_type']").prop('checked', false);
        }
    });

    jQuery( "#sift-2" ).on( "click", function() {
        if (confirm('Are you sure your want to Sift 2 end?')) {
            jQuery(".work_submit input[type='submit']").trigger("click");
        }else{
            jQuery("input[name='work_type']").prop('checked', false);
        }
    });

    jQuery("select[name='project_work']").on("change", function() {
        var option = jQuery(this).val();

        var selected = jQuery(".p_activity select").val('');
        var timer_html = $(".u_timer_select").html('');
        $(".u_timer").hide();

        if(option != ''){
            jQuery(".p_activity").show();
            jQuery(".all_desc").show();
        }else{
            jQuery(".p_activity").hide();
            jQuery(".all_desc").hide();
        }
        return false;
    });

    jQuery("select[name='activity_type1']").on("change", function() {
        var option = jQuery(this).val();
        
        var project_id = jQuery("select[name='project_work'] option:selected").val();
        
        if(option == '7'){
            jQuery(".user_list").show();
        }else{
            jQuery(".user_list").hide();
        }

        if(option == '1'){
            var user_id = "<?php echo Auth::user()->id ?>";
            
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data : { id: project_id},
                url: "{{ route('daily_work_report.show', '') }}/" + project_id,   // for local environment
                type: "GET",
                beforeSend: function() {
                    // if(user_id == '14'){
                        if(project_id != 'o1'){
                            $('.work_submit input').attr('disabled','disabled');
                            $('.spinner-wrapper').show();
                        // }    
                    }
                    
                },
                success: function (response) {
                    if(response){
                        var user_id = "<?php echo Auth::user()->id ?>";
                        var timer_html = $(".u_timer_select").html(response);
                        
                        $("input[name='work_submit']").show();
                        $('.work_submit input').removeAttr('disabled','disabled');
                        
                        // if(user_id == '14'){
                            $(".u_timer").show();
                            $('.spinner-wrapper').hide();
                        // }
                        return false;
                    }else{
                        var timer_html = $(".u_timer_select").html('');
                        
                        $(".u_timer").hide();
                        $('.work_submit input').removeAttr('disabled','disabled');
                        $('.spinner-wrapper').hide();
                    }
                    $('.work_submit input').removeAttr('disabled','disabled');
                }
            });
        }else{
            var timer_html = $(".u_timer_select").html('');
            $(".u_timer").hide();
        }
    });

    var work_type = jQuery("input[name='work_type']:selected").val();
    if(work_type == '2'){
        if(val == 'ol'){
            jQuery(".user_list").show();
        }else{
            jQuery(".user_list").hide();
        }
    }

    jQuery("input[name='activity_type']").on("click", function(){
        var val = jQuery(this).val();
        var work_type = jQuery("input[name='work_type']:checked").val();
        
        if(work_type == '3'){
            if(val == '7'){
                jQuery(".all_desc").show();
            }else{
                jQuery(".all_desc").hide();
            }
        }

        if(work_type == '1'){
            if(val == '2' || val == '3' || val == '4'){
                jQuery(".all_desc").show();
            }else{
                jQuery(".all_desc").hide();
            }
        }
    });

    jQuery("select[name='activity_type']").on("change", function() {
            var option = jQuery(this).val();
            
            if(option == '6'){
                jQuery(".user_list").show();
            }else{
                jQuery(".user_list").hide();
            }
        });
    });

    $(document).on('click', '.stop-timer', function () {
        let timer_id = $(this).data('id');

        $.ajax({
            url: "{{ route('timer.stop') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                timer_id: timer_id
            },
            success: function () {
                location.reload(); // refresh UI
            }
        });
    });
    jQuery(function ($) {
        $("#work_report").DataTable({
            "responsive": true,
            "lengthChange": false,
            "paging":false,
            "order": [[0,"desc"]],
            "autoWidth": false,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'csv',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                }
            ]
        }).buttons().container().appendTo('#work_report_wrapper .col-md-6:eq(0)');
    });

</script>
<script type="text/javascript">
    // Office hours Counter
    function counter_timer(hours, span_sec) {
        var time = hours,
        parts = time.split(':'),
        hours = +parts[0],
        minutes = +parts[1],
        seconds = +parts[2],
        span = $('#'+span_sec);

        function correctNum(num) {
            return (num<10)? ("0"+num):num;
        }

        var timer = setInterval(function(){
            seconds++;
            if(seconds == 60) {
                seconds = 0;
                minutes++;

                if(minutes == 60) {
                    minutes = 0;
                    hours++;
                }
            }

            span.text(correctNum(hours) + ":" + correctNum(minutes) + ":" + correctNum(seconds));
        }, 1000);
    }

    // remaining hours countdown
    function remcounter_timer(hours, span_sec) {
        var time = hours,
        parts = time.split(':'),
        hours = +parts[0],
        minutes = +parts[1],
        seconds = +parts[2],
        span = $('#'+span_sec);

        function correctNum(num) {
            return (num<10)? ("0"+num):num;
        }

        var timer = setInterval(function(){
            seconds--;
            if(seconds == 0) {
                seconds = 60;
                minutes--;

                if(minutes <= 0) {
                    minutes = 59;
                    hours--;
                }
            }
            if (hours <= 0 && minutes <= 0 && seconds <= 0) {
                return false;
            }

            span.text(correctNum(hours) + ":" + correctNum(minutes) + ":" + correctNum(seconds));
        }, 1000);
    }
    
    window.addEventListener("visibilitychange", function (event) {
        if(document.visibilityState == 'hidden') {
        //jQuery('.spinner-wrapper').hide();
        //jQuery('.preloader').css('height','0');
        } else {
        //jQuery('.spinner-wrapper').show();
        //jQuery('.preloader').css('height','0');
        //window.location.reload();
        }
    });

    $(document).ready(function() {
        $("input[name='work_submit']").hide();
        var flag_counter = "<?php echo $work_type_flag?>";
        var endday = "<?php echo $endbtn?>";
        var work_time_val = "<?php echo $work_time_val?>";
        console.log(work_time_val);
        
        const urlParams = new URLSearchParams(window.location.search);
    
        // Check if the 'report_date' parameter exists
        if (urlParams.has('report_date')) {
            
            const reportDate = urlParams.get('report_date');
            const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
            
            if (dateRegex.test(reportDate)) {
                var today_date = "past_date";
                
            } else {
                var today_date = 'in_valid_date';
            }
        } else {
            var today_date = 'today_date';
        }

        if(today_date != 'past_date'){
            if(endday == 'notend'){
                if(work_time_val == 'null_val'){
                    if(flag_counter == 'General Activity' || flag_counter == 'Project Work' || flag_counter=='Break'){

                        counter_timer("<?php echo $office_hours?>", "countdown");
                        counter_timer("<?php echo $time?>", "run_timer");

                        if (flag_counter != "Break") {
                            counter_timer("<?php echo $total_working_hours?>", "total_working_hours");
                            counter_timer("<?php echo $working_hours?>", "countdown_working_hours");

                            remcounter_timer("<?php echo $remaining_hour?>", "rem_hours");
                            // cookie
                            var cookieName = "hrm_break";

                            if (isCookieSet(cookieName)) {
                            deleteCookie(cookieName);
                            }

                        } else {
                            counter_timer("<?php echo $break_hours?>", "countdown_break_hours");

                            // cookie
                            var cookieName = "hrm_break";
                            var currentDate = new Date();
                            if (isCookieSet(cookieName)) {
                                var cookieValue = getCookie(cookieName);
                                var cookieDate = new Date(cookieValue);
                                setInterval(function() { myFunction(); }, 60000);
                                function myFunction(){
                                    if (currentDate > cookieDate) {
                                        showNotification();
                                        deleteCookie(cookieName);
                                    }
                                }
                            } else {
                                currentDate.setMinutes(currentDate.getMinutes() + 5);
                                setCookie(cookieName, currentDate, 5);
                            }
                        }
                    }
                }    
            }
        }    

        $(".work-type label").on( "click", function() {
            $("input[name='work_submit']").show();
        } );
    });

    $(document).on('click', 'a[data-toggle="modal"]', function () {

    let modal = $($(this).attr('href'));

    let projectId = $(this).data('project-id');
    let selectedTimer = $(this).data('timer-id'); // IMPORTANT FIX

    let container = modal.find('.edit_timer_select');

    $.ajax({
        url: "{{ route('daily_work_report.show', '') }}/" + projectId,
        type: 'GET',
        success: function (res) {

            if (res && res.trim() !== '') {

                container.html(res);

                setTimeout(function () {

                    let select = container.find('select[name="timer_id"]');

                    select.val(selectedTimer).trigger('change');

                }, 100);

            }
        }
    });

});
    function setCookie(name, value, minutesToExpire) {
        const date = new Date();
        date.setTime(date.getTime() + (minutesToExpire * 60 * 1000));
        const expires = "expires=" + date.toUTCString();
        document.cookie = name + "=" + value;
    }

    function isCookieSet(cookieName) {
        var cookies = document.cookie.split(';');
        
        for (var i = 0; i < cookies.length; i++) {
            var cookie = cookies[i].trim();
            if (cookie.indexOf(cookieName + '=') === 0) {
                return true;
            }
        }
        return false;
    }

    function getCookie(name) {
        var cookies = document.cookie.split(';');

        for (var i = 0; i < cookies.length; i++) {
            var cookie = cookies[i].trim();
            if (cookie.indexOf(name + '=') === 0) {
                return cookie.substring(name.length + 1);
            }
        }
        return null;
    }

    function deleteCookie(cookieName) {
        document.cookie = cookieName + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    }
    // Usage
    function showNotification() {
        var userName = "{{ auth()->user()->name }}";
        if ('Notification' in window) {
            Notification.requestPermission().then(function (permission) {
                if (permission === 'granted') {

                    var notification = new Notification('Hello '+ userName, {
                        body: 'Please close the break time. you have taken more than a 5-minute break'
                    });
                }
            });
        } else {
            console.log('This browser does not support notifications.');
        }
    }
</script>

<script>

    jQuery(document).ready(function() {
        jQuery(document).on('submit', 'form.daily_report', function() {
            jQuery('input[type="submit"]').attr('disabled', 'disabled');
        });

        jQuery("a#clear_search").on("click", function(){
            window.location.href = window.location.origin + window.location.pathname;
        });
    });
</script>

<style type="text/css">
    .work-type{
        text-align: center;
        padding: 5px;
    }
    .work-type input[type="radio"] {
        display: none;
    }
    .work-type label {
        margin-top: 0.5rem;
        display: inline-block;
        background-color: #343a40;
        padding: 10px 20px;
        cursor: pointer;
        color: #fff;
    }
    .work-type input[type="radio"]:checked+label {
        background-color: #149aa3;
        color:#fff;
    }
    .work-type input[type="radio"]+label:hover {
        transition: transform .2s;
        background-color: #149aa3;
        color:#fff;
    }
    .work-type .custom-checkout {
        position: fixed;
        bottom: 0;
        left: 5px;
        background-color: red;
        z-index: 1;
        width: 240px;
        -webkit-animation: glowing 1500ms infinite;
        -moz-animation: glowing 1500ms infinite;
        -o-animation: glowing 1500ms infinite;
        animation: glowing 1500ms infinite;
        padding: 12px 20px;
    }
    
    @keyframes glowing {
        0% { background-color: #B20000; box-shadow: 0 0 3px #B20000; }
        25% { background-color: #FF0000; box-shadow: 0 0 3px #FF0000; }
        50% { background-color: #B20000; box-shadow: 0 0 3px #B20000; }
        75% { background-color: #FF0000; box-shadow: 0 0 3px #FF0000; }
        100% { background-color: #B20000; box-shadow: 0 0 3px #B20000; }
    }
    select2-container--default .select2-selection--multiple .select2-selection__rendered:before {
        border: none;
        content: '';
        background: url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxOS4wLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHZpZXdCb3g9Ii00NzMgMjc3IDEyIDgiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgLTQ3MyAyNzcgMTIgODsiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPHN0eWxlIHR5cGU9InRleHQvY3NzIj4NCgkuc3Qwe2ZpbGw6IzhBOTNBNjt9DQo8L3N0eWxlPg0KPHBhdGggY2xhc3M9InN0MCIgZD0iTS00NzEuNiwyNzcuM2w0LjYsNC42bDQuNi00LjZsMS40LDEuNGwtNiw2bC02LTZMLTQ3MS42LDI3Ny4zeiIvPg0KPC9zdmc+DQo=') no-repeat 0 0;
        width: 12px;
        height: 8px;
        background-size: 100% 100%;
        transform: translateY(-50%);
        position: absolute;
        right: 18px;
        top: 20px;
    }
    .not_available
    {
        cursor: not-allowed;
    }

</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />