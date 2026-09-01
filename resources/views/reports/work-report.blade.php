@extends('admin.master')

@section('content')
    <section class="content workReport-act">
        <div class="container-fluid">
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

                    @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="card card-primary">
                        <form id="report_form" class="form-horizontal">
                            <div class="">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2  ">
                                            <label for="select_task">Select task</label>
                                            <select name="select_task" id="select_task" class="form-control">
                                                <option>-- Select --</option>
                                                <option>General Activity</option>
                                                <option>Project Work</option>
                                                <option>Break</option>
                                            </select>    
                                        </div>  
                                        <div class="col-md-2 projects" style="display:none">
                                            <div class="form-group">
                                                <label for="bid_name">Projects</label>
                                                <select name="projects" id="projects" class="form-control" multiple>
                                                    <option value="all">-- Select All --</option>
                                                    @foreach($project_name as $project)
                                                        <option value="{{$project->id}}">{{$project->project_name}}</option>
                                                    @endforeach
                                                    <option value="o1" style="color: #149aa3;">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2 general_activity" style="display:none">
                                            <div class="form-group">
                                                <label for="general_activity">General Activity</label>
                                                <select name="activity_type" id="activity_type" class="form-control" multiple>
                                                    <option value="all">-- Select All --</option>
                                                    <option value="1">Mail Check</option>
                                                    <option value="2">Event</option>
                                                    <option value="3">Interview</option>
                                                    <option value="4">skill  improvement</option>
                                                    <option value="5">Free</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2 break_activity" style="display:none">
                                            <div class="form-group">
                                                <label for="break_activity">Break</label>
                                                <select name="break_activity" id="break_activity" class="form-control" multiple>
                                                    <option value="all">-- Select All --</option>
                                                    <option value="1">Lunch</option>
                                                    <option value="2">Water</option>
                                                    <option value="3">Washroom</option>
                                                    <option value="4">Call</option>
                                                    <option value="5">Sleep</option>
                                                    <option value="6">Snack</option>
                                                    <option value="7">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 p_activity" style="display:none">
                                            <div class="form-group">
                                                <label for="emp_ids">Activities</label>
                                                <select class="form-control" id="activity_type1" name="activity_type1"  multiple>
                                                    <option value="all">-- Select Activity --</option>
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

                                        @if($emp->reporting_person == '1')
                                            <div class="col-md-2 user_list" style="display:none">
                                                <div class="form-group">
                                                    <label for="employee">Employees</label>
                                                    <select name="employee" id="employee" class="form-control" multiple>
                                                        <option value="all">-- Select All --</option>
                                                        @foreach($users as $user)
                                                            <option value="{{$user->id}}">{{$user->first_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="col-md-2 select_date" style="display:none">
                                            <div class="form-group">
                                                <label for="date_range" style="width:100%;">Select Date</label>
                                                <input type="text" name="report_date" value="" class="mb-2 form-control" />
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2 get_reports" style="display:none">
                                            <div class="form-group">
                                                <label for="bid_submit">&nbsp;</label>
                                                <button type="submit"  id="get_reports" class="btn btn-primary form-control">Get Reports</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" class="task_selection" name="task_selection" value="">
                        </form>
                    </div>
                          
                    <div class="card card-primary">
                        <div class="card-body">
                            <table id="work-report" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <?php if($emp->reporting_person == '1') { ?>
                                            <th>Name</th>
                                        <?php } ?>
                                        <th>Activity</th>
                                        <th>Description</th>
                                        <th>Work Hours</th>
                                        <th>Work Date</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                
                                <tfoot>
                                    <tr>
                                         <?php if($emp->reporting_person == '1') { ?>
                                        <th>Name</th>
                                        <?php } ?>
                                        <th>Activity</th>
                                        <th>Description</th>
                                        <th>Work Hours</th>
                                        <th>Work Date</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js')}}"></script>
    <script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>

    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script> 
    <script src="{{asset('plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>

    <script src="{{asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>

    <script src="{{asset('plugins/jszip/jszip.min.js')}}"></script>

    <script src="{{asset('plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>

     
    <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.js')}}"></script> <!-- Modal -->
    
    <script type="text/javascript">
        function sumTimeColumn() {
            <?php
                if($emp->reporting_person == '1') { ?>
                    var timeCells = document.querySelectorAll('#work-report tbody td:nth-child(4)'); <?php
                } else { ?>
                    var timeCells = document.querySelectorAll('#work-report tbody td:nth-child(3)'); <?php
                }
            ?>    

            var totalSeconds = 0;

            timeCells.forEach(function(cell) {
                var timeParts = cell.textContent.split(':');
                var hours = parseInt(timeParts[0], 10);
                var minutes = parseInt(timeParts[1], 10);
                var seconds = parseInt(timeParts[2], 10);

                totalSeconds += hours * 3600 + minutes * 60 + seconds;
            });

            var hours = Math.floor(totalSeconds / 3600);
            var minutes = Math.floor((totalSeconds % 3600) / 60);
            var seconds = totalSeconds % 60;

            var sumWorkHours = hours.toString().padStart(2, '0') + ':' + minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0');

            return sumWorkHours;
        }

        jQuery(function ($) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });

        $(document).ready(function(){
            jQuery('input[name="report_date"]').daterangepicker({
                opens: 'center',
                maxDate: new Date(),
                locale: {
                    format: 'DD/MM/YYYY'
                }
            });
        }); 
    
        <?php
            if($emp->reporting_person == '1') { ?>
                $('#projects').on('change', function() {
                    project_id = $(this).val();

                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: {project_id : project_id},
                        url: "{{ route('work_reports.index') }}",
                        type:'GET',
                        beforeSend: function (argument) {
                            jQuery('.user_list').addClass('disable_text');
                        },
                        success: function (response) {
                            jQuery('.user_list').removeClass('disable_text');
                            $("#employee").html('');
                            $('#employee').append('<option value="all">-- Select All --</option>');
                            $.each(response.data, function (key, value) {
                                var options ="<option value="+value.id+">"+value.name+"</option>";
                                    $('#employee').append(options);
                            });
                        }
                    });
                }); <?php
        } ?>
        
        var table = $('#work-report').DataTable({
            "order": [],
            "paging": true,
            "pageLength": 500,
            "dom": 'Bfrtip',
            "processing": "true",
            "columns": [
                <?php
                    if($emp->reporting_person == '1') { ?>
                        { data: 'name', name: 'name' }, <?php
                    }
                ?>
                    
                {
                    data: null,
                    render: function(data, type, row) {
                        if(row['help_person']){
                            
                            return row['project'] + ' - ' + row['type'] + ' - ' + row['help_person'];
                        }
                        else {
                            if(row['timer'] == 'Without Timer'){
                                return row['project'] + ' - ' + row['type'] + '<small class="badge badge-danger p-2">'+ row['timer']+'</small>';
                            }else{    
                                return row['project'] + ' - ' + row['type'] + '<small class="badge badge-success p-2">'+ row['timer']+'</small>';
                            }    
                        }
                    }
                },
                { data: 'description', name: 'description' },
                { data: 'total_work_time', name: 'total_work_time' },
                { data: 'work_date', name: 'work_date' }
            ],
            buttons: [
                'csv', 'excel'
            ]
        });

        $('#report_form').on('submit',function(e){
            e.preventDefault();

            // var project = $('#projects option:selected').val();
            var project = $('#projects option:selected').map(function() {
                return this.value; 
            }).get();
            // var employee = $('#employee option:selected').val();
            var employee = $('#employee option:selected').map(function() {
                return $(this).val();
            }).get();
            var report_date = $('input[name="report_date"]').val();
            // var report_activities = $('#activity_type1 option:selected').val();
            var report_activities = $('#activity_type1 option:selected').map(function() {
                return $(this).val();
            }).get();
            // var activity_type = $('#activity_type option:selected').val();
            var activity_type = $('#activity_type option:selected').map(function() {
                return $(this).val();
            }).get();
            // var break_activity = $('#break_activity option:selected').val();
            var break_activity = $('#break_activity option:selected').map(function() {
                return $(this).val();
            }).get();
            var task_selection = $('.task_selection').val();
            
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
                    project : project,
                    employee:employee,
                    report_date : report_date,
                    report_activities : report_activities,
                    activity_type : activity_type,
                    break_activity : break_activity,
                    task : task_selection

                },
                url: "{{ route('work_reports.index') }}",
                type:'GET',
                beforeSend: function (argument) {
                    jQuery('.spinner-wrapper').show();
                },
                success: function (response) {
                    table.clear().draw();
                    table.rows.add(response.data).draw();
                    var totalWorkHours = sumTimeColumn();
                    <?php
                        if($emp->reporting_person == '1') { ?>
                            if (!jQuery("#work-report_filter label").hasClass("total-hours")) {
                                jQuery("#work-report_filter label").before('<label class="total-hours" style="float:left;"><b>Total Hours</b> : '+totalWorkHours+'</label>');
                            }else{
                                jQuery("#work-report_filter label.total-hours").html('<label class="total-hours" style="float:left;"><b>Total Hours</b> : '+totalWorkHours+'</label>');
                            } <?php
                        }
                        else 
                        {  ?> 
                            if (!jQuery("#work-report_filter label").hasClass("total-hours")) {
                                jQuery("#work-report_filter label").before('<label class="total-hours" style="float:left;"><b>Total Hours</b> : '+totalWorkHours+'</label>');
                            } else {
                                jQuery("#work-report_filter label.total-hours").html('<label class="total-hours" style="float:left;"><b>Total Hours</b> : '+totalWorkHours+'</label>');
                            } <?php 
                        }
                    ?>   
                    jQuery('.spinner-wrapper').hide();
                }
            });
        });    
    </script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" /> <!-- select2 -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> <!-- date range picker -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> <!-- select2 -->



    <style type="text/css">
        .disable_text {
              opacity: 0.5;
            pointer-events: none;
        }
        .cb-dropdown-wrap {
          max-height: 80px; 
          position: relative;
          height: 30px;
          width: max-content;
        }

        .cb-dropdown,
        .cb-dropdown li {
          margin: 0 5px;
          padding: 0;
          list-style: none;
        }

        .cb-dropdown {
          position: relative;
          z-index: 1;
          width: 100%;
          height: 100%;
          overflow: hidden;
          background: #fff;
          border: 1px solid #888;
        }

        /* For selected filter. */
        
        .cb-dropdown-wrap:hover .cb-dropdown {
          height: auto;
          overflow: auto;
          max-height: 200px;
          transition: 0.2s height ease-in-out;
        }

        /* For selected items. */
        .cb-dropdown li.active {
          background: #6c757d;
        }
        .cb-dropdown li.active label span
        {
            color: #fff;
        }

        .cb-dropdown li label {
          display: block;
          position: relative;
          cursor: pointer;
          line-height: 2; 
          margin: 0;
        }

        .cb-dropdown li label > input {
          position: absolute;
          left: 0;
          top: 6px !important;
          width: 16px;
        }

        .cb-dropdown li label > span {
          display: block;
          margin-left: 20px;
          margin-right: 0px; 
          font-family: sans-serif;
          font-size: 0.8em;
          font-weight: normal;
          text-align: left;
        }

        
        .table-fit {
            width: 1px;
        }
        .dataTables_scrollHead{
            overflow: inherit !important;
        }
        .cb-dropdown-wrap{
            height: 30px;
        }
        
        .cb-dropdown-wrap .cb-dropdown {
           margin: 0;
        }
        .dataTables_scrollBody table {
           width: 100%;
        }
        .show_row
        {
            display: table-row !important;
        }
        .cb-dropdown li label > span.b 
        {
            display: none;
        }
        th.sales_person, td.sales_person{
            display: none;
        }
        .select2-container{
            width: 100% !important;
        } 
        .select2-selection.select2-selection--single{
            height: auto !important;
        }
    </style>
    <script type="text/javascript">
        jQuery("select[name='projects']").on("change", function() {
            var select_project_activitiy = $('#projects option:selected').val();
            if(select_project_activitiy != 'all'){
                jQuery(".p_activity").show();
            }else{
                jQuery(".p_activity").show();
            }
        });
        jQuery("select[name='select_task']").on("change", function() {
            var select_project_activitiy = $('#select_task option:selected').text();
            
            if(select_project_activitiy == "General Activity"){
                jQuery(".row .general_activity").show();
                jQuery(".row .select_date").show();
                jQuery(".row .get_reports").show();
                jQuery(".row .break_activity").hide();
                jQuery(".row .projects").hide();
                jQuery(".task_selection").val(1);
                jQuery(".user_list").show();
                jQuery(".p_activity").hide();
                jQuery('.general_activity option:selected').prop('selected', false);
            }

            else if(select_project_activitiy == "Project Work"){
                jQuery(".row .projects").show();
                jQuery(".row .select_date").show();
                jQuery(".row .get_reports").show();
                jQuery(".row .break_activity").hide();
                jQuery(".row .general_activity").hide();
                jQuery(".task_selection").val(2);
                jQuery(".user_list").show();
                jQuery('#projects option:selected').prop('selected', false);
                jQuery('#activity_type1 option:selected').prop('selected', false);
            }

            else if(select_project_activitiy == "Break"){
                jQuery(".row .select_date").show();
                jQuery(".row .get_reports").show();
                jQuery(".row .break_activity").show();
                jQuery(".row .projects").hide();
                jQuery(".row .general_activity").hide();
                jQuery(".task_selection").val(3);
                jQuery(".user_list").show();
                jQuery(".p_activity").hide();
                jQuery('#break_activity option:selected').prop('selected', false);
            }else{
                jQuery(".row .select_date").hide();
                jQuery(".row .get_reports").hide();
                jQuery(".row .break_activity").hide();
                jQuery(".row .projects").hide();
                jQuery(".row .general_activity").hide();
                jQuery(".task_selection").val();
                jQuery(".user_list").hide();
                jQuery(".p_activity").hide();
            }

            
        });

        jQuery('#activity_type').on('change', function() {
            var selectedValues = $('#activity_type').val();

            if (selectedValues.includes("all")) {
                $('#activity_type option').not('[value="all"]').prop('selected', false);
            } else {
                $('#activity_type option[value="all"]').prop('selected', false);
            }
        });

        jQuery('#employee').on('change', function() {
            var selectedValues = $('#employee').val();

            if (selectedValues.includes("all")) {
                $('#employee option').not('[value="all"]').prop('selected', false);
            } else {
                $('#employee option[value="all"]').prop('selected', false);
            }
        });
        jQuery('#break_activity').on('change', function() {
            var selectedValues = $('#break_activity').val();

            if (selectedValues.includes("all")) {
                $('#break_activity option').not('[value="all"]').prop('selected', false);
            } else {
                $('#break_activity option[value="all"]').prop('selected', false);
            }
        });
        jQuery('#projects').on('change', function() {
            var selectedValues = $('#projects').val();

            if (selectedValues.includes("all")) {
                $('#projects option').not('[value="all"]').prop('selected', false);
            } else {
                $('#projects option[value="all"]').prop('selected', false);
            }
        });
    </script>
@endsection