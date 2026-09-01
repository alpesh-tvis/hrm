@extends('admin.master')
@section('content')
	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12">
					<form method="get" action="{{route('leave_details')}}" name="leave_details">
			            <div class="row d-flex justify-content-end">
			            	@if($current_user_roll == 2)
				            	@if($employeerec)
				                <div class="col-auto">
				                    <select class="form-control" name="user_list">
				                       <option value="">--- Select All Employees --- </option> 
				                        @foreach($employeerec as $empname)
				                           <option value="{{ $empname->id }}" {{ isset($_GET['user_list']) ? ($empname->id == $_GET['user_list'] ? 'selected' : '') : '' }}>{{ $empname->first_name }} {{ $empname->last_name }}</option>
				                        @endforeach
			                        </select>
			                    </div>
			                    @endif
			                <div class="col-auto">
			                    <input type="submit" name="leave_search" value="Search" class="btn btn-primary submit">
			                </div>
			                @endif			                
			            </div>
			        </form>
					<div class="card">
						<div class="card-body">

							@if($current_user_roll == 2)
								@if($empRecord)
								<div class="row">
	                               <div class="col-md-0">
								      <img src="{{ $empRecord->profile_image}}" style="max-height: 30px;">
								    </div> 
									<div class="col-md-2">
									 <h4 style="color: #007bff;font-weight: 600;font-size: 14px;">{{ $empRecord->first_name }} {{ $empRecord->last_name }} </h4>
									 <h5 style="color: #16a3b8;font-weight: 600;font-size: 12px;">{{ $empRecord->position }}  </h5>
								    </div> 
								</div> 
								@endif	
							@endif


							<table id="leave_details" class="table table-bordered table-striped display">
								<thead>
									<tr>
										<th>No</th>
										<th>Months</th>
										<th>Details</th>
										<th>Leave Count</th>
										<th>Total Leave</th>
									</tr>	
								</thead>
								<tbody>
									@php
										$i = 1;
									@endphp
									@foreach($leave_details as $monthYear => $details)
										<tr>
											<td>{{$i++}}</td>
											<td>{{$monthYear}}</td>
											<td>
												<div style="display: flex; flex-wrap: wrap; justify-content: flex-start; gap: 2%;">
													@foreach($details as $detail)
														<div class="callout callout-info" style="width: 30%; display: inline-block;">
															<p>{{$detail['leave_date']}}</p>
															<span class="badge badge-success">
																@if($detail['leave_type'] == "PL")
																	Paid
																@endif
																@if($detail['leave_type'] == "SL")
																	Sick
																@endif
																@if($detail['leave_type'] == "CL")
																	Casual
																@endif
															</span>
															<span class="badge badge-success">
																@if($detail['leave_status'] == "F")
																	Full
																@endif
																@if($detail['leave_status'] == "FH")
																	First Half
																@endif
																@if($detail['leave_status'] == "SH")
																	Second half
																@endif
															</span>
														</div>
													@endforeach
												</div>	
											</td>
											<td style="vertical-align: unset;">
												@if($detail['full_leave'] > 0 && $detail['half_leave'] > 0)
													<div class="col-12">
												@else
													<div class="col-8">			
												@endif			
													<div class="info-box">
														@if($detail['full_leave'] > 0)
															<div class="info-box-content">
																<span class="info-box-text">Full Day</span>
																<span class="info-box-number">{{$detail['full_leave']}}</span>
															</div>
														@endif

														@if($detail['half_leave'] > 0)
															<div class="info-box-content" style="<?php echo $detail['full_leave'] > 0 ? 'border-left: 1px solid;' : ''; ?>">
																<span class="info-box-text">Half Day</span>
																<span class="info-box-number">{{$detail['half_leave']}}</span>
															</div>
														@endif	
													</div>
												</div>
											</td>
											<td>{{$detail['total_leave']}}</td>
										</tr>
									@endforeach	
								</tbody>	
							</table>
						</div>	
					</div>	
				</div>	
			</div>	
		</div>	
	</section>	
@endsection

<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script type="text/javascript">
	jQuery(function ($) {
		$("#leave_details").DataTable({
        	"responsive": true,
        	"lengthChange": false,
        	"autoWidth": false,
        	"pageLength": 100,
    	});
	});	
</script>	