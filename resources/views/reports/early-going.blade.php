@extends('admin.master')
@section('content')
@php
 use Carbon\Carbon;
@endphp
	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<div class="card card-primary">
                        <div class="card-body">
                        	@if($get_employees != null)
                        		@php
    								$selected_employee_id = request()->get('empl', null);
							@endphp
                        	<form action="{{route('early_going')}}" method="get" class="form-inline justify-content-md-end">
                                <div class="form-group">
                                    <label for="empl" class="pr-2">Employees</label>
                                    <select class="form-control mr-2" id="empl" name="empl">
                                        <option value="">-- Select -- </option>
                                        @foreach($get_employees as $employee) 
                                        <option value="{{ $employee->id }}"  {{ $employee->id == $selected_employee_id ? 'selected' : '' }} > {{ $employee->first_name }} {{ $employee->last_name }}</option> 
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" id="late_coming_submit"  class="btn btn-primary form-control">Find Record</button>
                            </form>
                            @endif
                            <table id="late-coming" class="table table-bordered table-striped display">
                                <thead>
                                    <tr>
                                        <th>Work Date</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Working Hours</th>
                                    </tr>
                                </thead>
                                <tbody>
                                	@foreach($earlygoing as $going)
                                		<tr>
                                           
	                                		<td>{{ $going->work_date }}</td>
                                            <td>{{$going->start_time}}</td>
	                                		<td>{{$going->end_time}} </td>
	                                		<td>{{$going->working_hours}}</td>
                                            
	                                	</tr>
	                                @endforeach	
                                </tbody>
                                
                                <tfoot>
                                    <tr>
                                        <th>Work Date</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Working Hours</th>
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
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">

<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>

<script src="{{asset('plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/jszip/jszip.min.js')}}"></script>
<script src="{{asset('plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.js')}}"></script>

<script type="text/javascript">
jQuery(function ($) {
    $("#late-coming").DataTable({
        responsive: true,
        lengthChange: false,
        autoWidth: false,
        pageLength: 100,
        order: [[0, 'desc']],
        dom: 'Bfrtip', 
        buttons: [
            {
              extend: 'csv',
              text: 'Export CSV'
            }
        ]
    });
});
</script>