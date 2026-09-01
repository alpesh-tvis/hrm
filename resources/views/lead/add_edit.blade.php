@extends('admin.master')
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 ">
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
                        <form action="{{route('bids.store')}}" method="POST" class="form-horizontal">
                            @csrf
                            <input type="hidden" name="lead_id_post" value="" id="lead_id_post">
                            <div class="">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="bid_name">Bid Title</label>
                                                <input type="text" class="form-control" name="bid_name" value="{{old('bid_name')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="bid_url">Bid URL</label>
                                                <input type="url" class="form-control" name="bid_url" value="{{old('bid_url')}}" >
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Bid Sources</label>
                                                <select name="bid_source" id="bid_source_edit" class="form-control">
                                                    <option value="">-- Select Sources --</option>
                                                    <option value="1">Upwork</option>
                                                    <option value="2">LinkedIn</option>
                                                    <option value="3">Clutch</option>
                                                    <option value="4">Freelancer</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="bid_date">Bid Date</label>
                                                <input type="date" class="form-control" name="bid_date" id="bid_date" value="<?php echo date('Y-m-d'); ?>" >
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="bid_submit">&nbsp;</label>
                                                <button type="submit"  class="btn btn-primary form-control">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>       
                    <div class="card card-primary">
                        <!-- Add  Lead Modal -->
                        <div class="modal fade" id="add_lead" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="">Bid Details</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <!-- Form -->
                                    <form name="lead_post" id="lead_post" class="form-horizontal">
                                        <div class="modal-body">
                                            <!-- Error message -->
                                            <div class="alert alert-danger print-error-msg" style="display:none">
                                                <ul></ul>
                                            </div>
                                            <div class="print-success-msg alert alert-success" style="display:none">
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="bid_name">Bid Title</label>
                                                            <input type="text" class="form-control" id="bid_name" name="bid_name" value="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="bid_url">Bid URL</label>
                                                            <input type="url" class="form-control" id="bid_url" name="bid_url" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" id="lead_submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </form>    
                                </div>   
                            </div>
                        </div>
                        <!-- End Add Lead Modal -->

                        <!-- Edit  Lead Modal -->
                        <div class="modal fade" id="edit_lead" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="">Bid Details</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <!-- Form -->
                                    <form name="lead_post_edit" id="lead_post_edit" class="form-horizontal">
                                        <div class="modal-body">
                                            <!-- Error message -->
                                            <div class="alert alert-danger print-error-msg" style="display:none">
                                                <ul></ul>
                                            </div>
                                            <div class="print-success-msg alert alert-success" style="display:none">
                                            </div>
                                            <input type="hidden" name="lead_id" value="" id="lead_id">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group bid_name">
                                                            <label for="bid_name">Bid Title</label>
                                                            <input type="text" class="form-control" id="bid_name_edit" name="bid_name" value="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group bid_url">
                                                            <label for="bid_url">Bid URL</label>
                                                            <input type="url" class="form-control" id="bid_url_edit" name="bid_url" value="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group bid_staus1 ">
                                                            <label for="bid_staus">Bid Status</label>
                                                                <div class="bid_status"> </div> 
                                                        </div>    
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label >Bid Sources</label>
                                                        <select name="bid_source" id="bid_source_edit" class="form-control">
                                                            <option value="">-- Select Sources --</option>
                                                            <option value="1">Upwork</option>
                                                            <option value="2">LinkedIn</option>
                                                            <option value="3">Clutch</option>
                                                            <option value="4">Freelancer</option>
                                                        </select>
                                                        <span id="bid_source_text"></span>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="bid_date">Bid Date</label>
                                                            <input type="date" class="form-control" name="bid_date" id="bid_date1" value="<?php echo date('Y-m-d'); ?>" >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group bid_reason"></div>
                                                        <div class="client-none error invalid-feedback"></div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group start_date"></div>
                                                        <div class="start-date-none error invalid-feedback"></div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group end_date"></div>
                                                        <div class="end-date-none error invalid-feedback"></div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group project-form" style="display: none;">
                                                            <label for="bid_reason">Projects</label>
                                                            <select name="project" class="form-control project"></select>
                                                            <div class="project-none error invalid-feedback"></div>
                                                        </div>    
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" id="lead_submit_edit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </form>    
                                </div>   
                            </div>
                        </div>
                        <!-- End Edit Lead Modal -->

                        <!-- View Lead Modal -->
                        <div class="modal fade" id="view_lead" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="">Bid Details</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <!-- Form -->
                                    <form name="lead_post_view" id="lead_post_view" class="form-horizontal">
                                        <div class="modal-body">
                                            <input type="hidden" name="lead_id_view" value="" id="lead_id_view">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group bid_name">
                                                            <label for="bid_name">Bid Title</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group bid_url">
                                                            <label for="bid_url">Bid URL</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group bid_staus1">
                                                            <label for="bid_staus">Bid Status</label>
                                                            <div class="bid_status"> </div> 
                                                        </div>    
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group bid_reason"></div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group projects"></div>
                                                        <div class="wrapper" id="wrp" style="display: none;">
                                                            <a href="#" id="type" class="font-weight-300" data-target="#mdl_Item" data-toggle="modal">+ Add New Project</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>    
                                </div>   
                            </div>
                        </div>
                        <!-- End Edit Lead Modal -->
                        <div class="card card-primary bid-filters"> {!! $html !!}</div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="lead_tbl" class="table table-bordered table-striped" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Bid Title</th>
                                            <!-- <th>Bid URL</th> -->
                                            <th>Bid Status</th>
                                            <?php
                                                $user = Auth::user();
                                                $get_emp_data = DB::table('employees')->where('id', $user->id)->first();
                                                
                                                if($user->role == '2' || ($get_emp_data->reporting_person == '1' && $get_emp_data->department == '1')){ 
                                                    $add_class = 'admin';

                                                }
                                                else{
                                                    $add_class = 'sales_person';
                                                }    
                                            ?>
                                            <th>Bid Source</th>
                                            <th>User</th>
                                            <th>Bid Date</th>
                                            <th>Created Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Bid Title</th>
                                            <!-- <th>Bid URL</th> -->
                                            <th>Bid Status</th>
                                            <th>Bid Source</th>
                                            <th>User</th>
                                            <th>Bid Date</th>
                                            <th>Created Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>

    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>  

    <script src="{{asset('plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
    <script src="{{asset('plugins/jszip/jszip.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>

    <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.js')}}"></script> <!-- Modal -->
    
    <script type="text/javascript">
      jQuery(function ($) {
        function cbDropdown(column) {
            return $('<ul>', {
                'class': 'cb-dropdown'
            }).appendTo($('<div>', {
                'class': 'cb-dropdown-wrap'
             }).appendTo(column));
        }
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });



        var table = $('#lead_tbl').DataTable({
            "order": [],
            processing: true,
            serverSide: true,
            "lengthMenu": [[-1,500, 1000,], ["All", 500, 1000,]],
            "dom": 'Bfrtip',
            <?php
                $user = Auth::user();
                $get_emp_data = DB::table('employees')->where('id', $user->id)->first();
                                                
                if($user->role == '2' || ($get_emp_data->reporting_person == '1' && $get_emp_data->department == '1')){ 
                    $add_class = 'admin';
                }else{
                     $add_class = 'sales_person';
                }    
            ?>
            drawCallback: function() {

                <?php if($add_class == 'admin'){ ?>
                this.api().columns([1,3]).every(function() {
                <?php } ?>

                <?php if($add_class == 'sales_person'){ ?>    
                this.api().columns([1]).every(function() {
                <?php } ?>

                    $( '.cb-dropdown-wrap' ).click( function(e) {
                        e.stopPropagation();
                    });

                    var column = this;

                    var ddmenu = cbDropdown($(column.header())).on('change', ':checkbox', function() {

                        var active;

                        var vals = $(':checked', ddmenu).map(function(index, element) {
                            active = true;
                            return $.fn.dataTable.util.escapeRegex($(element).val());
                        }).toArray().join('|');

                        column.search(vals.length > 0 ? '^(' + vals + ')$' : '', true, false)
                            .draw();

                        if (this.checked) {
                            $(this).closest('li').addClass('active');
                        } else {
                            $(this).closest('li').removeClass('active');
                        }

                        var active2 = ddmenu.parent().is('.active');
                        if (active && !active2) {
                            ddmenu.parent().addClass('active');
                        } else if (!active && active2) {
                            ddmenu.parent().removeClass('active');
                        }
                    });

                    column.data().unique().sort().each(function(d, j) {

                        var $label = $('<label>'),
                            $text = $('<span>', {
                                text: d
                            }),
                            $cb = $('<input>', {
                                type: 'checkbox',
                                value: d
                            });

                        $text.appendTo($label);
                        $cb.appendTo($label);

                        ddmenu.append($('<li>').html($label));
                    });

                });

            },
            <?php
                $user = Auth::user();
                $get_emp_data = DB::table('employees')->where('id', $user->id)->first();
                                                
                if($user->role == '2' || ($get_emp_data->reporting_person == '1' && $get_emp_data->department == '1')){ 
                    $add_class = 'admin';
                }
                else{
                    $add_class = 'sales_person';
                }    
            ?>
            ajax: {
                url: "{{ route('bids.index') }}",
                data: function (d) {
                    d.sales_person = $('#sales_person').val();
                    d.report_date = $('.drp-selected').text();
                    d.bid_status = $('#bid_status').val();
                    d.bid_source = $('#bid_source').val();
                }
            }, 
            columns: [
                {
                    data: 'bid_name',
                    name: 'bid_name',
                },
                // {data: 'bid_url', name: 'bid_url'},
                {data: 'bid_status', name: 'bid_status'},
                {data: 'bid_source', name: 'bid_source', orderable: true, searchable: true},
                {data: 'first_name', name: 'first_name', class: '{{ $add_class }}', orderable: true, searchable: true},
                {data: 'bid_date', name:'bid_date'},    
                {data: 'created_at', name:'created_at'},    
                {data: 'action', name: 'action'}
            ],
            buttons: [
                'csv', 'excel'
            ]
        });
                
                $('#filter_form').on('submit',function(e){
                    console.log("yessssssss");
                e.preventDefault();
                $(".cb-dropdown-wrap").remove();
                table.draw();

                
            });  
      });

        

      // Lead Insert Module
        $('#lead_submit').click(function (e) {
            e.preventDefault();
            
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: $('#lead_post').serialize(),
                url: "{{ route('bids.store') }}",
                type: "POST",
                success: function (response) {
                    if($.isEmptyObject(response.error)){
                        $(".print-success-msg").append('<p>'+response.success+'</p>');
                        $(".print-success-msg").css('display','block');
                        $('#bid_name').val('');
                        $('#bid_url').val('');
                        $("#bid_source_edit").val('');
                        setTimeout(function () {
                            location.reload();
                        }, 5000);
                        
                    }else{
                        printErrorMsg(response.error);
                    }
                 }
            });
        });

        // Lead Update
        $('#lead_submit_edit').click(function (e) {
            e.preventDefault();
            
            var status = $("#lead_post_edit .bid_status select").val();
            if(status == '3'){
                var client_name = $("#lead_post_edit #company").val();
                var start_date = $("#lead_post_edit #start_date").val();
                var project = $("#lead_post_edit .project-form select").val();
                
                if(client_name == ''){
                    $(".client-none.invalid-feedback").show();
                    $(".client-none").html("Client name is required");
                    return false;
                }else{
                    $(".client-none.invalid-feedback").hide();

                }
                if(start_date == ''){
                    $(".start-date-none.invalid-feedback").show();
                    $(".start-date-none").html("Start date is required");
                    return false;
                }else
                {
                    $(".start-date-none.invalid-feedback").hide();
                }
                if(project == ''){
                    $(".project-none.invalid-feedback").show();
                    $(".project-none").html("Project is required");
                    return false;
                }else{
                    $(".project-none.invalid-feedback").hide();

                }
            }

            if(status == '4'){
                var end_date = $("#lead_post_edit #end_date").val();
                
                if(end_date == ''){
                    $(".end-date-none.invalid-feedback").show();
                    $(".end-date-none").html("End date is required");
                    return false;
                }else
                {
                    $(".end-date-none.invalid-feedback").hide();
                }
                
            }
            // return false;
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: $('#lead_post_edit').serialize(),
                url: "{{ route('bids.store') }}",
                type: "POST",
                beforeSend: function(){
                    $(".spinner-wrapper").show();
                },
                success: function (response) {
                    $(".spinner-wrapper").hide();
                    if($.isEmptyObject(response.error)){
                        $(".print-success-msg").append('<p>'+response.success+'</p>');
                        $(".print-success-msg").css('display','block');
                        setTimeout(function () {
                            $(".print-error-msg").fadeOut(1000);
                            location.reload();
                        }, 2000);
                        
                    }else{
                        printErrorMsg(response.error);
                    }
                }
            });
        });
        // Error message fun
        function printErrorMsg (msg) {
            $(".print-error-msg").find("ul").html('');
            $(".print-error-msg").css('display','block');
            $.each( msg, function( key, value ) {
                $(".print-error-msg").find("ul").append('<li>'+value+'</li>');
            });
            setTimeout(function () {
                $(".print-error-msg").fadeOut(1000);
            }, 2000);
        }

        $( document ).ready(function() {
            $(document).on("click",'.lead-update',function(){
                data_id = $(this).attr('data-id');
                
                $('#lead_post_edit #lead_id').val(data_id);

                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data : { id: data_id},
                    url: "/bids/"+data_id+"/edit",

                    type: "GET",
                    success: function (response) {
                        // console.log(response);
                        $("#bid_name_edit").val(response.bid_name);
                        $("#bid_url_edit").val(response.bid_url);
                        $("#lead_post_edit #lead_id").val(response.id);
                        $("#bid_source_edit").val(response.bid_source_raw);
                        if(response.bid_status == 7 || response.bid_status == 6 || response.bid_status == 5)
                        {
                            $(".bid_reason").html('<label for="bid_reason">Bid Reason</label><textarea class="form-control" id="bid_reason" name="bid_reason" placeholder="Enter Reason">'+response.bid_reason+'</textarea>');
                        }
                        else{
                            $(".bid_reason").html('');
                        }
                        
                        var select = $("<select class='form-control' name='bid_status'></select>");
                        var chanels = ["Bidding","Communication", "Offer","Contract Start","Contract Close success","Contract Close Unsuccess","Contract Pause","Bid Close","Viewed"]
                        var chanelValue = Object.keys(chanels);

                        for(var i=0;i<chanels.length;i++){

                            var res1 = response.bid_status == chanelValue[i] ? "selected" : '';
                            
                            if(response.bid_status == 0){
                              if(parseInt(response.bid_status) == chanelValue[i] || parseInt(response.bid_status) + 8 == chanelValue[i] ){
                                
                                var option = $("<option "+res1+" ></option>");
                                var option_html = chanels[i];
                                var option_value = chanelValue[i];
                              }  

                            }
                            else if(response.bid_status == 8){
                                if(parseInt(1) == chanelValue[i] || parseInt(response.bid_status) == chanelValue[i] ){
                                    var option = $("<option "+res1+" ></option>");
                                var option_html = chanels[i];
                                var option_value = chanelValue[i];
                                }    

                            }
                            else{
                                if(parseInt(response.bid_status) == chanelValue[i] || parseInt(response.bid_status) + 1 == chanelValue[i] ){
                                    
                                    if(response.bid_status==4 || response.bid_status==5 || response.bid_status==6 ){
                                        if(parseInt(response.bid_status) == chanelValue[i]  ){
                                            var option = $("<option "+res1+" ></option>");
                                            var option_html = chanels[i];
                                            var option_value = chanelValue[i];
                                        }    
                                    }
                                    
                                    else{    
                                        var option = $("<option "+res1+"></option>");
                                        var option_html = chanels[i];
                                        var option_value = chanelValue[i];
                                    }

                                }
                            }    

                            if(response.bid_status==3){
                                if(parseInt(response.bid_status) + 2 ==  chanelValue[i] || parseInt(response.bid_status) + 3 ==  chanelValue[i] ){
                                    var option = $("<option "+res1+" ></option>");
                                    var option_html = chanels[i];
                                    var option_value = chanelValue[i];
                                }
                                
                            }
                            else if(response.bid_status==4 || response.bid_status==5 || response.bid_status==6 ){
                                    if(parseInt(response.bid_status) == chanelValue[i]  ){
                                        var option = $("<option "+res1+" ></option>");
                                        var option_html = chanels[i];
                                        var option_value = chanelValue[i];
                                    }    
                            }

                            else if(chanelValue[i] == 7){
                                var option = $("<option "+res1+" class='bid_close'></option>");
                                var option_html = chanels[i];
                                var option_value = chanelValue[i];
                            }
                                
                            $(option).val(option_value);
                            $(option).html(option_html);
                            $(select).append(option);

                        }
                        if(response.bid_status==2){
                            var myselect = $('<select>');
                            $.each(response.project, function(index,key) {
                                myselect.append( $("<option value="+response.project[index].id+" class='projects_option'>"+response.project[index].project_name+"</option>") );
                            });
                            $(".project-form").hide();
                            $('.project').html(myselect.html());
                        }
                        $("#lead_post_edit .card-body .row .bid_status").html($(select));
                    }
                });
                setTimeout(function(){
                    jQuery.noConflict();
                    $('#edit_lead').modal();
                }, 300);    
            });
            
            // View button
            $(document).on("click",'.lead-view',function(){
                data_id = $(this).attr('data-id');
                $('#lead_id_view').val(data_id);

                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data : { id: data_id},
                    url: "/bids/"+data_id+"/edit",

                    type: "GET",
                    success: function (response) {
                        if(response.bid_status == 0){
                            bid_status = "Bidding";
                        }
                        if(response.bid_status == 1){
                            bid_status = "Communication";
                        }
                        if(response.bid_status == 2){
                            bid_status = "Offer";
                        }
                        if(response.bid_status == 3){
                            bid_status = "Contract Start";
                        }
                        if(response.bid_status == 7){
                            bid_status = "Bid Close";
                        }
                        if(response.bid_status == 6){
                            bid_status = "Contract Pause";
                        }
                        if(response.bid_status == 5){
                            bid_status = "Contract Close Unsuccess";
                        }
                        if(response.bid_status == 4){
                            bid_status = "Contract Close success";
                        }
                        $("#lead_post_view .bid_name input").remove();
                        $("#lead_post_view .bid_name").html('<label for="bid_name">Bid Title</label><p>'+response.bid_details.bid_name+'</p>');
                        $("#lead_post_view .bid_url").html('<label for="bid_url">Bid URL</label><p>'+response.bid_details.bid_url+'</p>');
                        $("#lead_post_view .bid_staus1").html('<label for="bid_status">Bid Status</label><p>'+response.bid_stat+'</p>');
                        $("#lead_post_view .bid_reason").html('<label for="bid_reason">Bid Reason</label><p>'+response.bid_details.bid_reason+'</p>');
                        if(response.bid_status == 4){
                            $("#lead_post_view .bid_reason").html('<label for="client_name">Client Name</label><p>'+response.company+'</p>');
                        }
                    }
                });
                setTimeout(function(){
                    jQuery.noConflict();
                    $('#view_lead').modal();
                }, 300);    
            });
        });

        $( document ).ready(function() {
            $(document).on("change",'.bid_status select',function(){
                if ($(this).val() == '7' || $(this).val() == '5' || $(this).val() == '6') {
                    $(".bid_reason").html('<label for="bid_reason">Bid Reason</label><textarea class="form-control" id="bid_reason" name="bid_reason" placeholder="Enter Reason"></textarea>')
                    $(".start_date").html('');
                    $(".select2-container").html('');
                    $(".project-form").hide();
                }

                if($(this).val() == '3') {
                    $(".bid_reason").html('<label for="client_name">Business Name</label><input type="text" class="form-control " id="company" name="company" value="" placeholder="Enter Business Name">');
                    $(".start_date").html('<label for="start_date">Start Date</label><input type="date" class="form-control " id="start_date" name="start_date" value="" placeholder="Select date">');
                    jQuery('.project').select2({
                        dropdownParent: $("#lead_post_edit"),
                        placeholder: 'Choose project',
                        tags: true
                    });
                    
                    $(".project-form").show();
                }
                if($(this).val() == '4' || $(this).val() == '5') {
                    $(".end_date").html('<label for="end_date">End Date</label><input type="date" class="form-control " id="end_date" name="end_date" value="" placeholder="Select date">');
                }else{
                    $(".end_date").html('');
                }    

            });
            
            jQuery('input[name="report_date"]').daterangepicker({
                opens: 'center',
                startDate: moment().subtract(6, 'days'),
                endDate: moment(),
                maxDate: new Date(),
                locale: {
                    format: 'DD/MM/YYYY'
                }
            });
        });

    </script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
<style type="text/css">
                /* Styles for the drop-down. Feel free to change the styles to suit your website. :-) */

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
            height: 0px;
            display: none;
        }
        .cb-dropdown-wrap:first-child{
            height: 30px;
            display: block;
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
    <script>
        function getLocalDateString() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0'); // Month is 0-indexed
            const day = String(today.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`; // "YYYY-MM-DD"
        }
        function setMaxDate() {
            const today = getLocalDateString();
            document.getElementById('bid_date').setAttribute('max', today);
            document.getElementById('bid_date1').setAttribute('max', today);
        }
        setInterval(setMaxDate, 5000);
    </script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> <!-- date range picker -->

<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> <!-- select2 -->
@endsection