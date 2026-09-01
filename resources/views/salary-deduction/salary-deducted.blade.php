@extends('admin.master')

@section('content')
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">     
<style>
.leaf-button {
    position: relative;
    padding: 10px 20px;
    font-size: 10px;
    font-weight: bold;
    color: white;
    text-transform: uppercase;
    border: none;
    cursor: pointer;
    border-radius: 25px;
    outline: none;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}
.leaf-link {
    font-size: 10px;
    font-weight: bold;
    color: white;
    display: inline-block;
    padding: 8px 16px;
    border-radius: 25px;
    text-align: center;
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
}
.leaf-button:hover, .leaf-link:hover {
    transform: scale(1.05);
    background-color: #45a049;
}

/* Base styles for the buttons */
.dt-buttons .btn,
.dt-buttons button {
    display: inline-block;
    font-size: 14px; /* Slightly larger font */
    font-weight: bold; /* Bold text for better visibility */
    padding: 8px 15px; /* Comfortable padding */
    border: none; /* Remove borders for cleaner look */
    border-radius: 25px; /* Fully rounded buttons */
    cursor: pointer;
    text-transform: uppercase; /* Consistent uppercase text */
    color: #fff; /* White text */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Subtle shadow */
    transition: all 0.3s ease-in-out; /* Smooth hover effects */
    margin: 5px; /* Space between buttons */
}

/* Copy button */
.dt-buttons .buttons-copy {
    background: linear-gradient(45deg, #36d1dc, #5b86e5); /* Teal to blue gradient */
}
.dt-buttons .buttons-copy:hover {
    background: linear-gradient(45deg, #5b86e5, #36d1dc); /* Reverse gradient on hover */
    transform: scale(1.05); /* Slightly enlarge */
    box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15); /* Stronger shadow */
}

/* CSV button */
.dt-buttons .buttons-csv {
    background: linear-gradient(45deg, #11998e, #38ef7d); /* Green gradient */
}
.dt-buttons .buttons-csv:hover {
    background: linear-gradient(45deg, #38ef7d, #11998e); /* Reverse gradient */
    transform: scale(1.05);
    box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
}

/* Excel button */
.dt-buttons .buttons-excel {
    background: linear-gradient(45deg, #f7b733, #fc4a1a); /* Orange gradient */
}
.dt-buttons .buttons-excel:hover {
    background: linear-gradient(45deg, #fc4a1a, #f7b733);
    transform: scale(1.05);
    box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
}

/* PDF button */
.dt-buttons .buttons-pdf {
    background: linear-gradient(45deg, #e52d27, #b31217); /* Red gradient */
}
.dt-buttons .buttons-pdf:hover {
    background: linear-gradient(45deg, #b31217, #e52d27);
    transform: scale(1.05);
    box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
}

/* Print button */
.dt-buttons .buttons-print {
    background: linear-gradient(45deg, #6a11cb, #2575fc); /* Purple to blue gradient */
}
.dt-buttons .buttons-print:hover {
    background: linear-gradient(45deg, #2575fc, #6a11cb);
    transform: scale(1.05);
    box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
}

/* Disabled buttons */
.dt-buttons .btn:disabled,
.dt-buttons button:disabled {
    background: #ccc; /* Grey background for disabled */
    color: #777; /* Dimmed text */
    cursor: not-allowed; /* Not-allowed cursor */
    box-shadow: none; /* Remove shadow */
}

/* Optional: Center align the buttons */
.dt-buttons {
    text-align: center;
    margin-bottom: 20px; /* Space below button group */
}
</style>

<section class="content">
<div class="card-body">
    <div class="salary-deduction-file-format">
    <div class="row ">
        <div class="col-md-2">
           <label for="financial_years" class="form-label">Financial Year :: <span id='CurrentFY'></span></label>
            <select id="financial_years" class="form-control form-control-sm financial_years" data-emp-id="{{ $current_user_roll }}">
                <option>--Select--</option>
            </select>
        </div>
        @if($current_user_roll == 2)
            @if($employeerec)
                <div class="col-md-2">
                   <label for="employees_select" class="form-label">Emplyoee </label>
                    <select id="employees_select" class="form-control">
                    <option value="">--- Select All Employees --- </option> 
                      @foreach($employeerec as $empname)
                       <option value="{{ $empname->first_name }}">{{ $empname->first_name }}</option>
                      @endforeach
                    </select>
               </div>
        @endif
        <div class="col-md-2">
           <label for="lvtype" class="form-label">Leave Type </label>
            <select id="lvtype" class="form-control">
            <option value="">--- Select --- </option>
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
    </div>
    <table id="salary_deducted_tbl" class="table table-bordered table-striped display">
        <thead>
            <tr>
                <th>Financial Year</th>
                @if($current_user_roll == 2)
                <th>Employee</th>
                @endif
                <th>LvType</th>
                <th>Salary Deduction</th>
                <th>LvDate</th>
                <th>Month</th>
                <th>Adjust</th>
                @if($current_user_roll == 2)
                <th>Action</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($getSalaryDeductedData as $data)
                 @php 
                    $my_date = $data['date'];
                    $formatted_date = date("F j, Y", strtotime($my_date));
                    $day = date("l", strtotime($my_date));
                 @endphp
                <tr>
                    <td>{{ $data['financial_year'] }}</td> 
                    @if($current_user_roll == 2)
                        <td>
                            <a href="#" 
                               class="btn btn-success over-day-view" 
                               data-toggle="modal" 
                               data-target="#details-modal-deducted-{{ $data['id']}}" 
                               style="font-size: 12px;">
                                <strong style="color: white;">
                                   <i class="fas fa-user prefix grey-text"></i> {{ $data['employee_name'] }}
                                </strong>
                            </a>
                       </td> 
                    @endif
                    <td>{{ $data['leave_type'] }}</td> 
                    <td>{{ $data['salary_deduction'] }}</td> 
                    <td>{{ $day }}, {{ $formatted_date }}</td> 
                    <td>{{ $data['month'] }}</td> 
                    <td>
                       <!-- Leaf-styled Link -->
                        <!-- <a href="" 
                           class="leaf-link over-day-view" 
                           data-toggle="modal" 
                           data-target="#details-modal-adj" 
                           style="background: {{ $data['is_adjusted'] === 'Yes' ? '#28a745' : '#dc3545' }}; 
                                  border: 1px solid {{ $data['is_adjusted'] === 'Yes' ? '#28a745' : '#dc3545' }};" 
                           target="_blank">
                            <span>{{ $data['is_adjusted'] }}</span>
                        </a> -->

                        <button class="leaf-button" 
                                style="background-color: {{ isset($data['leave_deduct']) ? '#4CAF50' : '#FF6347' }};">
                            {{ $data['leave_deduct'] ?? 'Deducted' }}
                        </button>
                    </td> 
                    
                    @if($current_user_roll == 2)
                         <td>
                            <a href="#" 
                               class="btn btn-success over-day-view" 
                               data-toggle="modal" 
                               data-target="#details-modal-deducted-{{ $data['id']}}" 
                               style="font-size: 12px;">
                                <strong style="color: white;">
                                   <i class="fas fa-eye"></i>
                                </strong>
                            </a>
                            <div id="details-modal-deducted-{{ $data['id']}}" class="modal fade modal-top-right" tabindex="-1" role="dialog" aria-labelledby="details-modal-deducted-{{ $data['id']}}" aria-hidden="true">
                                <div class="modal-dialog modal-top-right">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <div class="modal-body">
                                            <div class="form-group">
                                            <i class="fas fa-user prefix grey-text"></i>
                                            <label for="emp_name" class="form-label">
                                               {{ $data['employee_name']}}
                                           </label>
                                            </div>
                                            <div class="form-group">
                                                <label for="form2">LvType : {{$data['leave_type']}}
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <label for="form2">Salary Deduction : {{$data['salary_deduction']}}
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <label for="form2">LvDate : {{$data['date']}}
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <label for="form2">Month : {{$data['month']}}
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <label for="form2">Adjust :
                                                    <button class="leaf-button" 
                                                            style="background-color: {{ isset($data['leave_deduct']) ? '#4CAF50' : '#FF6347' }};">
                                                        {{ $data['leave_deduct'] ?? 'Deducted' }}
                                                    </button>                                     
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <label for="form2">Financial Year : {{$data['financial_year']}}
                                                </label>   
                                            </div>
                                            </div>
                                        </div>
                                        <div class="modal-body">
                                        </div>
                                    </div>
                                </div>
                            </div>   
                        </td>
                    @endif
                </tr>
            @endforeach
         </tbody>
        <tfoot>
            <tr>
                <th>Financial Year</th>
                @if($current_user_roll == 2)
                <th>Employee</th>
                @endif
                <th>LvType</th>
                <th>Salary Deduction</th>
                <th>LvDate</th>
                <th>Month</th>
                <th>Adjust</th>
                @if($current_user_roll== 2)
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

<script src="{{asset('plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="{{asset('plugins/jszip/jszip.min.js')}}"></script>
<script src="{{asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>

<script>
jQuery(function ($) {
    var current_user_roll = "<?php echo $current_user_roll ?>";
    var order = current_user_roll == 2 ? [4, 'desc'] : [0, 'desc'];

    $("#salary_deducted_tbl").DataTable({
        "responsive": true,
        "order": order,
        "lengthChange": false,
        "autoWidth": false,
        "pageLength": 10,
        dom: 'Bfrtip',
        buttons: ['csv', 'excel', 'pdf', 'print'],
        columnDefs: [
           { type: 'date', targets: [4] }, 
        ],
    });
    
    var lvTypetables = $('#salary_deducted_tbl').DataTable();
    //emplyoee filter
    $("#salary_deducted_tbl.dataTables_filter").append($("#employees_select"));
    
    var EmpIndex = 0;
    $("#salary_deducted_tbl th").each(function (i) {
        if ($($(this)).html() == "Employee") {
            EmpIndex = i; 
            return false;
        }
    });
    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var EmpselectedItem = $('#employees_select').val();
            var Emp = data[EmpIndex];
            if (EmpselectedItem === "" || Emp.includes(EmpselectedItem)) {
              return true;
            }
            
            return false;
        }
    );
    $("#employees_select").change(function (e) {
        lvTypetables.draw();
    });

    //LeaveType
    $("#salary_deducted_tbl.dataTables_filter").append($("#lvtype"));
    var LeaveTypeIndex = 0;
    $("#salary_deducted_tbl th").each(function (i) {
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
        lvTypetables.draw();
    });

    
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
        let dropdown = $("#financial_years");

        for (let year = startYear; year <= currentYear + 1; year++) {
            let optionValue = `${year}-${year + 1}`;
            dropdown.append(new Option(optionValue, optionValue));
        }
    }

    populateFinancialYearDropdown();

    var defaultYear = getCurrentFinancialYear();
    $("#financial_years").val(defaultYear).change();
    
    $("#CurrentFY").text(defaultYear);

    // Initialize DataTable
    var table = $("#salary_deducted_tbl").DataTable();

    // Filter DataTable based on selected financial year
    $("#financial_years").on("change", function () {
        var selectedYear = $(this).val();
        var empId = $(this).val('emp-id'); 
        //table.columns(1).search(selectedYear).draw();

        if ($.fn.dataTable.isDataTable("#salary_deducted_tbl")) {
            var table = $("#salary_deducted_tbl").DataTable(); 
            table.column(0).search(selectedYear).draw();
        } else {
        console.error("DataTable is not initialized for #salary_deducted_tbl.");
        }
    });
});
</script>