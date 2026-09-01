@extends('admin.master')
    @section('content')
    
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        
                        @if(isset($project))
                                                   
                            <script type="text/javascript">
                            jQuery(document).ready(function($){

                                <?php if($type=='') { ?>
                                        $(".cred_port_user").hide();
                                        $(".cred_encr").hide();
                                        $(".cred_putty").hide();
                                    <?php } ?>

                                    <?php if($type=='1') { ?>
                                        $(".cred_port_user").show();
                                        $(".cred_encr").show();
                                        $(".cred_putty").hide();
                                    <?php } ?>

                                    <?php if($type=='2') { ?>
                                        $(".cred_port_user").show();
                                        $(".cred_encr").hide();
                                        $(".cred_putty").hide();
                                    <?php } ?>

                                    <?php if($type=='3') { ?>
                                        $(".cred_port_user").show();
                                        $(".cred_encr").hide();
                                        $(".cred_putty").show();
                                    <?php } ?>

                                    <?php if($type_stg=='') { ?>
                                        $(".cred_port_user_stg").hide();
                                        $(".cred_encr_stg").hide();
                                        $(".cred_putty_stg").hide();
                                    <?php } ?>

                                    <?php if($type_stg=='1') { ?>
                                        $(".cred_port_user_stg").show();
                                        $(".cred_encr_stg").show();
                                        $(".cred_putty_stg").hide();
                                    <?php } ?>

                                    <?php if($type_stg=='2') { ?>
                                        $(".cred_port_user_stg").show();
                                        $(".cred_encr_stg").hide();
                                        $(".cred_putty_stg").hide();
                                    <?php } ?>

                                    <?php if($type_stg=='3') { ?>
                                        $(".cred_port_user_stg").show();
                                        $(".cred_encr_stg").hide();
                                        $(".cred_putty_stg").show();
                                    <?php } ?>


                                jQuery('.hrm_timer').select2({
                                    placeholder: 'Choose Upwork Profile',
                                });
                                jQuery('.hrm_employee').select2({
                                    placeholder: 'Choose Employee',
                                });
                                jQuery('.bid-list').select2({
                                    placeholder: 'Choose Bid',
                                });
                                jQuery('.client-list').select2({
                                    placeholder: 'Choose Client',
                                });

                                jQuery('#cred_type').change(function() {
                                    if ($(this).val() != '') {
                                        $(".cred_host").show();
                                        $(".cred_port_user").show();
                                        $(".cred_password").show();
                                    } else {
                                        $(".cred_host").hide();
                                        $(".cred_port_user").hide();
                                        $(".cred_password").hide();
                                    }

                                    if ($(this).val() === '1') {
                                        $(".cred_encr").show();
                                    } else {
                                        $(".cred_encr").hide();
                                    }

                                    if ($(this).val() === '3') {
                                        $(".cred_putty").show();
                                    } else {
                                        $(".cred_putty").hide();
                                    }    
                                });

                            });
                        </script>
                             
                        <form action="{{ route('project.update',$project) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')   
                        @else    
                        <form action="{{ route('project.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        @endif    


                            <div class="card-body">
                                <div class="row">
                                    <h4 class="font-weight-bolder">Project Details</h4>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="project_name">Project Name*</label>
                                            <input type="text" class="form-control" id="project_name" name="project_name" value="{{ (!empty($project->project_name)) ? $project->project_name : old('project_name') }}" placeholder="Enter Project name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="project_description">Project Description</label>
                                            <textarea class="form-control" id="project_description" name="project_description" placeholder="Enter Project Description">{{ (!empty($project->project_description)) ? $project->project_description : old('project_description') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Bid</label>
                                            <select name="bid_id" class="form-control bid-list" placeholder="Select Bid">
                                                <option value="">-- Select Bid --</option>
                                                @foreach($bids as $bid)
                                                    
                                                    <option value="{{$bid->id}}" {{!empty($bidrelation->bid_id) && $bidrelation->bid_id == $bid->id ? 'selected' : ''}}>{{$bid->bid_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Client</label>
                                            <select name="client_id" class="form-control client-list" placeholder="Select Client">
                                                <option value="">-- Select Client --</option>
                                                @foreach($clients as $client)
                                                    
                                                    <option value="{{$client->id}}" {{!empty($bidrelation->client_id) && $bidrelation->client_id == $client->id ? 'selected' : ''}}>{{$client->company}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Start Date*</label>
                                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ (!empty($project->start_date)) ? $project->start_date : old('start_date') }}" >
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>End Date</label>
                                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ (!empty($project->end_date)) ? $project->end_date : old('end_date') }}" >
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="timer_id">Upwork Profile</label>

                                            <select name="timer_id[]" class="form-control hrm_timer" multiple placeholder="Choose Upwork Profile">
                                                @foreach($emp_timer as $emp)
                                                <option value="{{$emp->id}}" @if(!empty($timer_id)) {{ (in_array($emp->id, $timer_id)) ? 'selected' : '' }} @endif @if (old("timer_id")){{ (in_array($emp->id, old("timer_id")) ? "selected":"") }}@endif>{{$emp->first_name}}</option>
                                                
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="employee_id">Employee</label>
                                            <select name="employee_id[]" class="form-control hrm_employee" multiple placeholder="Choose Timer">
                                                @foreach($emp_list as $emp)
                                                    <option value="{{$emp->id}}" @if(!empty($emp_id)) {{ (in_array($emp->id, $emp_id)) ? 'selected' : ''  }} @endif @if (old("employee_id")){{ (in_array($emp->id, old("employee_id")) ? "selected":"") }}@endif>  {{$emp->first_name}}  {{$emp->last_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="live_url">Live URL</label>
                                            <input type="url" class="form-control" id="live_url" name="live_url" value="{{ (!empty($project->live_url)) ? $project->live_url : old('live_url') }}" placeholder="Enter Live URL">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="staging_url">Staging URL</label>
                                            <input type="url" class="form-control" id="staging_url" name="staging_url" value="{{ (!empty($project->staging_url)) ? $project->staging_url : old('staging_url') }}" placeholder="Enter Staging URL">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <h4 class="font-weight-bolder">Credentials Details(Live)</h4>
                                    </div>
                                    <div class="col-md-6">
                                        <h4 class="font-weight-bolder">Credentials Details(Stg)</h4>
                                    </div>
                                        
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="cred_type">Type</label>
                                                    <select name="cred_type" id="cred_type" class="form-control" placeholder="Select Type">
                                                        <option value="">--- Select ---</option>
                                                        <option value="1" @if(!empty($type)) {{ $type =='1' ? 'selected' : ''}}@endif @if (old('cred_type') == "1") {{ 'selected' }} @endif>FTP</option>
                                                        <option value="2" @if(!empty($type)) {{ $type =='2' ? 'selected' : ''}} @endif @if (old('cred_type') == "2") {{ 'selected' }} @endif>SFTP</option>
                                                        <option value="3" @if(!empty($type)) {{ $type =='3' ? 'selected' : ''}} @endif @if (old('cred_type') == "3") {{ 'selected' }} @endif>SSH</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 cred_encr">
                                                <div class="form-group">
                                                    <label>Encrption*</label>
                                                    <select name="cred_encr" id="cred_encr" class="form-control" placeholder="Select Encrption">
                                                        <option value="">--- Select ---</option>
                                                        <option value="1" @if(!empty($cred_encrption)) {{ $cred_encrption =='1' ? 'selected' : ''}} @endif @if (old('cred_encr') == "1") {{ 'selected' }} @endif>Use explicit FTP over TLS if available</option>
                                                        <option value="2" @if(!empty($cred_encrption)) {{ $cred_encrption =='2' ? 'selected' : ''}} @endif @if (old('cred_encr') == "2") {{ 'selected' }} @endif>Require explicit FTP over TLS</option>
                                                        <option value="3" @if(!empty($cred_encrption)) {{ $cred_encrption =='3' ? 'selected' : ''}} @endif @if (old('cred_encr') == "3") {{ 'selected' }} @endif>Require implicit FTP over TLS</option>
                                                        <option value="4" @if(!empty($cred_encrption)) {{ $cred_encrption =='4' ? 'selected' : ''}} @endif @if (old('cred_encr') == "4") {{ 'selected' }} @endif>Only use plain FTP(insecure)</option>
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 cred_putty">
                                                <div class="form-group">
                                                    <label>Putty File*</label>
                                                    <input type="file" name="cred_putty" id="cred_putty" value="" class="form-control">

                                                    @php
                                                        if(!empty($cred_putty_file)){
                                                        $cred_putty_file_url = explode('/',$cred_putty_file);
                                                        }
                                                    @endphp
                                                   @if(!empty($cred_putty_file))
                                                    <a href="{{route('download_putty',$cred_putty_file_url[1])}}">Download</a>
                                                   @endif 
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row cred_port_user">
                                            <div class="col-md-6 cred_host">
                                                <div class="form-group">
                                                    <label>Host*</label>
                                                    <input type="text" name="cred_host" id="cred_host" value="{{ (!empty($cred_host)) ? $cred_host : old('cred_host')}}" class="form-control" placeholder="Enter Host">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Port*</label>
                                                    <input type="text" name="cred_port" id="cred_port" value="{{ (!empty($cred_port)) ? $cred_port : old('cred_port') }}" class="form-control" placeholder="Enter Port">
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="row cred_port_user" >
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>User*</label>
                                                    <input type="text" name="cred_user" id="cred_user" value="{{ (!empty($cred_user)) ? $cred_user : old('cred_user') }}" class="form-control" placeholder="Enter User">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Password*</label>
                                                    <input type="text" name="cred_password" id="cred_password" value="{{ (!empty($cred_password)) ? $cred_password : old('cred_password')}}" class="form-control" placeholder="Enter Password">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="cred_type">Type</label>
                                                    <select name="cred_type_stg" id="cred_type_stg" class="form-control" placeholder="Select Type">
                                                        <option value="">--- Select ---</option>
                                                        <option value="1" @if(!empty($type_stg)) {{ $type_stg =='1' ? 'selected' : ''}}@endif @if (old('cred_type_stg') == "1") {{ 'selected' }} @endif>FTP</option>
                                                        <option value="2" @if(!empty($type_stg)) {{ $type_stg =='2' ? 'selected' : ''}} @endif @if (old('cred_type_stg') == "2") {{ 'selected' }} @endif>SFTP</option>
                                                        <option value="3" @if(!empty($type_stg)) {{ $type_stg =='3' ? 'selected' : ''}} @endif @if (old('cred_type_stg') == "3") {{ 'selected' }} @endif>SSH</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 cred_encr_stg">
                                                <div class="form-group">
                                                    <label>Encrption*</label>
                                                    <select name="cred_encr_stg" id="cred_encr_stg" class="form-control" placeholder="Select Encrption">
                                                        <option value="">--- Select ---</option>
                                                        <option value="1" @if(!empty($cred_encrption_stg)) {{ $cred_encrption_stg =='1' ? 'selected' : ''}} @endif @if (old('cred_encr_stg') == "1") {{ 'selected' }} @endif>Use explicit FTP over TLS if available</option>
                                                        <option value="2" @if(!empty($cred_encrption_stg)) {{ $cred_encrption_stg =='2' ? 'selected' : ''}} @endif @if (old('cred_encr_stg') == "2") {{ 'selected' }} @endif>Require explicit FTP over TLS</option>
                                                        <option value="3" @if(!empty($cred_encrption_stg)) {{ $cred_encrption_stg =='3' ? 'selected' : ''}} @endif @if (old('cred_encr_stg') == "3") {{ 'selected' }} @endif>Require implicit FTP over TLS</option>
                                                        <option value="4" @if(!empty($cred_encrption_stg)) {{ $cred_encrption_stg =='4' ? 'selected' : ''}} @endif @if (old('cred_encr_stg') == "4") {{ 'selected' }} @endif>Only use plain FTP(insecure)</option>
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 cred_putty_stg">
                                                <div class="form-group">
                                                    <label>Putty File*</label>
                                                    <input type="file" name="cred_putty_stg" id="cred_putty_stg" value="" class="form-control">

                                                    @php
                                                        if(!empty($cred_putty_file_stg)){
                                                        $cred_putty_file_url = explode('/',$cred_putty_file_stg);
                                                        }
                                                    @endphp
                                                   @if(!empty($cred_putty_file_stg))
                                                    <a href="{{route('download_putty',$cred_putty_file_url[1])}}">Download</a>
                                                   @endif 
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row cred_port_user_stg">
                                            <div class="col-md-6 cred_host_stg">
                                                <div class="form-group">
                                                    <label>Host*</label>
                                                    <input type="text" name="cred_host_stg" id="cred_host_stg" value="{{ (!empty($cred_host_stg)) ? $cred_host_stg : old('cred_host_stg')}}" class="form-control" placeholder="Enter Host">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Port*</label>
                                                    <input type="text" name="cred_port_stg" id="cred_port_stg" value="{{ (!empty($cred_port_stg)) ? $cred_port_stg : old('cred_port_stg') }}" class="form-control" placeholder="Enter Port">
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="row cred_port_user_stg" >
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>User*</label>
                                                    <input type="text" name="cred_user_stg" id="cred_user_stg" value="{{ (!empty($cred_user_stg)) ? $cred_user_stg : old('cred_user_stg') }}" class="form-control" placeholder="Enter User">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Password*</label>
                                                    <input type="text" name="cred_password_stg" id="cred_password_stg" value="{{ (!empty($cred_password_stg)) ? $cred_password_stg : old('cred_password_stg')}}" class="form-control" placeholder="Enter Password">
                                                </div>
                                            </div>
                                        </div>
                                    </div>    

                                </div>    
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <h4 class="font-weight-bolder">Admin Details(Live)</h4>
                                    </div>
                                    <div class="col-md-6">
                                        <h4 class="font-weight-bolder">Admin Details(Stg)</h4>
                                    </div>
                                </div>
                                <div class="row">
                                        <div class="col-md-6">
                                            <div class=" row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="admin_url">URL</label>
                                                        <input type="url" class="form-control" id="admin_url" name="admin_url" value="{{ (!empty($admin_url)) ? $admin_url :  old('admin_url') }}" placeholder="Enter URL">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="admin_username">Username</label>
                                                        <input type="text" class="form-control" id="admin_username" name="admin_username" value="{{(!empty($admin_username)) ? $admin_username : old('admin_username')}}" placeholder="Enter Username">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="admin_password">Password</label>
                                                        <input type="text" class="form-control" id="admin_password" name="admin_password" value="{{ (!empty($admin_password)) ? $admin_password : old('admin_password')}}" placeholder="Enter Password">
                                                    </div>
                                                </div>
                                            </div>    
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="admin_url">URL</label>
                                                        <input type="url" class="form-control" id="admin_url_stg" name="admin_url_stg" value="{{ (!empty($admin_url_stg)) ? $admin_url_stg :  old('admin_url_stg') }}" placeholder="Enter URL">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="admin_username">Username</label>
                                                        <input type="text" class="form-control" id="admin_username_stg" name="admin_username_stg" value="{{ (!empty($admin_username_stg)) ? $admin_username_stg : old('admin_username_stg') }}" placeholder="Enter Username">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="admin_password">Password</label>
                                                        <input type="text" class="form-control" id="admin_password_stg" name="admin_password_stg" value="{{ (!empty($admin_password_stg)) ? $admin_password_stg : old('admin_password_stg')}}" placeholder="Enter Password">
                                                    </div>
                                                </div>
                                            </div>    
                                        </div>
                                </div>
                                    
                                

                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <h4 class="font-weight-bolder">Database Details(Live)</h4>
                                    </div>
                                    <div class="col-md-6">
                                        <h4 class="font-weight-bolder">Database Details(Stg)</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="database_url">URL</label>
                                                    <input type="url" class="form-control" id="database_url" name="database_url" value="{{ (!empty($database_url)) ? $database_url : old('database_url')}}" placeholder="Enter URL">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="database_username">Username</label>
                                                    <input type="text" class="form-control" id="database_username" name="database_username" value="{{ (!empty($database_username)) ? $database_username : old('database_username') }}" placeholder="Enter Username">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="database_password">Password</label>
                                                    <input type="text" class="form-control" id="database_password" name="database_password" value="{{ (!empty($database_password)) ? $database_password : old('database_password') }}" placeholder="Enter Password">
                                                </div>
                                            </div>
                                        </div>    
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="database_url">URL</label>
                                                    <input type="url" class="form-control" id="database_url_stg" name="database_url_stg" value="{{ (!empty($database_url_stg)) ? $database_url_stg : old('database_url_stg')}}" placeholder="Enter URL">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="database_username">Username</label>
                                                    <input type="text" class="form-control" id="database_username_stg" name="database_username_stg" value="{{ (!empty($database_username_stg)) ? $database_username_stg : old('database_username_stg') }}" placeholder="Enter Username">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="database_password">Password</label>
                                                    <input type="text" class="form-control" id="database_password_stg" name="database_password_stg" value="{{ (!empty($database_password_stg)) ? $database_password_stg : old('database_password_stg') }}" placeholder="Enter Password">
                                                </div>
                                            </div>
                                        </div>    
                                    </div>       
                                    
                                </div> 

                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <h4 class="font-weight-bolder">Domain Host Details(Live)</h4>
                                    </div>
                                    <div class="col-md-6">
                                        <h4 class="font-weight-bolder">Domain Host Details(Stg)</h4>
                                    </div>    
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="domain_url">URL</label>
                                                    <input type="url" class="form-control" id="domain_url" name="domain_url" value="{{ (!empty($domain_host_url)) ? $domain_host_url : old('domain_url') }} " placeholder="Enter URL">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="domain_username">Username</label>
                                                    <input type="text" class="form-control" id="domain_username" name="domain_username" value="{{ (!empty($domain_host_username)) ? $domain_host_username : old('domain_username') }}" placeholder="Enter Username">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="domain_password">Password</label>
                                                    <input type="text" class="form-control" id="domain_password" name="domain_password" value="{{ (!empty($domain_host_password)) ? $domain_host_password : old('domain_password') }}" placeholder="Enter Password">
                                                </div>
                                            </div>
                                        </div>    
                                    </div>
                                     <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="domain_url">URL</label>
                                                    <input type="url" class="form-control" id="domain_url_stg" name="domain_url_stg" value="{{ (!empty($domain_host_url_stg)) ? $domain_host_url_stg : old('domain_url_stg') }} " placeholder="Enter URL">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="domain_username">Username</label>
                                                    <input type="text" class="form-control" id="domain_username_stg" name="domain_username_stg" value="{{ (!empty($domain_host_username_stg)) ? $domain_host_username_stg : old('domain_username_stg') }}" placeholder="Enter Username">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="domain_password">Password</label>
                                                    <input type="text" class="form-control" id="domain_password_stg" name="domain_password_stg" value="{{ (!empty($domain_host_password_stg)) ? $domain_host_password_stg : old('domain_password_stg') }}" placeholder="Enter Password">
                                                </div>
                                            </div>
                                        </div>    
                                    </div>      
                                    
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <h4 class="font-weight-bolder">Cpanel/Hosting Details(Live)</h4>
                                    </div>
                                    <div class="col-md-6">
                                        <h4 class="font-weight-bolder">Cpanel/Hosting Details(Stg)</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="cpanel_url">URL</label>
                                                    <input type="text" class="form-control" id="cpanel_url" name="cpanel_url" value="{{ (!empty($cpanel_hosting_url)) ? $cpanel_hosting_url :  old('cpanel_url') }}" placeholder="Enter URL">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="cpanel_username">Username</label>
                                                    <input type="text" class="form-control" id="cpanel_username" name="cpanel_username" value="{{ (!empty($cpanel_hosting_username)) ? $cpanel_hosting_username : old('cpanel_username')}}" placeholder="Enter Username">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="cpanel_password">Password</label>
                                                    <input type="text" class="form-control" id="cpanel_password" name="cpanel_password" value="{{ (!empty($cpanel_hosting_password)) ? $cpanel_hosting_password : old('cpanel_password') }}" placeholder="Enter Password">
                                                </div>
                                            </div>
                                        </div>    
                                    </div>  
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="cpanel_url">URL</label>
                                                    <input type="text" class="form-control" id="cpanel_url_stg" name="cpanel_url_stg" value="{{ (!empty($cpanel_hosting_url_stg)) ? $cpanel_hosting_url_stg :  old('cpanel_url_stg') }}" placeholder="Enter URL">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="cpanel_username">Username</label>
                                                    <input type="text" class="form-control" id="cpanel_username_stg" name="cpanel_username_stg" value="{{ (!empty($cpanel_hosting_username_stg)) ? $cpanel_hosting_username_stg : old('cpanel_username_stg') }}" placeholder="Enter Username">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="cpanel_password">Password</label>
                                                    <input type="text" class="form-control" id="cpanel_password_stg" name="cpanel_password_stg" value="{{ (!empty($cpanel_hosting_password_stg)) ? $cpanel_hosting_password_stg : old('cpanel_password_stg')}}" placeholder="Enter Password">
                                                </div>
                                            </div>


                                        </div>    
                                    </div>
                                </div>

                                <div class="row">
                                   <div class="col-md-6">
                                        <label for="project_name">Service Status</label>
                                        <div class="form-group">
                                           <input class="checkbox_serviceitem"  type="checkbox" name="service_status" id="service_start_status" value="1" @if($project->service_status == 1) checked @endif> Active
                                            <br />
                                            <!-- <input class="checkbox_serviceitem"  type="checkbox" name="service_status" id="service_end_status" value="0" @if($project->service_status == 0) checked @endif> 
                                            Deactive -->
                                        </div>
                                   </div>
                                   <div class="col-md-6">
                                       
                                   </div>
                                </div>

                                <div class="card-footer">
                                   <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                </div>
            </div>
        </div>
    </section>
    @endsection
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
    <script type="text/javascript">
        jQuery(document).ready(function($){

            //Service status check
            $('input.checkbox_serviceitem').on('change', function() {
                $('input.checkbox_serviceitem').not(this).prop('checked', false);  
            });
            $("#service_start_status").change(function() {
                var is_checked = $(this).is(":checked");
                if(is_checked) {
                 $("#end_date").val(0);
                }
                 $("#end_date").prop("readonly", !is_checked);
            });
            /*$("#service_end_status").change(function() {
                var d = new Date();
                var month = d.getMonth()+1;
                var day = d.getDate();
                var output = d.getFullYear() + '-' +
                ((''+month).length<2 ? '0' : '') + month + '-' +
                ((''+day).length<2 ? '0' : '') + day;
                var currentdate = $('#end_date').val(output);
            });*/

            $(".cred_port_user").hide();
            $(".cred_port_user_stg").hide();
            $(".cred_encr").hide();
            $(".cred_encr_stg").hide();
            $(".cred_putty").hide();
            $(".cred_putty_stg").hide();
            <?php 
            if(old('cred_type') !== null){ ?>
                $(".cred_port_user").show();
                
            <?php }

            if(old('cred_type') == '1'){ ?>
                $(".cred_encr").show();
            <?php }

            if(old('cred_type') == '3'){ ?>
                $(".cred_putty").show();
            <?php }

            if(old('cred_type_stg') !== null){ ?>
                $(".cred_port_user_stg").show();
                
            <?php }

            if(old('cred_type_stg') == '1'){ ?>
                $(".cred_encr_stg").show();
            <?php }

            if(old('cred_type_stg') == '3'){ ?>
                $(".cred_putty_stg").show();
            <?php }

            ?>
            
            
            jQuery('.hrm_timer').select2({
                placeholder: 'Choose Upwork Profile',
            });
            jQuery('.hrm_employee').select2({
                placeholder: 'Choose Employee',
            });
            jQuery('.bid-list').select2({
                placeholder: 'Choose Bid',
            });
            jQuery('.client-list').select2({
                placeholder: 'Choose Client',
            });

            jQuery('#cred_type').change(function() {
                if ($(this).val() != '') {
                    $(".cred_host").show();
                    $(".cred_port_user").show();
                    $(".cred_password").show();

                }else{
                    $(".cred_host").hide();
                    $(".cred_port_user").hide();
                    $(".cred_password").hide();
                }

                if ($(this).val() === '1') {
                    $(".cred_encr").show();
                }
                else{
                    $(".cred_encr").hide();
                }

                if ($(this).val() === '3') {
                    $(".cred_putty").show();
                }
                else{
                    $(".cred_putty").hide();
                }    
                
            });

            // Staging
            jQuery('#cred_type_stg').change(function() {
                if ($(this).val() != '') {
                    $(".cred_host_stg").show();
                    $(".cred_port_user_stg").show();
                    $(".cred_password_stg").show();

                }else{
                    $(".cred_host_stg").hide();
                    $(".cred_port_user_stg").hide();
                    $(".cred_password_stg").hide();
                }

                if ($(this).val() === '1') {
                    $(".cred_encr_stg").show();
                }
                else{
                    $(".cred_encr_stg").hide();
                }

                if ($(this).val() === '3') {
                    $(".cred_putty_stg").show();
                }
                else{
                    $(".cred_putty_stg").hide();
                }    
                
            });
            
        });
    </script>
    <style type="text/css">
        .select2-container--default .select2-selection--multiple .select2-selection__rendered:before {
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

    </style>