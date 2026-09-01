@extends('admin.master')

@section('content')
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">    
@php 
$user = \Auth::user();
$check_department = \App\Models\Employee::where('company_email', '=',$user->email)->first();
@endphp
<section class="content">
 <p id="removeholidayMsg" class="alert alert-danger" style="display:none">Holiday Row Deleted Successfully!</p>
    @if($user->role == '2')
    <div class="container-fluid">
       <div class="row mb-2">
           <div class="col-12 text-right">
               <a href="{{ route('holiday.add') }}" class="btn btn-primary">Add Holiday</a>
            </div>
        </div>
    </div>
    @endif
    <!-- @foreach ($errors->all() as $error)
       <li>{{ $error }}</li>
    @endforeach -->
<div class="card-body">
      <div class="row">
        <div class="col-sm-4">
            <label for="financial_year" class="form-label">Financial Year :: <span id='CurrentFY'></span></label>
            <select id="financial_year" class="form-control form-control-sm financial_year">
               <option value="">All Financial Year</option> 
               @foreach($financial_years as $year)
                <option value="{{$year->finanical_year}}">{{$year->finanical_year}}</option>
               @endforeach
            </select>
       </div>
       <div class="col-sm-4">
            <label for="holiday_fillter" class="form-label">Holiday Fillter :: </label>
            <select id="holiday_fillter" class="form-control form-control-sm">
                <option value="">All</option>
                <option value="past">Past</option>
                <option value="upcoming">Upcoming</option>
            </select>
        </div>
    </div>
        <table id="holiday_tbl" class="table table-bordered table-striped display">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Holiday</th>
                    <th>Date</th>
                   <!--  <th>Day</th> -->
                    <th>Remark</th>
                    <th>Financial Year</th>
                    @if($user->role == '2')
                    <th>Action</th>
                    @endif
                </tr>
            </thead>

            <tbody>
                @php
                 $count =1;
                @endphp
                @foreach($holidayList as $holiday )
                    @if($holiday)
                    <tr>
                        <td>{{ $count}}</td>
                        <td>
                            @php
                              $todayDate = date("Y-m-d");
                            @endphp
                           
                            @if($todayDate <= $holiday->holiday_date)
                            <p class="btn btn-info">
                               <!-- <i class="far fa-hourglass"></i> --> <i class="fas fa-calendar-plus"></i> Upcoming
                            </p>
                            @else
                            <p class="btn btn-danger">
                               <!-- <i class="fas fa-hourglass-end"></i> --><i class="fas fa-calendar-minus"></i> Past
                            </p>
                            @endif
                           
                        </td>
                        @php
                            $holidaydate = $holiday->holiday_date;
                            $date = new DateTime($holidaydate);
                            $formattedDate = date_format($date, 'l, F j, Y');
                        @endphp
                        <td>{{ $formattedDate }}</td>
                       <!--  <td>{{ $holiday->day}}</td> -->
                        <td>{{ $holiday->remark}}</td>
                        <td>{{ $holiday->finanical_year}}</td>
                        @if($user->role == '2')
                        <td class="dflex">
                           <a href="" class='btn btn-info project-view' data-toggle="modal" data-target="#details-modal-{{ $holiday->id }}">
                                <i class="fas fa-eye"></i>
                            </a>

                            <!--Modal: Holiday Leave-->
                            <div id="details-modal-{{ $holiday->id }}" class="modal fade modal-top-right" tabindex="-1" role="dialog" aria-labelledby="details-modal-{{ $holiday->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-top-right">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <i class="nav-icon fas fa-calendar-alt" aria-hidden="true"></i>
                                                <label for="emp_name" class="form-label">Date : {{ $holiday->holiday_date }}</label>
                                            </div>
                                            <div class="form-group">
                                                <label for="form2">Day : {{$holiday->day}}
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <label for="form2">Remark : {{$holiday->remark}}
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <label for="form2">Finanical Year : {{$holiday->finanical_year}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-body">
                                    </div>
                                </div>
                            </div>
                            </div>
                            <!---END Model-->

                            <a href="{{ route('holiday.edit', [$holiday->id]) }}" class="btn btn-info project-view"> 
                              <i class="fas fa-edit"></i> 
                            </a>
                            <a href="javascript:void(0)" data-url="{{ route('holiday.destroy', $holiday->id) }}" data-id="{{ $holiday->id }}" data-name="{{ $holiday->remark }}" 
                            class="btn btn-danger destroy-holiday"><i class="fas fa-trash"></i>
                            </a>
                        </td>
                        @endif
                    </tr>
                    @endif
                    @php 
                     $count++;
                    @endphp
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <th>#</th>
                    <th>Holiday</th>
                    <th>Date</th>
                   <!--  <th>Day</th> -->
                    <th>Remark</th>
                    <th>Financial Year</th>
                    @if($user->role == '2')
                    <th>Action</th>
                    @endif
                </tr>
            </tfoot>

        </table>
   
    </div>
</section>

<!-- DataTables  & Plugins -->
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.js')}}"></script>

<script>
jQuery(function ($) {
    $("#holiday_tbl").DataTable({
      language: {
       emptyTable: "No holiday available in table",  
       loadingRecords: "Please wait .. ", 
       zeroRecords: "No holiday matching records found"
      }, "paging": true, "responsive": true,"lengthChange": false, "autoWidth": false, "searchable": true, "pageLength": 10,
    })

    //Filter financial Year
    var Yeartables = $('#holiday_tbl').DataTable();
    $("#holiday_tbl.dataTables_filter").append($("#financial_year"));
    var YearIndex = 0;

    $("#holiday_tbl th").each(function (i) {
        if ($($(this)).html() == "Financial Year") {
            YearIndex = i; 
            return false;
        }
    });
    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var YearselectedItem = $('#financial_year').val();
            var Year = data[YearIndex];
            if (YearselectedItem === "" || Year.includes(YearselectedItem)) {
              return true;
            }
            return false;
        }
    );
    $("#financial_year").change(function (e) {
        Yeartables.draw();
        
    });

    //Current financial year function
    function getCurrentFinancialYear() {
      var fiscalyear = "";
      var today = new Date();
      if ((today.getMonth() + 1) <= 3) {
        fiscalyear = (today.getFullYear() - 1) + "-" + today.getFullYear()
      } else {
        fiscalyear = today.getFullYear() + "-" + (today.getFullYear() + 1)
      }
      return fiscalyear
    }
    //set default current financial year
    var defaultYear = getCurrentFinancialYear();
    $("#financial_year").val(defaultYear).change();
    document.getElementById("CurrentFY").innerHTML = getCurrentFinancialYear();

    var holidtable = $('#holiday_tbl').DataTable();
    $("#holiday_fillter").on('change', function() {
       holidtable.column(1).search($(this).val()).draw();
    }); 

});

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
//Remove Holiday Column
$(document).on('click', '.destroy-holiday', function() {

        var holidayURL = $(this).data('url');
        var trObj = $(this);
        var dataId = $(this).attr('data-id');
        var dataName = $(this).attr('data-name');
        
        if (confirm("Are you sure you want to delete this holiday " + dataName + " column?") == true) {
            $.ajax({
                url: holidayURL,
                type: 'DELETE',
                success: function(data) {
                   trObj.parents("tr").remove();
                   console.log(data);
                    $("#removeholidayMsg").show();
                }
            });
        }

    });
});
</script>
@endsection
