@extends('admin.master')

@section('content')
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">     

<section class="content salaryAdjust">
    <div class="card-body">
            <div class="row">
                <div class="col-md-2">
                   <label for="financial_year" class="form-label">Financial Year : <span id='CurrentFY'></span></label>
                    <select id="financial_year" class="form-control form-control-sm financial_year">
                        <option>--Select Financial Year-</option>
                    </select>
                </div>
                @if($current_user_role == 2)
                    @if($employeerec)
                        <div class="col-md-2">
                           <label for="employees_list" class="form-label">Emplyoee </label>
                            <select id="employees_list" class="form-control">
                            <option value="">--- Select All Employees --- </option> 
                              @foreach($employeerec as $empname)
                               <option value="{{ $empname->first_name }} {{ $empname->last_name }}">{{ $empname->first_name }} {{ $empname->last_name }}</option>
                              @endforeach
                            </select>
                       </div>
                    @endif
                    <div class="col-md-2">
                       <label for="lvtype" class="form-label">Leave Type </label>
                        <select id="lvtype" class="form-control">
                        <option value="">--- Select Leave Type--- </option>
                            @php
                            $uniqueLeaveTypes = $employeLeave->pluck('leave_type')->unique()->sort();
                            @endphp 
                            @foreach($uniqueLeaveTypes as $leaveType)
                              <option value="{{ $leaveType  }}">{{ $leaveType }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" value="{{$current_user_role}}" id="emp_id">
                @endif
            </div>
            <table id="leave_adjust_tbl" class="table table-bordered table-striped display">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Financial Year</th>
                        @if($current_user_role == 2)
                        <th>Employee</th>
                        @endif
                        <th>LvType</th>
                        <th>Leave Deduction</th>
                        <th>Leave Adjust</th>
                        <th>LvDate</th>
                        <th>Month</th>
                        <!-- @if($current_user_role== 2)
                        <th>Action</th>
                        @endif -->
                    </tr>
                </thead>
                <tbody>
                    @forelse ($getSalaryAdjustData as $index => $data)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $data['financial_year'] }}</td>
                            @if($current_user_role == 2)
                            <td>{{ $data['employee_name'] }}</td>
                            @endif
                            <td>{{ $data['leave_type'] }}</td>
                            <td>{{ $data['leave_deduct'] }}</td>
                            <td>{{ $data['leave_adjust'] }}</td>
                            <td>{{ $data['leave_date'] }}</td>
                            <td>{{ $data['leave_month'] }}</td>
                            @if($current_user_role == 2)
                                 <!-- <td>
                                    <a href="" class="btn btn-info project-view" data-toggle="modal" data-target="">
                                       <i class="fas fa-eye"></i>
                                    </a> 
                                    <a href="" class="btn btn-info project-view"> 
                                      <i class="fas fa-edit"></i> 
                                    </a>
                                    <a href="javascript:void(0)" data-url="" data-id="" data-name="Cheti Chand" class="btn btn-danger destroy">
                                      <i class="fas fa-trash"></i>
                                    </a>
                                </td> -->
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No data available</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th>#</th>
                        <th>Financial Year</th>
                        @if($current_user_role == 2)
                        <th>Employee</th>
                        @endif
                        <th>LvType</th>
                        <th>Leave Deduction</th>
                        <th>Leave Adjust</th>
                        <th>LvDate</th>
                        <th>Month</th>
                        <!-- @if($current_user_role== 2)
                        <th>Action</th>
                        @endif -->
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
    $("#leave_adjust_tbl").DataTable({
        responsive: true,
        lengthChange: false,
        autoWidth: false,
        pageLength: 10
    });

    //emplyoee filter
    var empidmain = $('#emp_id').val();
    if(empidmain == 2){
        var emplyoeetables = $('#leave_adjust_tbl').DataTable();
        $("#leave_adjust_tbl.dataTables_filter").append($("#employees_list"));
        
        var EmpIndex = 0;
        $("#leave_adjust_tbl th").each(function (i) {
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

        //LeaveType
        $("#leave_adjust_tbl.dataTables_filter").append($("#lvtype"));
        var LeaveTypeIndex = 0;
        $("#leave_adjust_tbl th").each(function (i) {
            if ($($(this)).html() == "LvType") {
                LeaveTypeIndex = i; 
                return false;
            }
        });
        $.fn.dataTable.ext.search.push(
            function (settings, data, dataIndex) {
                var LeaveTypeselectedItem = $('#lvtype').val();
                var LeaveType = data[LeaveTypeIndex];
                if (LeaveTypeselectedItem === "" || LeaveType.includes(LeaveTypeselectedItem)) {
                  return true;
                }
                
                return false;
            }
        );
        $("#lvtype").change(function (e) {
            emplyoeetables.draw();
        });
    }    

    // Current financial year function
    function getCurrentFinancialYear() {
        var fiscalyear = "";
        var today = new Date();
        if ((today.getMonth() + 1) <= 3) {
            fiscalyear = (today.getFullYear() - 1) + "-" + today.getFullYear();
        } else {
            fiscalyear = today.getFullYear() + "-" + (today.getFullYear() + 1);
        }
        return fiscalyear;
    }

    // Populate dropdown with financial years
    function populateFinancialYearDropdown() {
        let startYear = 2020; 
        let currentYear = new Date().getFullYear();
        let dropdown = $("#financial_year");

        for (let year = startYear; year <= currentYear + 1; year++) {
            let optionValue = `${year}-${year + 1}`;
            dropdown.append(new Option(optionValue, optionValue));
        }
    }

    populateFinancialYearDropdown();

    var defaultYear = getCurrentFinancialYear();
    $("#financial_year").val(defaultYear).change();
    $("#CurrentFY").text(defaultYear);

    //var table = $("#leave_adjust_tbl").DataTable();
    $("#financial_year").on("change", function () {
            var empid = $('#emp_id').val();
            var selectedYear = $(this).val();
        if ($.fn.dataTable.isDataTable("#leave_adjust_tbl")) {
            var table = $("#leave_adjust_tbl").DataTable(); 
            table.column(1).search(selectedYear).draw();
        } else {
        console.error("DataTable is not initialized for #leave_adjust_tbl.");
        }
    });

});
</script>