@extends('admin.master')

@section('content')
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">    
@php
    use Illuminate\Support\Str;
@endphp

<section class="content extraDays">
<p id="removeExtraDayMsg" class="alert alert-danger" style="display:none">Extra Day Row Deleted Successfully!<span style='font-size:14px;'>&#10060;</span></p>    
<div class="container-fluid">
   @if($current_user_roll == 2)
   <div class="row mb-2">
       <div class="col-12 text-right">
           <a href="{{ route('extra-days.create') }}" class="btn btn-primary">Add</a>
        </div>
    </div>
    @endif
</div>
<div class="card-body">
       <div class="row">
           <div class="col-md-2">
               <label for="financial_year" class="form-label">Financial Year :: <span id='CurrentFY'></span></label>
                <select id="financial_year" class="form-control form-control-sm financial_year">
                   @foreach($financial_years as $yearvalue)
                    <option value="{{ $yearvalue->financial_year }}">{{ $yearvalue->financial_year }}</option>
                    @endforeach    
                </select>
            </div>
            
            @if($current_user_roll == 2)
                @if($employeerec)
                    <div class="col-md-2">
                        <label for="employees_list" class="form-label">Emplyoee </label>
                        <select id="employees_list" class="form-control employees_list">
                        <option value="">--- Select All Employees --- </option> 
                          @foreach($employeerec as $empname)
                           <option value="{{ $empname->first_name }} {{ $empname->last_name }}">{{ $empname->first_name }} {{ $empname->last_name }}</option>
                          @endforeach
                        </select>
                    </div>
                @endif
            @endif
        </div>
        
        <table id="extra_days_tbl" class="table table-bordered table-striped display">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Date</th>
                    <th>Reason of Work Description</th>
                    <th>Extra Days</th>
                    <th>Financial Year</th>
                     @if($current_user_roll == 2)
                    <th>Action</th>
                    @endif
                </tr>
            </thead>

            <tbody>
                    @foreach($employeeExtraDays as $empExDaVal)
                    <tr>
                        <td>{{ $empExDaVal->employee_name }}</td>
                        @php
                            $my_date = $empExDaVal->date;
                            $formatted_date = date("F j, Y", strtotime($my_date));
                            $day = date("l", strtotime($my_date));
                        @endphp

                        <td data-order="{{$my_date}}">{{ $day }}, {{ $formatted_date }}</td>
                        <td>
                            @php
                                $reasonDesc = $empExDaVal->reason_of_work_description;
                                $truncReasonDesc = Str::limit($reasonDesc, 40, '...');
                            @endphp

                            {{ $truncReasonDesc }}

                            @if(strlen($reasonDesc) > 40)
                                <a href="{{ route('extra_days_show', [$empExDaVal->id]) }}" class="btn btn-info extra-day-view" data-toggle="modal" data-target="#details-modal-{{ $empExDaVal->id }}" style="font-size: 12px;">
                                More
                                </a>
                            @endif
                        </td>
                        @php
                         $extra_day = $empExDaVal->extra_days;
                         $extra_day = (float) $extra_day;
                         $extra_day = number_format($extra_day, (strpos($extra_day, '.') !== false) ? 1 : 0);
                        @endphp
                        <td>{{ $extra_day }}</td>
                        <td class="hidden_table" >{{ $empExDaVal->financial_year }}</td>
                         @if($current_user_roll == 2)
                        <td>
                           <a href="{{ route('extra_days_show', [$empExDaVal->id]) }}" class="btn btn-info project-view" data-toggle="modal" data-target="#details-modal-{{ $empExDaVal->id }}">
                            <i class="fas fa-eye"></i>
                            </a>
                            
                            <!--Modal: Extra Days-->
                            <div id="details-modal-{{ $empExDaVal->id }}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="details-modal-{{ $empExDaVal->id }}" aria-hidden="true">
                                <div class="modal-dialog extra-days-popup">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <div class="form-group mb-0">
                                                <i class="fa fa-user" aria-hidden="true"></i>
                                                <label for="" class="form-label mb-0">{{ $empExDaVal->employee_name }}</label>
                                            </div>
                                            <label for="emp_name" class="form-label emp_name mb-0">Date : {{ $day }}, {{ $formatted_date }}</label>
                                        </div>
                                        <div class="modal-body">
                                            
                                            <div class="form-group borderdark">
                                                <label for="form2" class="mb-0">Reason of description : {{ $empExDaVal->reason_of_work_description }} 
                                                </label>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <div class="form-group mb-0 ">
                                                    @php
                                                     $extra_day = $empExDaVal->extra_days;
                                                     $extra_day = (float) $extra_day;
                                                     $extra_day = number_format($extra_day, (strpos($extra_day, '.') !== false) ? 1 : 0);
                                                    @endphp
                                                    <label for="form2" class="mb-0 pills">Extra day :  {{ $extra_day }}
                                                    </label>
                                                </div>
                                                <div class="form-group mb-0">
                                                    <label for="form2" class="mb-0 pills">Finanical Year :{{ $empExDaVal->financial_year }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!---END Extra Days Model-->
                            
                            <a href="{{ route('extra_days_edit',$empExDaVal->id) }}" class="btn btn-info project-view"> 
                              <i class="fas fa-edit"></i> 
                            </a>

                            <a href="javascript:void(0)" data-url="{{ route('extra-days.destroy', $empExDaVal->id) }}" data-id="{{ $empExDaVal->id }}" data-date="{{ $empExDaVal->date }}" data-employee_name="{{ $empExDaVal->employee_name }}" data-reason_of_work_description="{{ $empExDaVal->reason_of_work_description }}" data-extra_days="{{ $empExDaVal->extra_days }}" data-financial_year="{{ $empExDaVal->financial_year }}" class="btn btn-danger destroy-extradays">
                            <i class="fas fa-trash"></i>
                            </a>
                        </td>
                        @else
                        <td>
                            <!--Modal: Extra Days-->
                            <div id="details-modal-{{ $empExDaVal->id }}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="details-modal-{{ $empExDaVal->id }}" aria-hidden="true">
                                <div class="modal-dialog extra-days-popup">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <div class="form-group mb-0">
                                                    <i class="fa fa-user" aria-hidden="true"></i>
                                                    <label for="" class="form-label">{{ $empExDaVal->employee_name }}</label>
                                                </div>
                                                <label for="emp_name" class="form-label emp_name">Date : {{ $day }}, {{ $formatted_date }}</label>
                                        </div>  
                                        <div class="modal-body">
                                            <div class="form-group borderdark">
                                                    <label for="form2">Reason of description : {{ $empExDaVal->reason_of_work_description }} 
                                                    </label>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <div class="form-group mb-0">
                                                    <label for="form2" class="mb-0 pills">Extra day :  {{ $empExDaVal->extra_days }}
                                                    </label>
                                                </div>
                                                <div class="form-group mb-0">
                                                    <label for="form2" class="mb-0 pills">Finanical Year : {{ $empExDaVal->financial_year }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>                                              
                                               
                                            
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <!---END Extra Days Model-->
                            <a href="{{ route('extra_days_show', [$empExDaVal->id]) }}" class="btn btn-info project-view" data-toggle="modal" data-target="#details-modal-{{ $empExDaVal->id }}">
                            <i class="fas fa-eye"></i>
                            </a>
                        </td>    
                        @endif
                    </tr>
                    @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <th>Employee</th>
                    <th>Date</th>
                    <th>Reason of Work Description</th>
                    <th>Extra Days</th>
                    <th>Financial Year</th>
                    @if($current_user_roll == 2)
                    <th>Action</th>
                    @endif
                </tr>
            </tfoot>
        </table>
    </div>
</section>
@endsection

<!-- DataTables  & Plugins -->
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.js')}}"></script>

<script>
jQuery(function ($) {

    /*$("#extra_days_tbl").DataTable({
      "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "pageLength": 100,
    })*/

    $("#extra_days_tbl").DataTable({
        language: {
            emptyTable: "No extra day available in table",  
            loadingRecords: "Please wait .. ", 
            zeroRecords: "No matching records found"
        }, "paging": false, "responsive": true,"lengthChange": false, "autoWidth": false, "searchable": true, "pageLength": 10,
        
    });

    //finanical Filter
    var Yeartables = $('#extra_days_tbl').DataTable();
    $("#extra_days_tbl.dataTables_filter").append($("#financial_year"));
    
    var YearIndex = 0;
    
    $("#extra_days_tbl th").each(function (i) {
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

    //emplyoee filter
    var emplyoeetables = $('#extra_days_tbl').DataTable();
    $("#extra_days_tbl.dataTables_filter").append($("#employees_list"));
    
    var EmpIndex = 0;
    
    $("#extra_days_tbl th").each(function (i) {
        if ($($(this)).html() == "Employee") {
            EmpIndex = i; 
            return false;
        }
    });
    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var EmpselectedItem = $('#employees_list').val();
           
            var Emp = data[EmpIndex];
            if (EmpselectedItem === "" || Emp.includes(EmpselectedItem)) {
              return true;
            }
            
            return false;
        }
    );
    $("#employees_list").change(function (e) {
        emplyoeetables.draw();
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

});

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).on('click', '.destroy-extradays', function() {
        var extradayURL = $(this).data('url');
        var trObj = $(this);
        var dataId = $(this).attr('data-id');
        var dataName = $(this).attr('data-employee_name');
        var dataDate = $(this).attr('data-date');
        var dataReason = $(this).attr('data-reason_of_work_description');
        var dataExtraDay = $(this).attr('data-extra_days');
        var datafinYear = $(this).attr('data-financial_year');
        
        if (confirm("Are you sure you want to delete this Extra Day " + dataName + " column?") == true) {
            $.ajax({
                url: extradayURL,
                type: 'DELETE',
                success: function(data) {
                   trObj.parents("tr").remove();
                   $("#removeExtraDayMsg").show();
                }
                
            });
        }
    });
});
</script>

