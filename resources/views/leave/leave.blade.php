@extends('admin.master')

@section('content')
    
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                   
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
                        <form action="{{ route('leave.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="project-bg">
                                    <h3 class="font-weight-bolder mb-0">Leave Details</h3>
                                    <div class="p-4">
                                        <div class="row">
                                            <div class="{{$role == 2 ? 'col-md-4' : 'col-md-6'}}">
                                                <div class="form-group mb-0">
                                                    <label for="leave_type">Leave Type:</label>
                                                    <select name="leave_type" id="leave_type" class="form-control">
                                                        <option value="PL">Paid Leave</option>
                                                        <option value="SL">Sick Leave</option>
                                                        <option value="CL">Casual Leave</option>
                                                    </select>
                                                </div>    
                                            </div>
                                            <div class="{{$role == 2 ? 'col-md-4' : 'col-md-6'}}">
                                                <div class="form-group mb-0">
                                                    <label for="leave_type">Leave Date:</label>
                                                    <input type="text" name="leave_date" value="" class="mb-2 form-control" />
                                                </div>    
                                            </div>
                                            @if($role == 2)
                                                <div class="col-md-4">
                                                    <div class="form-group mb-0">
                                                        <label for="leave_type">Employees:</label>
                                                        <select name="user_id" id="user_id" class="form-control">
                                                            @foreach ($employees as $employee)
                                                                <option value="{{$employee->id}}">{{$employee->first_name}} {{$employee->last_name}}</option>
                                                            @endforeach
                                                    </select>
                                                    </div>    
                                                </div>
                                            @endif    
                                        </div>
                                    
                                
                                <div id="result"></div>
                                </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent p-0">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                   
                </div>
            </div>
        </div>
    </section>
@endsection
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> <!-- date range picker -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> <!-- select2 -->
<script type="text/javascript">
    $(document).ready(function(){
        jQuery('input[name="leave_date"]').daterangepicker({
            opens: 'center',
            locale: {
                format: 'DD/MM/YYYY'
            }
        });

        $(document).on('click','.applyBtn',function(){
            drp_selected = $('.drp-selected').text();
            result = $(drp_selected.split('-'));

            var startDateStr = result[0];
            var endDateStr = result[1];

            var startDate = parseDate(startDateStr);
            var endDate = parseDate(endDateStr);

            var datesBetween = getDatesBetween(startDate, endDate);

            var dateInputs = $('#result');
            var html_status = '';
            for (var i = 0; i < datesBetween.length; i++) {
                html_status +=   '<div class="row">';
                html_status +=      '<div class="col-md-3">';
                html_status +=          '<div class="form-group">';
                html_status +=              '<label for="leave_date">Leave Date:</label>';
                html_status +=              '<input type="text" class="form-control" name="leave['+i+'][leave_date]" value="'+formatDate(datesBetween[i])+'" readonly>';
                html_status +=          '</div>';    
                html_status +=      '</div>'; 
                html_status +=      '<div class="col-md-3">';
                html_status +=          '<div class="form-group">';
                html_status +=              '<label for="leave_status">Leave Status:</label>';
                html_status +=                  '<select name="leave['+i+'][leave_status]" id="leave_status" class="form-control">';
                html_status +=                      '<option value="F">Full Day</option>';
                html_status +=                      '<option value="FH">First Half</option>';
                html_status +=                      '<option value="SH">Second Half</option>';
                html_status +=                  '</select>';
                html_status +=          '</div>';    
                html_status +=      '</div>';
                html_status +=      '<div class="col-md-6">';
                html_status +=          '<div class="form-group">';
                html_status +=              '<label for="leave_reason">Leave Reason:</label>';
                html_status +=              '<textarea class="form-control" name="leave['+i+'][leave_reason]" required></textarea>';
                html_status +=          '</div>';    
                html_status +=      '</div>';  
                html_status +=    '</div>'; 

                var dateInput = $(html_status);
            }
            dateInputs.html(dateInput);
        });    
    });
    
    function getDatesBetween(startDate, endDate) {
        var dates = [];
        var currentDate = new Date(startDate);

        while (currentDate <= endDate) {
            dates.push(new Date(currentDate));
            currentDate.setDate(currentDate.getDate() + 1);
        }
        return dates;
    }

    function parseDate(dateString) {
      var parts = dateString.split('/');
      return new Date(parts[2], parts[1] - 1, parts[0]);
    }

    function formatDate(date) {
        var day = date.getDate();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();
        return day + '/' + month + '/' + year;
    }
</script>