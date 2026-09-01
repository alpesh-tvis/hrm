@extends('admin.master')

<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">

<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">

<link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">

@section('content')

@php
    
@endphp

    <div class="container-fluid">
        <div class="row mb-2">            
            @if($user->role == 2 || $employee->reporting_person == 1 || $check_department->department == '1')
                <div class="col-12 text-right">
                    <a href="{{ route('project.create') }}" class="btn btn-primary">Add</a>
                </div>
            @endif    
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ $message }}
        </div>
    @endif
<section class="content">

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    

                    <!-- View Project Modal -->
                        <div class="modal fade" id="project-view" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="">Project Details</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                
                                    <div class="modal-body">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="card card-primary card-outline card-tabs">
                                                            <div class="card-header p-0 pt-1 border-bottom-0">
                                                                <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                                                                    
                                                                    <li class="nav-item ftp-nav-item">
                                                                        <a class="nav-link" id="ftp-tab" data-toggle="pill" href="#ftp" aria-selected="false">FTP</a>
                                                                    </li>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" id="admin-tab" data-toggle="pill" href="#admin" role="tab" aria-controls="admin" aria-selected="false">Admin</a>
                                                                    </li>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" id="database-tab" data-toggle="pill" href="#database" role="tab" aria-controls="database" aria-selected="false">Database</a>
                                                                    </li>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" id="domain_hosting-tab" data-toggle="pill" href="#domain_hosting" role="tab" aria-controls="domain_hosting" aria-selected="false">Domain Host</a>
                                                                    </li>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" id="cpanel_hosting-tab" data-toggle="pill" href="#cpanel_hosting" role="tab" aria-controls="cpanel_hosting" aria-selected="false">Cpanel/Hosting</a>
                                                                    </li>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" id="timer-tab" data-toggle="pill" href="#timer" role="tab" aria-controls="timer" aria-selected="false">Upwork Timer</a>
                                                                    </li>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link active" id="description-tab" data-toggle="pill" href="#description" role="tab" aria-controls="description" aria-selected="false">Description</a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="tab-content" id="custom-tabs-three-tabContent">
                                                                    <div class="tab-pane fade" id="ftp" role="tabpanel" aria-labelledby="description-tab">
                                                                        
                                                                    </div>
                                                                    <div class="tab-pane fade" id="admin" role="tabpanel" aria-labelledby="admin-tab">
                                                                        <p></p>
                                                                    </div>
                                                                    <div class="tab-pane fade" id="database" role="tabpanel" aria-labelledby="database-tab">
                                                                        <p></p>
                                                                    </div>
                                                                    <div class="tab-pane fade" id="domain_host" role="tabpanel" aria-labelledby="domain_hosting-tab">
                                                                        <p></p>
                                                                    </div>
                                                                    <div class="tab-pane fade" id="cpanel_hosting" role="tabpanel" aria-labelledby="cpanel_hosting-tab">
                                                                        <p></p>
                                                                    </div>

                                                                    <div class="tab-pane fade" id="timer" role="tabpanel" aria-labelledby="timer-tab">
                                                                        <p></p>
                                                                    </div>
                                                                    
                                                                    <div class="tab-pane fade active show" id="description" role="tabpanel" aria-labelledby="description-tab">
                                                                        
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>  
                                    </div>   
                                </div>
                            </div>
                        <!-- End Edit Lead Modal -->
                        

                    <div class="card-body">
                        <table id="project_tbl" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Project Name</th>
                                    <th>Upwork Profiles</th>
                                    @if($user->role == 2 || $employee->reporting_person == 1 || $check_department->department == '1')
                                        <th>Assign Employee</th>
                                        <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($projects as $proj)
                                <tr>
                                    <td>{{ $proj->project_name }}</td>
                                    <td>
                                        @foreach($proj->upworkProfiles as $profile)
                                            <span class="btn btn-info">{{ $profile->first_name }}</span>
                                        @endforeach
                                    </td>
                                    @if($user->role == 2 || $employee->reporting_person == 1 || $check_department->department == '1')
                                    <td>
                                        @foreach($proj->assignedEmployees as $emp)
                                            <span class="btn btn-info">{{ $emp->first_name }} {{ $emp->last_name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <a href="{{ route('complete_project.project_complete_edit', [$proj->id]) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<!-- DataTables  & Plugins -->

<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>

<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>

<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>

<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>

<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.js')}}"></script> <!-- Modal -->




<script>

    jQuery(function ($) {

        $("#project_tbl").DataTable({
            "responsive": true, "lengthChange": false, "autoWidth": false,"pageLength": 100,
        });
    });

    /* View Cred */
    $( document ).ready(function() {
        $(document).on("click",'.project-view',function(){
            $(".nav-item:last a")[0].click();
            data_id = $(this).attr('data-id');
             $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data : { id: data_id},
                url: "/project/"+data_id,

                type: "GET",
                success: function (response) {
                    var timers = response.timer;
                    var upwork_password_details = '';
                    $.each(timers, function(index, value){
                        
                        if(value.first_name == null){
                            var first_name = '';
                        }else{
                            var first_name = '<a  class="copy_ftp" data-id="'+index+'" data-type="first_name" href="javascript:void(0)"><i class="fa fa-copy"></i></a><span style="color:#32CD32; margin-left:10px"></span>';
                        }

                        if(value.upwork_password == null){
                            var upwork_password = '';
                            var u_password = '';
                        }else{
                            var upwork_password = '<a  class="copy_ftp" data-id="'+index+'" data-type="upwork_password" href="javascript:void(0)"><i class="fa fa-copy"></i></a><span style="color:#32CD32; margin-left:10px"></span>';
                            var u_password = '********';
                        }
                        upwork_password_details += '<div class="row"><div class="col-2"><label>Username: </label></div><div class="col-10"><p>tvis-'+value.first_name.toLowerCase()+''+first_name+'</p></div></div><div class="row"><div class="col-2"><label>Password: </label></div><div class="col-10"><p>'+u_password+upwork_password+'</p></div></div><hr>';
                    });
                    $("#timer").html(upwork_password_details);
                    /* hide admin tab */
                    if(response.cred_data.admin.url == null && response.cred_data.admin.url_stg == null && response.cred_data.admin.username == null )
                    {
                        jQuery("#admin-tab").hide();
                    }else{
                        jQuery("#admin-tab").show();
                    }

                    /* hide database tab */
                    if(response.cred_data.database.password == null && response.cred_data.password_stg == null && response.cred_data.database.username == null )
                    {
                        jQuery("#database-tab").hide();
                    }else{
                        jQuery("#database-tab").show();
                    }

                    /* hide Domain host tab */
                    if(response.cred_data.domain_host.password == null && response.cred_data.domain_host.password_stg == null)
                    {
                        jQuery("#domain_hosting-tab").hide();
                    }else{
                        jQuery("#domain_hosting-tab").show();
                    }

                    /* hide Domain host tab */
                    if(response.cred_data.cpanel_hosting.password == null && response.cred_data.cpanel_hosting.password_stg == null)
                    {
                        jQuery("#cpanel_hosting-tab").hide();
                    }else{
                        jQuery("#cpanel_hosting-tab").show();
                    }
                    // console.log(response.data.project_description);
                    var description = '<div class="row"><div class="col-2"><label>Description: </label></div><div class="col-10"><p>'+nl2br(response.data.project_description);+'</p></div></div>';
                    
                    // Project Description
                    if(response.data.project_description == null){
                        $("#description div").remove();
                    }
                    else{
                        $("#description").html(description);
                    }

                    // Timer
                    if(response.timer == null || response.timer == ''){
                        $("#timer-tab").hide();
                    }
                    else{
                        $("#timer-tab").show();
                    }

                    // FTP Data
                    var encrption = '';
                    if(response.cred_data.cred.type == '1'){

                    var encrption = '<div class="row"><div class="col-2 text-right"><label>Encrption: </label></div><div class="col-10"><p>'+response.encrption+'</p></div></div>';
                    }

                    var encrption_stg = '';
                    if(response.cred_data.cred.type_stg == '1'){

                    var encrption_stg = '<div class="row"><div class="col-2 text-right"><label>Encrption: </label></div><div class="col-10"><p>'+response.encrption_stg+'</p></div></div>';
                    }

                    var putty_file = '';
                    
                    if(response.cred_data.cred.type == '3'){

                        var download = response.cred_data.cred.putty_file;
                        var downloadex = download.split('/')[1];
                        
                        var putty_file = '<div class="row"><div class="col-2 text-right"><label>Putty File: </label></div><div class="col-10"><p><a href="download_putty/'+downloadex+'">Download</a></p></div></div>';
                    }

                    var putty_file_stg = '';
                    
                    if(response.cred_data.cred.type_stg == '3'){

                        var download = response.cred_data.cred.putty_file_stg;
                        var downloadex = download.split('/')[1];
                        
                        var putty_file_stg = '<div class="row"><div class="col-2 text-right"><label>Putty File: </label></div><div class="col-10"><p><a href="download_putty/'+downloadex+'">Download</a></p></div></div>';
                    }
                    
                    var ftp_password = response.cred_data.cred.password;
                    if(ftp_password == null){
                        ftp_password = '';
                    }else{
                        ftp_password = '********';
                    }

                    var ftp_password_stg = response.cred_data.cred.password_stg;
                    if(ftp_password_stg == null){
                        ftp_password_stg = '';
                    }else{
                        ftp_password_stg = '********';
                    }

                    // var ftp = '';
                    if(response.ftpdata){
                    var ftp_sec1 = '<div class="row"><div class="col-md-6"><div class="card-header"><h3 class="card-title">Live Details</h3></div><div class="row mt-4"><div class="col-2 text-right"><label>Type: </label></div><div class="col-10"><p>'+response.ftpdata+'</p></div></div>'+encrption+'<div class="row"><div class="col-2 text-right"><label>Host: </label></div><div class="col-10"><p>'+response.cred_data.cred.host+'<a  class="copy_ftp" data-id="host" href="javascript:void(0)"><i class="fa fa-copy"></i></a><span style="color:#32CD32; margin-left:10px"></span></p></div></div><div class="row"><div class="col-2 text-right"><label>Port: </label></div><div class="col-10"><p>'+response.cred_data.cred.port+'<a  class="copy_ftp" data-id="port" href="javascript:void(0)"><i class="fa fa-copy"></i></a><span style="color:#32CD32; margin-left:10px"></span></p></div></div><div class="row"><div class="col-2 text-right"><label>User: </label></div><div class="col-10"><p>'+response.cred_data.cred.user+'<a  class="copy_ftp" data-id="user" data-type="cred" href="javascript:void(0)"><i class="fa fa-copy"></i></a><span style="color:#32CD32; margin-left:10px"></span></p></div></div><div class="row"><div class="col-2 text-right"><label>Password: </label></div><div class="col-10"><p>'+ftp_password+'<a  class="copy_ftp" data-id="password" href="javascript:void(0)"><i class="fa fa-copy"></i></a><span style="color:#32CD32; margin-left:10px"></span></p></div></div>'+putty_file+'</div>';
                    }else{
                        ftp_sec1 = '';
                    }
                    if(response.ftpdata_stg){

                        var ftp_sec2 = '<div class="col-md-6"><div class="card-header"><h3 class="card-title">Staging Details</h3></div><div class="row mt-4"><div class="col-2 text-right"><label>Type: </label></div><div class="col-10"><p>'+response.ftpdata_stg+'</p></div></div>'+encrption_stg+'<div class="row"><div class="col-2 text-right"><label>Host: </label></div><div class="col-10"><p>'+response.cred_data.cred.host_stg+'<a  class="copy_ftp" data-id="host_stg" href="javascript:void(0)"><i class="fa fa-copy"></i></a><span style="color:#32CD32; margin-left:10px"></span></p></div></div><div class="row"><div class="col-2 text-right"><label>Port: </label></div><div class="col-10"><p>'+response.cred_data.cred.port_stg+'<a  class="copy_ftp" data-id="port_stg" href="javascript:void(0)"><i class="fa fa-copy"></i></a><span style="color:#32CD32; margin-left:10px"></span></p></div></div><div class="row"><div class="col-2 text-right"><label>User: </label></div><div class="col-10"><p>'+response.cred_data.cred.user_stg+'<a  class="copy_ftp" data-id="user_stg" data-type="cred" href="javascript:void(0)"><i class="fa fa-copy"></i></a><span style="color:#32CD32; margin-left:10px"></span></p></div></div><div class="row"><div class="col-2 text-right"><label>Password: </label></div><div class="col-10"><p>'+ftp_password_stg+'<a  class="copy_ftp" data-id="password_stg" href="javascript:void(0)"><i class="fa fa-copy"></i></a><span style="color:#32CD32; margin-left:10px"></span></p></div></div>'+putty_file_stg+'</div></div>';
                    }else{
                        var ftp_sec2 = '';
                    }
                    var ftp = ftp_sec1+ftp_sec2;


                    /* Copy fields */
                    jQuery(document).ready(function () {
                        jQuery('.copy_ftp').on('click', function(){
                            var data_id = jQuery(this).attr('data-id');
                            var type = jQuery(this).parents('.tab-pane').attr('id');
                            var data_type = jQuery(this).attr('data-type');
                            var current = jQuery(this);
                            

                            // Check type
                            if(type == 'ftp'){
                                var type = 'cred';
                            }
                            if(type == 'timer'){
                                var timer = response[type][data_id][data_type];
                                var copied = navigator.clipboard.writeText(timer);
                            }else{

                                var copied = navigator.clipboard.writeText(response.cred_data[type][data_id]);
                            }
                           
                            if(copied){
                                jQuery(this).next().show();
                                // jQuery(this).next().html('Copied').delay(2000).fadeOut('medium');
                                // jQuery(this).css('margin-right','0px');
                                var cop = jQuery(this).next().html('Copied');
                                setTimeout(function() {
                                    cop.text('');
                                    // jQuery(this).css("margin-right","40px");
                                    current.next().hide();
                                    // console.log(css);
                                }, 2000);
                                
                                
                            }
                        });
                    });
                    /* Copy fields end */    
                    var admin_arr = ["admin","database","domain_host","cpanel_hosting"];

                    admin_arr.forEach(function(item) {
                        // URL
                        if(response.cred_data[item].url == '' || response.cred_data[item].url == null)
                        {
                            var url = '';
                            var url_show = '';

                        }
                        else{
                            var url = '<a  class="copy_ftp" data-id="url" href="javascript:void(0)"><i class="fa fa-copy"></i></a><span style="color:#32CD32; margin-left:10px"></span>';
                            var url_show = response.cred_data[item].url;
                        }

                        // URL Staging
                        if(response.cred_data[item].url_stg == '' || response.cred_data[item].url_stg == null)
                        {
                            var url_stg = '';
                            var url_show_stg = '';

                        }
                        else{
                            var url_stg = '<a  class="copy_ftp" data-id="url_stg" href="javascript:void(0)"><i class="fa fa-copy"></i></a><span style="color:#32CD32; margin-left:10px"></span>';
                            var url_show_stg = response.cred_data[item].url_stg;
                        } 

                        if(response.cred_data[item].username == '' || response.cred_data[item].username == null)
                        {
                            var username = '';
                            var username_show = '';    
                        }
                        else{
                            // var username = response.cred_data[item].username;
                            var username = '<a  class="copy_ftp" data-id="username" href="javascript:void(0)"><i class="fa fa-copy"></i></a><span style="color:#32CD32; margin-left:10px"></span>';
                            var username_show = response.cred_data[item].username;
                        }
                        // Username stg
                        if(response.cred_data[item].username_stg == '' || response.cred_data[item].username_stg == null)
                        {
                            var username_stg = '';
                            var username_show_stg = '';    
                        }
                        else{
                            // var username = response.cred_data[item].username;
                            var username_stg = '<a  class="copy_ftp" data-id="username_stg" href="javascript:void(0)"><i class="fa fa-copy"></i></a><span style="color:#32CD32; margin-left:10px"></span>';
                            var username_show_stg = response.cred_data[item].username_stg;
                        } 

                        if(response.cred_data[item].password == '' || response.cred_data[item].password == null)
                        {
                            var password = '';
                            var o_password = '';    
                        }
                        else{
                            // var password = response.cred_data[item].password;
                            var password = '<a  class="copy_ftp" data-id="password" href="javascript:void(0)"><i class="fa fa-copy"></i></a><span style="color:#32CD32; margin-left:10px"></span>';
                            var o_password = '********';
                        }   
                        // Password Stg
                        if(response.cred_data[item].password_stg == '' || response.cred_data[item].password_stg == null)
                        {
                            var password_stg = '';
                            var o_password_stg = '';    
                        }
                        else{
                            // var password = response.cred_data[item].password;
                            var password_stg = '<a  class="copy_ftp" data-id="password_stg" href="javascript:void(0)"><i class="fa fa-copy"></i></a><span style="color:#32CD32; margin-left:10px"></span>';
                            var o_password_stg = '********';
                        }
                        if(url_show){    
                            var admin_details1 = '<div class="row"><div class="col-md-6"><div class="card-header"><h3 class="card-title">Live Details</h3></div><div class="row mt-4"><div class="col-2 text-right"><label>URL: </label></div><div class="col-10"><p>'+url_show+' '+url+'</p></div></div><div class="row"><div class="col-2 text-right"><label>Username: </label></div><div class="col-10"><p>'+username_show+' '+username+'</p></div></div><div class="row"><div class="col-2 text-right"><label>Password: </label></div><div class="col-10"><p>'+o_password+password+'</p></div></div></div>';
                        }else{
                            admin_details1 = '';
                        }    
                        if(url_show_stg){
                            var admin_details2 = '<div class="col-md-6"><div class="card-header"><h3 class="card-title">Staging Details</h3></div><div class="row mt-4"><div class="col-2 text-right"><label>URL: </label></div><div class="col-10"><p>'+url_show_stg+' '+url_stg+'</p></div></div><div class="row"><div class="col-2 text-right"><label>Username: </label></div><div class="col-10"><p>'+username_show_stg+' '+username_stg+'</p></div></div><div class="row"><div class="col-2 text-right"><label>Password: </label></div><div class="col-10"><p>'+o_password_stg+password_stg+'</p></div></div></div></div>';
                        }else{
                            admin_details2 = '';
                        }    
                        var admin_details = admin_details1+admin_details2;
                         $("#"+item).html(admin_details);
                        
                    });

                    if(response.cred_data.cred.type  == null && response.cred_data.cred.type_stg == null ){
                        $('#ftp div').remove();
                    }
                    else{
                        $("#ftp").html(ftp);

                    }
                }
            });
            setTimeout(function(){
                jQuery.noConflict();
                $('#project-view').modal();
            }, 300);
        });    
    });
function nl2br (str, is_xhtml) {
     var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
     return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
  } 

</script>
<style type="text/css">
    .copy_ftp
    {
        margin-left: 10px;
        /*margin-right: 40px;*/

    }
    .copy_ftp i{
        font-size: 24px;
    }
    #project-view .tab-content .tab-pane .row .col-10 p{
        display: flex;
    }
    #project-view .tab-content .tab-pane .row .col-10 p .copy_ftp{
        width: 100%;
        text-align: right;
    }
    .copy_ftp+ span{
        width: 120px;
        background-color: #000;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px;
        position: absolute;
        z-index: 1;
        bottom: 120%;
        right: 0%;
        margin-left: -60px;
        display: none;
    }
</style>    

@endsection