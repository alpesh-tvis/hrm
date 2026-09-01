@extends('admin.master')
@section('content')
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">    

	<section class="content">
		@if(session()->has('message'))
    		<div id="alert" class="alert alert-success">
        		{{ session()->get('message') }}
    		</div>
    		<script>
        		setTimeout(function(){
            		var alert = document.getElementById('alert');
            		alert.style.opacity = '0';
            		setTimeout(function(){
                		alert.style.display = 'none';
            		}, 1000); 
        		}, 5000); 
    
    		</script>
    		
		@endif
		<div class="card-body">
			<table class="table table-bordered table-striped display">
			    <thead>
			        <tr>
			            <th>Users</th>
			            <th>Mon</th>
			            <th>Tue</th>
			            <th>Wed</th>
			            <th>Thu</th>
			            <th>Fri</th>
			            <th>Sat</th>
			            <th>Sun</th>
			            <th>Action</th>
			        </tr>
			    </thead>
	    		<tbody>
	        		@foreach($get_sales_persons as $person)
	            		<tr>
	                		<form action="{{ route('shift-settings.store') }}" method="POST">
	                    		@csrf
	                    		<td>{{ $person->first_name }}</td>
	                    			@foreach(['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'] as $day)
				                        @php
				                            $employeeShifts = $shifts->get($person->id, collect());
				                            $shift1 = optional($employeeShifts->where('shift', 1)->first())->$day;
				                            $shift2 = optional($employeeShifts->where('shift', 2)->first())->$day;
				                        @endphp
			                        	<td>
				                            <div class="form-group">
				                                <label>Shift 1</label>
				                                <input type='time' name="shift1[{{ $day }}]" class="form-control" value="{{ $shift1 }}" />
				                                @if($shift1)
				                                	<a href="#" class="clear-link" data-target="shift1[{{ $day }}]">Remove</a>
				                                @endif	
				                            </div>
				                            <div class="form-group">
				                                <label>Shift 2</label>
				                                <input type='time' name="shift2[{{ $day }}]" class="form-control" value="{{ $shift2 }}" />
				                                @if($shift2)
				                                	<a href="#" class="clear-link" data-target="shift2[{{ $day }}]">Remove</a>
				                                @endif	
				                            </div>
			                        	</td>
	                    			@endforeach
	                    		<td>
			                        <input type="hidden" name="employee_id" value="{{ $person->id }}">
			                        <input type="hidden" name="shift1[shift]" value="1">
			                        <input type="hidden" name="shift2[shift]" value="2">
			                        <input type="submit" class="btn btn-primary form-control" value="Update">
	                    		</td>
	                		</form>
	            		</tr>
	        		@endforeach
	    		</tbody>
			</table>

		</div>	
	</section>	

	<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.clear-link').forEach(function(link) {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                var targetName = this.getAttribute('data-target');
                document.querySelector(`input[name="${targetName}"]`).value = '';
            });
        });
    });
</script>
	<!-- DataTables  & Plugins -->
	<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
	<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
	<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
	<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
	<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
	<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.js')}}"></script>
@endsection