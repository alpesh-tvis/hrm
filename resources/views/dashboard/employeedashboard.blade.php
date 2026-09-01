@extends('admin.master')

@section('content')

<section class="content">

	<div class="container-fluid">

	<h3 class="m-b-20 ">Welcome Back!</h3>

	 <div class="row info-detail leave_row">

	 	<div class="col-lg-7">

	 		<div class="row">

	            <div class="col-lg-4 personal_leave">

	                <div class="small-box">

	                    <div class="inner">

	                        <p>Personal <br> Leaves</p>

	                        <h3 class="leave_info">{{ $total_Pl }} / {{ $all_count_PL }}</h3>

							@if($remainLeavePl < 0)

							<p class="remain_leave"><strong  style="font-size: 14px;">{{ $remainLeavePl }}</strong> </p>

							@else

							<p class="remain_leave">Remaining : <strong  style="font-size: 14px;">{{ $remainLeavePl }}</strong> </p>

							@endif

	                    </div>

	                    <div class="icon">

	                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 50 50" fill="none">

								<path d="M45.8334 14.5833L29.4404 30.9763C28.6153 31.8013 28.2028 32.2139 27.7271 32.3684C27.3087 32.5044 26.858 32.5044 26.4396 32.3684C25.9639 32.2139 25.5514 31.8013 24.7263 30.9763L19.0237 25.2737C18.1987 24.4487 17.7862 24.0361 17.3105 23.8816C16.892 23.7456 16.4413 23.7456 16.0229 23.8816C15.5472 24.0361 15.1347 24.4487 14.3097 25.2737L4.16669 35.4167M45.8334 14.5833H31.25M45.8334 14.5833V29.1667" stroke="#28A745" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								</svg>

                         </div>

	                </div>

	            </div>

	            <div class="col-lg-4 sick_leave">

	                <div class="small-box ">

	                    <div class="inner">

	                    	<p>Sick <br> Leaves</p>

	                        <h3 class="leave_info">{{ $total_Sl }} / {{ $all_count_SL }}</h3>

		                       	@if($remainLeaveSl < 0)

								<p class="remain_leave"><strong  style="font-size: 14px;">{{ $remainLeaveSl }}</strong> </p>

								@else

								<p class="remain_leave">Remaining : <strong  style="font-size: 14px;">{{ $remainLeaveSl }}</strong> </p>

								@endif

	                    </div>

	                    <div class="icon">

							<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 50 50" fill="none">

							<path d="M45.8333 35.4167L29.4403 19.0237C28.6153 18.1987 28.2028 17.7861 27.7271 17.6316C27.3087 17.4956 26.8579 17.4956 26.4395 17.6316C25.9638 17.7861 25.5513 18.1987 24.7263 19.0237L19.0236 24.7263C18.1986 25.5513 17.7861 25.9639 17.3104 26.1184C16.892 26.2544 16.4413 26.2544 16.0228 26.1184C15.5472 25.9639 15.1346 25.5513 14.3096 24.7263L4.16663 14.5833M45.8333 35.4167H31.25M45.8333 35.4167V20.8333" stroke="#FF0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

							</svg>

	                    </div>

	                </div>

	            </div>

	            <div class="col-lg-4 causal_leave">

	                <div class="small-box ">

	                    <div class="inner">

	                        <p>Casual <br> Leaves</p>

	                        <h3 class="leave_info">{{ $total_Cl }} / {{ $all_count_CL }}</h3>

							 @if($remainLeaveCl < 0)

							 <p class="remain_leave"> <strong  style="font-size: 14px;">{{ $remainLeaveCl }}</strong> </p>

                             @else

							 <p class="remain_leave">Remaining : <strong  style="font-size: 14px;">{{ $remainLeaveCl }}</strong> </p>

                             @endif

	                    </div>

	                    <div class="icon">

	                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 50 50" fill="none">

						<path d="M45.8333 35.4167L29.4403 19.0237C28.6153 18.1987 28.2028 17.7861 27.7271 17.6316C27.3087 17.4956 26.8579 17.4956 26.4395 17.6316C25.9638 17.7861 25.5513 18.1987 24.7263 19.0237L19.0236 24.7263C18.1986 25.5513 17.7861 25.9639 17.3104 26.1184C16.892 26.2544 16.4413 26.2544 16.0228 26.1184C15.5472 25.9639 15.1346 25.5513 14.3096 24.7263L4.16663 14.5833M45.8333 35.4167H31.25M45.8333 35.4167V20.8333" stroke="#00B0F0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

						</svg>

	                    </div>

	                </div>

	            </div>

	        </div>

        </div>

            <div class="col-lg-5 finacial_year">

                <div class="small-box">

                    <div class="inner">

                        <p>Total leaves for this <br> finacial year</p>

                        <h3 class="leave_info mt-15">{{ $all_leave }}</h3>

                        <span class="extra">

                        	<p><strong class="leftborder"> Taken : {{ $totalTakenLeave }}</strong>

                        	<strong> Remaining : {{$remainYearLeave}}</strong></p>

                        </span>

                    </div>

                    <div class="semi-inner">

	                    <div class="inner">

	                        <p>Previous year's leaves </p>

	                        <h3 class="leave_info">

		                        @if($countPYL)

		                         {{ $remain_prev_yl }} / {{ $all_count_PRE }}

		                         @else

		                         0 

		                        @endif

	                        </h3>

	                    </div>

	                    <div class="inner">

	                    	<p> Extra Day's </p>

	                    	<h3 class="leave_info">								

								@if($extra_day)

								{{$remain_extdays}} / {{ $extra_day }}

								@else

								0

								@endif

	                       </h3>

	                    </div>	

	                </div>

                </div>

            </div>

        </div>

    </div>

	<h3 class="mt-4 mb-4">Week</h3>

		 <div class="row info-detail info-detail-week">

	            <div class="col-lg-4 {{ $assign_project < 1 ? 'col-lg-6' : 'col-lg-4'}}">

	                <div class="small-box">

	                    <div class="inner">

	                        <h3>{{$Week_office_hours}}</h3>

	                        <p>Week total working Hours</p>

	                    </div>

	                    <div class="icon">

	                        <img src="{{asset('img/office-icon.png')}}">

	                    </div>

	                </div>

	            </div>

	            <div class="col-lg-4 {{ $assign_project < 1 ? 'col-lg-6' : 'col-lg-4'}}">

	                <div class="small-box">

	                    <div class="inner">

	                        <h3 id="rem_hours">{{$remaining_hour}}</h3>

	                        <p>Remaining working Hours</p>

	                    </div>

	                    <div class="icon">

	                        <img src="{{asset('img/office-icon.png')}}">

	                    </div>

	                </div>

	            </div>

	            @if($assign_project > 0)

		            <div class="col-lg-4">

		                <div class="small-box">

		                	<div class="inner">

		                    	<h3 class="leave_info">{{$assign_project}}</h3>

		                        <p>Project Assigned</p>

		                    </div>

		                    <div class="icon">

		                        <img src="{{asset('img/project-icon-aqua.png')}}">

		                    </div>

		                  

		                </div>

		            </div>

		        @endif    

	        </div>

	        <div class="row info-detail">

	            <div class="col-lg-6">

	               <h3 class="mt-4 mb-4"> Recent Employee On Leave </h3>

	                <!-- <div class="small-box">

	                    <h5>Today’s Recent Activity</h5>

	                </div> -->

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

							    @forelse($recent_emp_leave as $leave)

							    	<tr>

							    		<td>{{$leave->employee->first_name}}</td>

							    		<td>{{$leave->display_status}}</td>

							    		<td>{{$leave->formatted_date}}</td>

							    	</tr>

							    @empty

							    	<tr>

							    		<td colspan="3">No employee has leave for the next 15 days.</td>

							    	</tr>	

							    @endforelse

							  </tbody>

							</table>

		                 </div>

	                </div>

	            </div>

	        	<div class="col-lg-6">

	                <h3 class="mt-4 mb-4"> Email Request - Late Coming & Early Going </h3>

	            	<div class="small-box" style="height: auto;">

	                	<div class="card-body">

	                        <table class="table">

							  <thead>

							    <tr>

							      <th scope="col"><i class="nav-icon fas fa-wind"></i> Subject </th>

							      <th scope="col"><i class="nav-icon fas fa-calendar-alt" aria-hidden="true"></i>  Date </th>

							      <th scope="col"><i class="fas fa-circle"></i> Status</th>

							    </tr>

							  </thead>

							  <tbody>

							  	<tbody>

								    @forelse($recent_latecoming_earlycoming as $dataVal)

								        <tr>

								            <td>{{ $dataVal->subject }}</td>

								            <td>{{ $dataVal->display_date }}</td>

								            <td>{{ $dataVal->status }}</td>

								        </tr>

								    @empty

								        <tr>

								            <td colspan="3">Your no pending email requests for Late Coming or Early Going.</td>

								        </tr>

								    @endforelse

								</tbody>

							  </tbody>

							</table>

		                 </div>

	                </div>

	            </div>

	        </div>	        

@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script type="text/javascript">

	function remcounter_timer(hours, span_sec) {

        var time = hours,

        parts = time.split(':'),

        hours = +parts[0],

        minutes = +parts[1],

        seconds = +parts[2],

        span = $('#'+span_sec);



        function correctNum(num) {

            return (num<10)? ("0"+num):num;

        }



        var timer = setInterval(function(){

            seconds--;

            if(seconds == 0) {

            	seconds = 60;

                minutes--;



                if(minutes <= 0) {

            	   minutes = 59;

                    hours--;

                }

                if(hours <= 0){

                	hours = '00';

                }

            }

            if ((hours <= 0 && minutes <= 0 && seconds <= 0) || (hours <= '00' && minutes <= '00' && seconds <= '00')) {

                // clearInterval(timer); // Stop the timer when all values reach 0

                // console.log(timer);

                return false;

                return;

            }



            span.text(correctNum(hours) + ":" + correctNum(minutes) + ":" + correctNum(seconds));

        }, 1000);

    }

    $(document).ready(function() {

    	

    	var endday = "<?php echo $enddate?>";

    	var break_activity = "<?php echo $break_check?>";

    	if(endday == 'notend'){

    		console.log(endday);

    		if(break_activity !='not_start'){

	    		if (break_activity != 3) {

	    			remcounter_timer("<?php echo $remaining_hour?>", "rem_hours");

	    		}

    		}

    	}

    });	

</script>	