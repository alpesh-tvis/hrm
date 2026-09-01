@extends('admin.master')

@section('content')
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<section class="content">
    <div id="message-container"></div>
    <div class="card-body">
        <div class="row sal-deduction" style="margin-bottom: 30px;">
            <div class="col-md-2">
                <label for="financial_year" class="form-label">
                    Financial Year ::
                    <span id='CurrentFY'></span>
                </label>
                <select id="financial_year" class="form-control form-control-sm financial_year">
                    <option>--Select Financial Year-</option>
                    @foreach($financialYears as $fyear)
                        <option value="{{$fyear}}">{{$fyear}}</option>
                    @endforeach
                </select>
            </div>

            @if($current_user_roll == 2)
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
                    <label for="employees_lvtype" class="form-label">Leave Type </label>
                    <select id="employees_lvtype" class="form-control">
                        <option value="">--- Select Leave Type --- </option>
                        @php
                            $uniqueLeaveTypes = $employeLeave->pluck('leave_type')->unique()->sort();
                        @endphp 
                            
                        @foreach($uniqueLeaveTypes as $leaveType)
                            <option value="{{ $leaveType  }}">{{ $leaveType }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <table id="salary_deduction_tbl" class="table table-bordered table-striped display">
            <thead>
                <tr>
                    @if($current_user_roll == 2)
                        <th>Employee</th>
                    @endif
                        
                    <th>Date</th>
                    <th>Status</th>
                    <th>LvType</th>
                    <th>Reason</th>
                    <th>Financial Year</th>
                        
                    @if($current_user_roll == 2)
                        <th>Action</th>
                    @endif
                </tr>
            </thead>

            <tbody>
@foreach(($allLeaveData ?? []) as $userId => $types)
    @foreach(($types['leaves'] ?? []) as $type => $leaves)
        @foreach(($leaves ?? []) as $leave)

            <tr>

                @if($current_user_roll == 2)
                    <td>{{ $types['employee_name'] ?? '' }}</td>
                @endif

                <td>{{ $leave->leave_date ?? '' }}</td>
                <td>{{ $leave->leave_status ?? '' }}</td>
                <td>{{ $leave->leave_type ?? '' }}</td>
                <td>{{ $leave->leave_reason ?? '' }}</td>
                <td>{{ $financialY ?? '' }}</td>

                @if($current_user_roll == 2)
                    <td>
                        <select class="form-control leave_type"
                            data-leave-id="{{ $leave->id ?? '' }}"
                            data-user-id="{{ $leave->user_id ?? '' }}"
                            data-leave-type="{{ $leave->leave_type ?? '' }}"
                            data-leave-status="{{ $leave->leave_status ?? '' }}"
                            data-leave-date="{{ $leave->leave_date ?? '' }}"
                            data-leave-reason="{{ $leave->leave_reason ?? '' }}">

                            <option value="">--Select--</option>
                            <option value="1">Paid</option>
                        </select>
                    </td>
                @endif

            </tr>

        @endforeach
    @endforeach
@endforeach
</tbody>
            <tfoot>
                <tr>
                    @if($current_user_roll == 2)
                        <th>Employee</th>
                    @endif
                    <th>Date</th>
                    <th>Month</th>
                    <th>LvType</th>
                    <th>Reason</th>
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
<script src="{{asset('plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>

<script src="{{asset('plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script src="{{asset('plugins/jszip/jszip.min.js')}}"></script>

<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.js')}}"></script>

<script src="{{asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>

<!-------sweetalert---->
<link rel="stylesheet" href="{{asset('sweet-alert/css/sweetalert2.min.css')}}">
<script src="{{asset('sweet-alert/js/sweetalert2.min.js')}}"></script>

<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.leave_type').on('change', function() {
            var leaveId = $(this).data('leave-id');
            var userId = $(this).data('user-id'); 
            var leaveType = $(this).data('leave-type'); 
            var leaveStatus = $(this).data('leave-status');
            var leaveDate = $(this).data('leave-date'); 
            var leaveReason = $(this).data('leave-reason'); 
            var leaveDeduct = $(this).val();
            
            if (leaveId) {
                $.ajax({
                    url: '/update-leave-deduction',
                    method: 'POST',
                    data: {
                        leave_id: leaveId,
                        user_id: userId,
                        leave_type: leaveType,
                        leave_status: leaveStatus,
                        leave_date: leaveDate,
                        leave_reason: leaveReason,
                        leave_deduct: leaveDeduct
                    },
                    success: function(response) {
                       Swal.fire({
                            title: 'Are you sure?',
                            text: response.message,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Yes',
                            cancelButtonText: 'No',
                        }).then((result) => {
                            if (result.isConfirmed) {
                               setTimeout(function() {
                                    location.reload();  
                                }, 1000); 
                            } else if (result.isDismissed) {
                                console.log('User clicked No');
                            }
                        });
                       $('#message-container').html('<div class="alert alert-success">' + response.message + '</div>');
                        localStorage.setItem('successMessage', response.message);
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = "Error updating leave deduction.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'Try Again'
                        }); 
                        $('#message-container').html('<div class="alert alert-danger">' + errorMessage + '</div>');
                    }
                });
            }
        });

        var successMessage = localStorage.getItem('successMessage');
        if (successMessage) {
            $('#message-container').html('<div class="alert alert-success">' + successMessage + '</div>');
            localStorage.removeItem('successMessage'); 
        }
    });

    jQuery(function ($) {

    var current_user_roll = "<?php echo $current_user_roll ?>";

    // ===============================
    // DataTable Init (ONLY ONCE)
    // ===============================
    var table = $("#salary_deduction_tbl").DataTable({
        responsive: true,
        order: current_user_roll == 2 ? [[1, 'desc']] : [[0, 'desc']],
        lengthChange: false,
        autoWidth: false,
        pageLength: 10,
        dom: 'Bfrtip',
        buttons: ['csv', 'excel', 'pdf', 'print'],
        columnDefs: [
            { type: 'date', targets: [1] },
        ],
    });

    // ===============================
    // Find column indexes dynamically
    // ===============================
    var EmpIndex = -1;
    var LeaveTypeIndex = -1;

    $('#salary_deduction_tbl th').each(function (i) {
        var text = $(this).text().trim();
        if (text === 'Employee') EmpIndex = i;
        if (text === 'LvType') LeaveTypeIndex = i;
    });

    // ===============================
    // Custom Role-wise Filter Logic
    // ===============================
    $.fn.dataTable.ext.search.push(function (settings, data) {

        // ---- Financial Year (common) ----
        var fyVal = $('#financial_year').val();
        var fyIndex = (current_user_roll == 2) ? 5 : 4;
        var fy = data[fyIndex] || "";

        // Role 1 → ONLY Year filter
        if (current_user_roll != 2) {
            return fyVal === "" || fy.includes(fyVal);
        }

        // ---- Role 2 → All filters ----
        var empVal = $('#employees_list').val();
        var emp = EmpIndex > -1 ? data[EmpIndex] : "";

        var lvVal = $('#employees_lvtype').val();
        var lv = LeaveTypeIndex > -1 ? data[LeaveTypeIndex] : "";

        if (
            (empVal === "" || emp.includes(empVal)) &&
            (lvVal === "" || lv.includes(lvVal)) &&
            (fyVal === "" || fy.includes(fyVal))
        ) {
            return true;
        }

        return false;
    });

    // ===============================
    // Redraw on filter change
    // ===============================
    $('#financial_year').on('change', function () {
        table.draw();
    });

    if (current_user_roll == 2) {
        $('#employees_list, #employees_lvtype').on('change', function () {
            table.draw();
        });
    }

    // ===============================
    // Set default current financial year
    // ===============================
    function getCurrentFinancialYear() {
        var today = new Date();
        if ((today.getMonth() + 1) <= 3) {
            return (today.getFullYear() - 1) + "-" + today.getFullYear();
        }
        return today.getFullYear() + "-" + (today.getFullYear() + 1);
    }

    var defaultYear = getCurrentFinancialYear();
    $('#financial_year').val(defaultYear).trigger('change');
    $('#CurrentFY').text(defaultYear);

});

</script>