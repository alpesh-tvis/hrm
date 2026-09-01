@extends('admin.master')
<link rel="stylesheet" href="{{asset('public/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('public/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">

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
                        <form action="{{ route('upwork-timer.store') }}" method="POST" class="form-horizontal">
                            @csrf
                            <div class="modal-body">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="bid_name">Bid Title</label>
                                                <select class="form-control" name="project_id">
                                                    <option value="">-- Select Project --</option>
                                                    @foreach($projects as $project)
                                                        <option value="{{$project->id}}">{{$project->project_name}}</option>
                                                    @endforeach    
                                                </select>    
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
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="lead_tbl" class="table table-bordered table-striped" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Bid Title</th>
                                            <th>Bid Status</th>
                                            <th>User</th>
                                            <th>Created Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Bid Title</th>
                                            <th>Bid Status</th>
                                            <th>User</th>
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
    <script src="https://hrm.tvistech.com/public/plugins/jquery/jquery.min.js"></script>

    <script src="https://hrm.tvistech.com/public/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="https://hrm.tvistech.com/public/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{asset('public/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>  
    
    
    <script type="text/javascript">
        jQuery(function ($) {
    
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
   
  
            // Lead Update
            $('#lead_submit_edit').click(function (e) {
                e.preventDefault();
        
                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: $('#lead_post_edit').serialize(),
                    url: "{{ route('upwork-timer.store') }}",
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
                            
                        }
                     }
                });
            });
        });    
    
    </script>
@endsection