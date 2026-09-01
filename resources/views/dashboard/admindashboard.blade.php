@extends('admin.master')
@section('content')
	
	<section class="content">
		<div class="container-fluid">
			<div class="row mt-5">
				<div class="col-md-6">
					<div>
		                <h3> Recent Employee On Leave </h3>
	                    <div class="small-box" style="height: auto;">
		                	<div class="card-body">
								<table class="table">
								    <thead>
								    	<tr>
									       <th scope="col"><i class="nav-icon fas fa-user"></i>  Employee Name </th>
									       <th scope="col"><i class="nav-icon fas fa-holly-berry"></i> Day </th>
									       <th scope="col"><i class="nav-icon fas fa-calendar-alt" aria-hidden="true"></i> Date</th>
								    	</tr>
								  	</thead>
								  	<tbody>
										@forelse($recent_emp_leave as $leavedata)
										    <tr>
										        <td>{{ $leavedata->employee->first_name}}</td>
										        <td>
										            {{ [
										               'F'  => 'Full Day',
										               'FH' => 'First Half',
										               'SH' => 'Second Half'
										            ][$leavedata->leave_status] ?? 'Unknown' }}
										        </td>
										        <td>{{ \Carbon\Carbon::parse($leavedata->leave_date)->format('l, F j, Y') }}</td>
										    </tr>
										@empty
										    <tr>
										        <td colspan="3">No employee has leave for next 15 days.</td>
										    </tr>
										@endforelse
								  	</tbody>
								</table>
							</div>	
		                </div>
		            </div>    
				</div>
				<div class="col-md-6">
	                <h3> Email Request - Late Coming & Early Going </h3>
					<div class="small-box" style="height: auto;">
	                	<div class="card-body">
							<table class="table">
							    <thead>
								    <tr>
								    	<th scope="col"><i class="nav-icon fas fa-wind"></i> Employee </th>
										<th scope="col"><i class="nav-icon fas fa-wind"></i> Subject </th>
										<th scope="col"><i class="nav-icon fas fa-calendar-alt" aria-hidden="true"></i>  Date </th>
										<th scope="col"><i class="fas fa-circle"></i> Status</th>
								    </tr>
							  	</thead>
							    <tbody>
								    @forelse($all_latecoming_earlycoming as $dataVal)
								    	<tr>
								    		<td>{{ $dataVal->user->name ?? 'N/A' }}</td>
								    		<td>{{ $dataVal->subject }}</td>
								    		<td>{{ \Carbon\Carbon::parse($dataVal->request_date)->format('l, F j, Y') }}</td>
								    		<td>{{ $dataVal->status }}</td>
								    	</tr>
								    @empty
								    	<tr>
								    		<td colspan="4">No employees have any pending email requests for late coming or early going.</td>
								    	</tr>	
								    @endforelse
							    </tbody>
							</table>
	                 	</div>
	                </div>
				</div>
			</div>	
		</div>	
	</section>	
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script type="text/javascript">
	$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

	jQuery(function ($) {
		$('#projects').on('change', function(e) {
			
	        e.preventDefault();
	        id = $(this).val();
	        $.ajax({
	            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	            data: { id: id},
	            url: "{{ route('dashboard.store') }}",
	            type: "POST",
	            beforeSend: function() {
                    $(".spinner-wrapper").show();
                },
	            success: function (response) {
	                $("#project-data").html('');
	                $.each(response.success, function (key, value) {
						$('#project-data').append("<tr>\
								<td>"+value.work_date+"</td>\
								<td>"+value.name+"</td>\
								<td>"+value.work_time+"</td>\
							</tr>");
					});
					$("#data-none").hide();
					$(".spinner-wrapper").hide();
	            }
	        });
	    });
	});    
</script>