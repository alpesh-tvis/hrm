@extends('admin.master')
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
@section('content')
@php
    use App\Models\Leave;
    
        $get_smallest_date = Leave::orderBy('leave_date','asc')->first();
        $get_largest_date = Leave::orderBy('leave_date','desc')->first();

        $ex_month = explode('-', $get_smallest_date->leave_date);
        $ex_month1 = explode('-', $get_largest_date->leave_date);

        if ($ex_month[1] <= '3') {
            $startdate = $ex_month[0]-1;
            
        } else {
            $startdate = $ex_month[0];
        }

        if ($ex_month1[1] <= '3') {
            $enddate = $ex_month1[0];

        } else {
            $enddate = $ex_month1[0] + 1;
        }
    
    $finc_years = range($startdate, $enddate); 
@endphp
<style>
.hidden_table {
  display: none;
}	
</style>
	<section class="content leavesEmployee">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 text-right pad0">
					<a href="{{route('leave.create')}}" class="btn btn-primary">Add Leave</a>
                </div>
				<div class="col-12 pad0">
					<div class="card">
						<div class="card-body">

							@if(session('success'))
						    	<div class="alert alert-success">
						        	{{ session('success') }}
						    	</div>
							@endif
							@php
								$user_id = Auth::id();
        						$check_department = \App\Models\Employee::where('id',$user_id)->first();

        						$all_user = \App\Models\Employee::get();
        						$admin_user = \App\Models\User::where('id',$user_id)->first();
        						
        						if($check_department->reporting_person == 1 && $admin_user->role == 2){
        							$dataArray = $all_user->pluck('id')->toArray();
        							$dataArray[] = $dataArray;
        						}else if($check_department->reporting_person == 1 && $admin_user->role == 1){
									$get_assign_empl = \App\Models\Employee::where('reporting_person',$user_id)->select('id')->get();
        							$dataArray = $get_assign_empl->pluck('id')->toArray();
        							$dataArray[] = $user_id;
        							
        						}else {
        							$dataArray[] = $user_id;
        						}	
        					@endphp
        					  
							<div class="ajax_msg" style="display:none;">
								<div class="alert alert-success">
									<div id="msg-show"></div>
								</div>	
							</div>

							<div class="row">
								<div class="col-md-3 col-sm-6 col-12">
									<div class="info-box">
										<span class="info-box-icon bg-info">
											<i class="fas fa-holly-berry"></i>
										</span>
										<div class="info-box-content">
											<h5 class="info-box-text">Casual leave</h5>
											<h4 class="info-box-number" id="casual_leave_count">{{$total_Cl}}</h4>
										</div>
									</div>
								</div>
								<div class="col-md-3 col-sm-6 col-12">
									<div class="info-box">
										<span class="info-box-icon bg-info">
											<i class="fas fa-holly-berry"></i>
										</span>
										<div class="info-box-content">
											<h5 class="info-box-text">Personal leave</h5>
											<h4 class="info-box-number" id="personal_leave_count">{{$total_Pl}}</h4>
										</div>
									</div>
								</div>
								<div class="col-md-3 col-sm-6 col-12">
									<div class="info-box">
										<span class="info-box-icon bg-info">
											<i class="fas fa-holly-berry"></i>
										</span>
										<div class="info-box-content">
											<h5 class="info-box-text">Sick leave</h5>
											<h4 class="info-box-number" id="sick_leave_count">{{$total_Sl}}</h4>
										</div>
									</div>
								</div>
                            </div>
                            
                            
							<div class="row">
								<div class="col-md-2">
									<div class="form-group">
										<label for="financial_year" class="form-label">Financial Year :: <span id='CurrentFY'></span></label>
										<select id="financial_year" class="form-control financial_year">
								           <option value="">All Financial Year </option>
								            @foreach ($finc_years as $year)
								                @if ((substr($year, 0) + 1) <= substr($enddate, 0))
							                    <option value="{{ $year }}-{{ (substr($year,0) + 1) }}">{{ $year }}-{{ (substr($year,0) + 1) }}</option>
							                    @endif
							                @endforeach
								        </select>
								    </div>    
							    </div>    

						        @if($employees_list)
						        <div class="col-md-2">
							        <label for="employees_list" class="form-label">Employees</label>
									<select id="employees_list" class="form-control employees_list">
							           <option value="">--- Select All Employees --- </option>
							            @foreach ($employees_list as $employee)
							                <option value="{{ $employee->first_name }} {{ $employee->last_name }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
						                @endforeach
							        </select>
							    </div>    
							    @endif
							</div>        
						    
						    <table id="leave_tbl" class="table table-bordered table-striped display">
	                            <thead>
	                                <tr>
	                                	
	                                		<th>Employee</th>
	                                	
	                                    <th>Leave Date</th>
	                                    <th>Leave Reason</th>
	                                    <th>Leave Type</th>
	                                    <th>Leave Status</th>
	                                    <th>Status</th>
	                                    <th class="hidden_table">Financial Year</th>
	                                    <th>Action</th>
	                                </tr>
	                            </thead>
                            
	                            <tbody>
	                            	@php
	                            		$leave_type = [
											'PL'=>'Paid', 
											'SL'=>'Sick', 
											'CL'=>'Casual'
										];

										$leave_status = [
											'F'=>'Full Day', 
											'FH'=>'First Half', 
											'SH'=>'Second Half'
										];
										$status = [
											'Pending'=>'btn-primary', 
											'Approved'=>'btn-success', 
											'Rejected'=>'btn-danger',
											'Cancelled'=>'btn-danger'
										];

									@endphp	
	                                @foreach($leaves as $leave)
	                                	@php
											$get_employee_name = \App\Models\Employee::where('id',$leave->user_id)->first();
											$leave_date = \Carbon\Carbon::parse($leave->leave_date)->format('l, F j, Y');
	                                	@endphp
	                                	
	                                	@if (in_array($leave->user_id, $dataArray)) 
	                                	    <tr>
			                                	
			                                		<td>{{$get_employee_name->first_name}} {{$get_employee_name->last_name}}</td>
			                                	
			                                    <td data-order="{{$leave->leave_date}}">{{$leave_date}}</td>
			                                    <td>{{$leave->leave_reason}}</td>
			                                    <td>{{$leave_type[$leave->leave_type]}}</td>
			                                    <td>{{$leave_status[$leave->leave_status]}}</td>
			                                    <td>
			                                    	{{$leave->status}}
												</td>
												<td class="hidden_table">
												    @php
												        $leaveDate = \Carbon\Carbon::parse($leave->leave_date);
												        $financialYearStart = $leaveDate->month >= 4 ? $leaveDate->year : $leaveDate->year - 1;
												        $financialYearEnd = $financialYearStart + 1;
												    @endphp
												    {{ $financialYearStart }}-{{ $financialYearEnd }}
												</td>

			                                    <td class="dFlex">

			                                    	@php
													    $dateToCheck = \Carbon\Carbon::parse($leave->leave_date);
													    $today = \Carbon\Carbon::now();
													@endphp

													@if($role == '2' || $check_department->reporting_person == '1')
														@if($get_employee_name->id !=$user_id)
															<input type="hidden" name="leave_id" value="{{$leave->id}}">
															<select name="leave_type" id="leave_type" class="form-control leave_type margin0" leave-id="{{$leave->id}}" style="display:inline; margin-right: 10px;" >
																<option value="" readonly>---Select---</option>
					                                           	<option value="Approved" {{ ( $leave->status == 'Approved') ? 'selected' : '' }}>Approve</option>
					                                           	<option value="Cancelled" {{ ( $leave->status == 'Cancelled') ? 'selected' : '' }}>Cancelled</option>
					                                            <option value="Rejected" {{ ( $leave->status == 'Rejected') ? 'selected' : '' }}>Reject</option>
					                                        </select>
					                                        <a href="{{ route('leave.edit',$leave->id) }}" class="btn btn-primary" style="margin-top:10px">Edit</a>

														<form action="{{route('leave.destroy',$leave->id) }}" method="POST" class="margin0">
														       	@method('DELETE')
														       	@csrf
														       	<input type="hidden" name="delete_comment" value="1">
														       	<button class='btn btn-danger float-right btn-sm' type="submit" style="margin-top:10px" onclick="return confirm('Sure Want Delete?')" ><i class="fas fa-user-times mr-2"></i>Delete</button>
														    </form>
														@endif
													@endif
													
													@if(!$dateToCheck->lt($today))
														@if($role == '1' && $get_employee_name->id == $user_id)
				                                    		@if($leave->status !='Cancelled')
						                                    	<form action="{{ route('leave.destroy',$leave->id) }}" method="post" style="display:inline">
					                                            	@csrf
					                                            	{{ method_field('delete') }}
					                                            	<input type="hidden" name = "can_leave" value="can_leave">
					                                            	<button class="btn btn-danger show-alert-delete-box" type="submit">Cancle Leave?</button>
					                                        	</form>
				                                        	@else
				                                        		Already Cancelled.
				                                        	@endif
				                                    	@endif
				                                    @endif	    	
		                                    	</td>
			                                </tr>
			                            @endif    
		                            @endforeach    
	                            </tbody>
	                            <tfoot>
		                            <tr>
		                            	
		                                	<th>Employee</th>
		                                
		                                <th>Leave Date</th>
		                                <th>Leave Reason</th>
		                                <th>Leave Type</th>
		                                <th>Leave Status</th>
		                                <th>Status</th>
		                                <th class="hidden_table sorting">Financial Year</th>
		                                <th>Action</th>
		                            </tr>
	                            </tfoot>
                        	</table>
						</div>	
					</div>	
				</div>	
			</div>	
		</div>	
	</section>	
@endsection

<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.js')}}"></script>

<script type="text/javascript">
jQuery(function ($) {
    var role = "<?php echo $role ?>";
    var reporting_person = "<?php echo $check_department->reporting_person ?>";
    var order = (role == 2 || reporting_person == 1) ? [1, 'desc'] : [0, 'desc'];
    var selectedFY = "";

    var table = $("#leave_tbl").DataTable({
        responsive: true,
        order: order,
        lengthChange: false,
        autoWidth: false,
        pageLength: 100,
    });

    // Append filters to DataTables filter area
    $("#leave_tbl.dataTables_filter").append($("#financial_year"));

    var YearIndex = 0;
    var empIndex = 0;

    // Find index of 'Financial Year' and 'Employee' columns
    $("#leave_tbl thead th").each(function (i) {
        var headerText = $(this).text().trim();
        if (headerText === "Financial Year") {
            YearIndex = i;
        }
        if (headerText === "Employee") {
            empIndex = i;
        }
    });

    // DataTable custom filter
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        var selectedYear = $('#financial_year').val();
        var selectedEmp = $('#employees_list').length ? $('#employees_list').val() : "";

        var rowYear = data[YearIndex] || "";
        var rowEmp = data[empIndex] || "";

        var yearMatch = selectedYear === "" || rowYear.includes(selectedYear);
        var empMatch = selectedEmp === "" || rowEmp.includes(selectedEmp);

        return yearMatch && empMatch;
    });

    // Filter by financial year
    $("#financial_year").change(function () {

	    selectedFY = $(this).val();

	    table.draw();

	    loadEmployeeDropdown(selectedFY);
	    updateLeaveCountsFromTable();
	});

    // Filter by employee
    $("#employees_list").change(function () {
        table.draw();
         setTimeout(updateLeaveCountsFromTable, 100);
    });

    // Set default financial year (April to March)
    function getCurrentFinancialYear() {
        var today = new Date();
        var fyStart = (today.getMonth() + 1) <= 3 ? today.getFullYear() - 1 : today.getFullYear();
        return fyStart + "-" + (fyStart + 1);
    }

    var defaultYear = getCurrentFinancialYear();
    $("#financial_year").val(defaultYear).trigger('change');
    document.getElementById("CurrentFY").innerHTML = defaultYear;

    $(document).ready(function () {
	    updateLeaveCountsFromTable();
	});

    function loadEmployeeDropdown(fy) {

    var employees = [];

    table.rows().every(function () {

        var data = this.data();

        var rowYear = (data[YearIndex] || "").trim();

        // STRICT FY match (NOT includes)
        if (fy === "" || rowYear === fy) {

            var emp = (data[empIndex] || "").trim();

            if (emp && employees.indexOf(emp) === -1) {
                employees.push(emp);
            }
        }
    });

    var $dropdown = $("#employees_list");
    var current = $dropdown.val();

    $dropdown.empty();
    $dropdown.append(`<option value="">--- Select All Employees ---</option>`);

    employees.sort().forEach(function (emp) {
        $dropdown.append(`<option value="${emp}">${emp}</option>`);
    });

    if (employees.includes(current)) {
        $dropdown.val(current);
    }
}
	function updateLeaveCountsFromTable() {
		    var casual = 0, personal = 0, sick = 0;
		    
		    // Get column indexes dynamically by header text
		    

		    table.rows({ search: 'applied' }).every(function () {
		        var rowData = this.data();
		        var leaveReason = (rowData[2] || "").toLowerCase().trim(); // Column index 2 = Leave Reason
		        var leaveType = (rowData[3] || "").toLowerCase().trim();   // Column index 3 = Leave Type
		        var leaveStatus = (rowData[4] || "").toLowerCase().trim();   // Column index 3 = Leave Type
		        var leaveSts = (rowData[5] || "").toLowerCase().trim();   // Column index 3 = Leave Type

		        // Debug: check data
		        //console.log("Type:", leaveType, "| Reason:", leaveReason, "| Status:", leaveSts);
		        // Determine if it's a half day
                var dayValue = (leaveStatus === 'first half' || leaveStatus === 'second half') ? 0.5 : 1;

		        if(leaveSts == 'approved') 
		        {
		        	if (leaveType.includes("casual")) casual += dayValue;
			        else if (leaveType.includes("sick")) sick += dayValue;
			        else if (leaveType.includes("paid")) personal += dayValue;

		        }
		        
		    });
		    
		    $('#casual_leave_count').text(casual);
		    $('#personal_leave_count').text(personal);
		    $('#sick_leave_count').text(sick);
		}

});


$( document ).ready(function() {
    $(document).on("change",'select.leave_type',function(){
    	var leave_id_attr = $(this).closest('select').attr('leave-id');
    	var status = $(this).val();
        $.ajax({
    		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    		data : { id: leave_id_attr, status: status},
    		url: "/leave/"+leave_id_attr,
    		type: "GET",
    		success: function (response) {
        if (response.success == 1) {

            $("#msg-show").html(response.message ?? "Status Updated Successfully!");

            $(".ajax_msg")
                .fadeIn()
                .delay(2000)
                .fadeOut();

            location.reload(true);
        }
    }
    	});	
    });
}); 


</script>