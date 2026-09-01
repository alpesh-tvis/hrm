@extends('admin.master')

@section('content')
    <link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">    
    <style>
        /* Style for the .btn.extra-day-view class */
        .btn.extra-day-view {
            display: inline-block; 
            font-size: 12px; 
            color: #fff; 
            background-color: #007bff; 
            text-decoration: none; 
            border-radius: 5px; 
            transition: background-color 0.3s ease; 
        }
        
        .btn.extra-day-view:hover {
            background-color: #0056b3; 
            text-decoration: none; 
        }

        .btn.extra-day-view p {
            margin: 0; 
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .scrollable-cell {
            max-height: 100px;
            overflow-y: auto;
            width: 500px;
        }
        
        .modal-content {
            top: 20px;
        }
        
        table#deduction_cl_tbl {
            /* height: 500px; */
            /* overflow-y: scroll; */
            display: block;
            height: 500px;
            overflow-y: scroll;
        }

        table#deduction_pl_tbl {
            display: block;
            height: 200px;
            overflow-y: scroll;
        }

        table#deduction_sl_tbl{
            display: block;
            height: 200px;
            overflow-y: scroll;
        }
    </style>

    @php
        $date = date('m');
        
        if ($date > 3) {
            $year = date('Y')."-".(date('Y') +1);
        }
        else {
            $year = (date('Y')-1)."-".date('Y');
        }

        $check_setting = \App\Models\LeaveSetting::where('financial_year', $year)->exists();
    @endphp

    <section class="content">
        <p id="removerowMsg" class="alert alert-danger" style="display:none">Leave Setting Deleted Successfully!</p>

        <div class="container-fluid">
            @if($employeerecord)
                <div class="row mb-2">
                    <div class="col-12 text-right">
                        <a href="{{ route('leave_add_setting') }}" class="btn btn-primary"><i class="fas fa-plus-square"></i></a>
                    </div>
                </div>
            @endif

            @if($check_setting)
                <div class="row mb-2">
                    <div class="col-12 text-right">
                        <a href="{{ route('leave_updates_setting') }}" class="btn btn-primary">Update Leave</a>
                    </div>
                </div>
            @endif
        </div>
   
        <div class="card-body">
            <label for="financial_year" class="form-label">Financial Year :: <span id='CurrentFY'></span></label>
            <select id="financial_year" class="form-control form-control-sm financial_year"  style="width:25%">
                @foreach($financial_years as $yearvalue)
                    <option value="{{ $yearvalue->financial_year }}">{{ $yearvalue->financial_year }}</option>
                @endforeach    
            </select>

            @if($check_setting == $check_setting)
                <table id="leave_setting_tbl" class="table table-bordered table-striped display">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th> <i class="nav-icon fas fa-suitcase"></i> PL </th>
                            <th> <i class="nav-icon fas fa-bed"></i> SL</th>
                            <th> <i class="nav-icon fas fa-holly-berry"></i> CL</th>
                            <th>Prev Year</th>
                            <th>Ex Day's</th>
                            <th>AdjLeave</th>
                            <th>Test</th>
                            <th>Deducted</th>
                            <th>Total</th>
                            <th>Financial Year</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @php
                            $count = 1;
                        @endphp
                
                        @foreach($getdataleavesetting as $employeeId => $leaveSettings)
                            @foreach($leaveSettings as $emplyoeeval)
                
                                @php
                                    $financial_year_ex = $emplyoeeval->financial_year;
                                    $financial_year_new_array = explode('-',$financial_year_ex);
                        
                                    $total_leave = $emplyoeeval->sick_leave + $emplyoeeval->paid_leave + $emplyoeeval->casual_leave + $emplyoeeval->previous_year_leave + $emplyoeeval->extra_days;
                        
                                    $start_date = $financial_year_new_array[0].'-04-01';
                                    $end_date = $financial_year_new_array[1].'-03-31';
                                    $employee_id  = $emplyoeeval->employee_id;
                                    $remain_prev_yl  = $emplyoeeval->remain_prev_yl;
                                    $remain_extdays  = $emplyoeeval->remain_extdays;
                        
                                    $count_half_SL =  \App\Models\Leave::where('user_id',$employee_id)->where('leave_type','SL')->where('leave_status','!=','F')->where('status','Approved')->whereBetween('leave_date',[$start_date,$end_date])->count(); 

                                    if($count_half_SL == 0){
                                        $formatted_half_SL = 0;
                                    }
                                    else{
                                        $total_half_SL = $count_half_SL * 0.5;
                                        $formatted_half_SL = rtrim(rtrim($total_half_SL, '0'), '.');
                                    }

                                    $count_full_SL =  \App\Models\Leave::where('user_id',$employee_id)->where('leave_type','SL')->where('leave_status','F')->where('status','Approved')->whereBetween('leave_date',[$start_date,$end_date])->count(); 

                                    $total_Sl = $count_full_SL + $formatted_half_SL; 

                                    $count_half_PL =  \App\Models\Leave::where('user_id',$employee_id)->where('leave_type','PL')->where('leave_status','!=','F')->where('status','Approved')->whereBetween('leave_date',[$start_date,$end_date])->count(); 
                        
                                    if($count_half_PL == 0){
                                        $formatted_half_PL = 0;
                                    }
                                    else{
                                        $total_half_PL = $count_half_PL * 0.5;
                                        $formatted_half_PL = rtrim(rtrim($total_half_PL, '0'), '.');
                                    }

                                    $count_full_PL =  \App\Models\Leave::where('user_id',$employee_id)->where('leave_type','PL')->where('leave_status','F')->where('status','Approved')->whereBetween('leave_date',[$start_date,$end_date])->count(); 

                                    $total_Pl = $count_full_PL + $formatted_half_PL; 

                                    $count_half_CL =  \App\Models\Leave::where('user_id',$employee_id)->where('leave_type','CL')->where('leave_status','!=','F')->where('status','Approved')->whereBetween('leave_date',[$start_date,$end_date])->count(); 

                                    if($count_half_CL == 0){
                                        $formatted_half_CL = 0;
                                    }
                                    else{
                                        $total_half_CL = $count_half_CL * 0.5;
                                        $formatted_half_CL = $total_half_CL;
                                    } 
                    
                                    $count_full_CL =  \App\Models\Leave::where('user_id',$employee_id)->where('leave_type','CL')->where('leave_status','F')->where('status','Approved')->whereBetween('leave_date',[$start_date,$end_date])->count(); 
                    
                                    $total_Cl = $count_full_CL + $formatted_half_CL;
                    
                                    $all_total_leave = $total_Sl + $total_Pl + $total_Cl;
                    
                                    if (!function_exists('financial_year')) {
                                        function financial_year() {
                                            $date = date('m');
                                            if ($date > 3) {
                                                $year = date('Y')."-".(date('Y') + 1);
                                            } else {
                                                $year = (date('Y') - 1)."-".date('Y');
                                            }
                                            return $year;
                                        }
                                    }
                                    $count_extra_days =  \App\Models\ExtraDays::where('employee_id',$employee_id)->where('financial_year', $financial_year_ex)->sum('extra_days'); 

                                    $count_pl_days = \App\Models\LeaveSetting::where('employee_id', $employee_id)->where('financial_year', financial_year())->sum('remaining_pl_leave');
                                    
                                    $count_sl_days =  \App\Models\LeaveSetting::where('employee_id', $employee_id)->where('financial_year', financial_year())->sum('remaining_sl_leave');
                                    $count_cl_days =  \App\Models\LeaveSetting::where('employee_id', $employee_id)->where('financial_year', financial_year())->sum('remaining_cl_leave');
                    
                                    $preLeave = $emplyoeeval->previous_year_leave;

                                    if ($count_extra_days !== null && $preLeave !== null) {
                                        if ($count_extra_days > 0) {
                                            $dedPlLeave = $count_pl_days + $count_extra_days;
                                        } else {
                                            $dedPlLeave = $count_pl_days + $preLeave;
                                        }
                                    }

                                    if ($count_extra_days !== null && $preLeave !== null) {
                                        if ($count_extra_days > 0) {
                                            $dedSlLeave = $count_sl_days + $count_extra_days;
                                        } else {
                                            $dedSlLeave = $count_sl_days + $preLeave;
                                        }
                                    }
                    
                                    if ($count_extra_days !== null && $preLeave !== null) {
                                        if ($count_extra_days > 0) {
                                            $dedCLLeave = $count_cl_days + $count_extra_days;
                                        } else {
                                            $dedCLLeave = $count_cl_days + $preLeave;
                                        }
                                    }
                                @endphp
                                
                                <tr>
                                    <td>{{ $emplyoeeval->emp_name }}</td>
                                    <td data-sort="{{  $total_Pl }}">
                                        @if($total_Pl)
                                            <span class="text-danger">{{  $total_Pl }}</span>/
                                        @else

                                            <span class="text-danger">0</span> /  
                                        @endif 
                                        <span class="text-success">{{ $emplyoeeval->paid_leave }}</span>
                                    </td>
                                    <td data-sort="{{  $total_Sl }}">
                                        @if($total_Sl)
                                            <span class="text-danger">
                                                {{ $total_Sl }}
                                            </span>     
                                            /
                                        @else

                                            <span class="text-danger">0</span> /   
                                        @endif
                                        <span class="text-success"> {{ $emplyoeeval->sick_leave }} </span>
                                    </td>
                                    <td data-sort="{{  $total_Cl }}">
                                        @if($total_Cl)
                                            <span class="text-danger">
                                                {{ $total_Cl }}
                                            </span>     
                                            /
                                        @else
                                            <span class="text-danger">0</span> / 
                                        @endif
                                        <span class="text-success"> 
                                            {{ $emplyoeeval->casual_leave }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            // --- Base Employee Leave Data ---
                                            $fincPaidLeave   = $emplyoeeval->paid_leave ?? 0;
                                            $fincSickLeave   = $emplyoeeval->sick_leave ?? 0;
                                            $fincCasualLeave = $emplyoeeval->casual_leave ?? 0;
                                            $previous_year_leave = $emplyoeeval->previous_year_leave ?? 0;

                                            // --- Totals (ensure defined) ---
                                            $total_Pl = $total_Pl ?? 0;
                                            $total_Sl = $total_Sl ?? 0;
                                            $total_Cl = $total_Cl ?? 0;

                                            $preLeave = $preLeave ?? 0;
                                            $count_extra_days = $count_extra_days ?? 0;
                                            $extra_day = $extra_day ?? 0;

                                            // --- Remaining Leave after usage ---
                                            $dedLeavePl = $fincPaidLeave - $total_Pl;
                                            $dedLeaveSl = $fincSickLeave - $total_Sl;
                                            $dedLeaveCl = $fincCasualLeave - $total_Cl;

                                            // --- Leave Adjustment using Previous Leave or Extra Days ---
                                            $leaveAdjust = 0;

                                            if ($preLeave > 0) {
                                                if ($dedLeavePl < 0) {
                                                    $leaveAdjust = $preLeave - abs($dedLeavePl);
                                                } elseif ($dedLeaveSl < 0) {
                                                    $leaveAdjust = $preLeave - abs($dedLeaveSl);
                                                } elseif ($dedLeaveCl < 0) {
                                                    $leaveAdjust = $preLeave - abs($dedLeaveCl);
                                                }
                                            } elseif ($count_extra_days > 0) {
                                                if ($dedLeavePl < 0) {
                                                    $leaveAdjust = $count_extra_days - abs($dedLeavePl);
                                                } elseif ($dedLeaveSl < 0) {
                                                    $leaveAdjust = $count_extra_days - abs($dedLeaveSl);
                                                } elseif ($dedLeaveCl < 0) {
                                                    $leaveAdjust = $count_extra_days - abs($dedLeaveCl);
                                                }
                                            }

                                            // --- Final Adjustment (cannot go below zero) ---
                                            $saveLeave = max(0, $leaveAdjust);

                                            // --- Extra Leave Taken (beyond entitlement) ---
                                            $pl_leave = max(0, $total_Pl - $fincPaidLeave);
                                            $sl_leave = max(0, $total_Sl - $fincSickLeave);
                                            $cl_leave = max(0, $total_Cl - $fincCasualLeave);

                                            // --- Previous Year Leave Adjustment ---
                                            $used_pre_year_leave = $pl_leave + $sl_leave + $cl_leave;

                                            if ($used_pre_year_leave >= $previous_year_leave) {
                                                $pre_year_leave = $previous_year_leave;
                                                $remain_pre_leave = $used_pre_year_leave - $previous_year_leave;
                                            } else {
                                                $pre_year_leave = $used_pre_year_leave;
                                                $remain_pre_leave = 0;
                                            }

                                            // --- Extra Day Leave Calculation ---
                                            $extra_day_leave = ($remain_pre_leave > 0) ? $extra_day : 0;
                                        @endphp

                                        {{-- Display output --}}
                                        <p>{{ $pre_year_leave }} / {{ $previous_year_leave }}</p>
                                    </td>

                                    
                                        @php
                                            $extra_day = $count_extra_days;
                                            $extra_day = (float) $extra_day;
                                            $extra_day = number_format($extra_day, (strpos($extra_day, '.') !== false) ? 1 : 0);
                                        @endphp
                                    
                                    <td>
                                    
                                        @if($extra_day)
                                            {{$emplyoeeval->remain_extdays}} /
                                            <a href="https://hrm.tvistech.com/leave_setting/{{$emplyoeeval->employee_id}}" class="" data-toggle="modal" data-target="#details-modal-{{$emplyoeeval->employee_id}}">
                                                {{ $extra_day }} 
                                            </a>
                                            
                                            <div id="details-modal-{{$emplyoeeval->employee_id}}" class="modal fade modal-top-right" tabindex="-1" role="dialog" aria-labelledby="details-modal-{{$emplyoeeval->employee_id}}" aria-hidden="true">
                                                <div class="modal-dialog modal-top-right" style="max-width: 1440px;">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <i class="fas fa-user prefix grey-text"></i>
                                                                    <label for="emp_name" class="form-label">Employee : {{ $emplyoeeval->emp_name }}</label>
                                                                </div>
                                                                
                                                                @php
                                                                    $empExtra_days =  \App\Models\ExtraDays::where('employee_id',$emplyoeeval->employee_id)->orderBy('id', 'desc')->get(); 
                                                                @endphp
                                                                
                                                                <table id="extra_days_tbl" class="table table-bordered table-striped display">
                                                                
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Employee</th>
                                                                            <th>Date</th>
                                                                            <th>Reason of Work Description</th>
                                                                            <th>Extra Days</th>
                                                                            <th>Action</th>
                                                                        </tr>
                                                                    </thead>

                                                                    <tbody>
                                                                        @foreach($empExtra_days as $empExDaVal)
                                                                            @php
                                                                                $my_date = $empExDaVal->date;
                                                                                $formatted_date = date("F j, Y", strtotime($my_date));
                                                                                $day = date("l", strtotime($my_date));

                                                                                $reasonDesc = $empExDaVal->reason_of_work_description;
                                                                                $truncReasonDesc = Str::limit($reasonDesc, 40, '...');

                                                                                $extra_day = $empExDaVal->extra_days;
                                                                                $extra_day = (float) $extra_day;
                                                                                $extra_day = number_format($extra_day, (strpos($extra_day, '.') !== false) ? 1 : 0);

                                                                            @endphp
                                                                            <tr>
                                                                                <td>{{ $empExDaVal->employee_name }}</td>
                                                                                <td>{{ $day }}, {{ $formatted_date }}</td>
                                                                                <td> <div class="scrollable-cell"> {{ $reasonDesc }} </div> </td>
                                                                                <td>{{ $extra_day }}</td>
                                                                                <td> <a href="{{ route('extra_days_edit',$empExDaVal->id) }}" class="btn btn-info project-view"> <i class="fas fa-edit"></i></a></td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>

                                                                    <tfoot>
                                                                        <tr>
                                                                            <th>Employee</th>
                                                                            <th>Date</th>
                                                                            <th>Reason of Work Description</th>
                                                                            <th>Extra Days</th>
                                                                            <th>Action</th>
                                                                        </tr>
                                                                    </tfoot>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <p>0</p>
                                        @endif
                                    </td>

                                    <td class="adjLeave">
                                    
                                        @php
                                            $leaveAdjustments = \App\Models\LeaveAdjust::where('user_id', $emplyoeeval->employee_id)
                                            ->where('financial_year', financial_year()) 
                                            ->get();
                                            
                                            $previousYearLeaveSum = $leaveAdjustments->where('leave_deduct', 'Previous Year Leave')
                                            ->sum(function 
                                                ($leaveAdjust) {
                                                    return ($leaveAdjust->leave_adjust === 'Full Day' ? 1 : 0) + 
                                                        (in_array($leaveAdjust->leave_adjust, ['Half Day', 'Second Half', 'First Half']) ? 0.5 : 0);
                                                }
                                            );
                                
                                            $extraDayLeaveSum = $leaveAdjustments->where('leave_deduct', 'Extra Day')->sum(function ($leaveAdjust) {
                                                return ($leaveAdjust->leave_adjust === 'Full Day' ? 1 : 0) + 
                                                    (in_array($leaveAdjust->leave_adjust, ['Half Day', 'Second Half', 'First Half']) ? 0.5 : 0);
                                            });

                                            $totalLeave = $leaveAdjustments->sum(function ($leaveAdjust) {
                                                return ($leaveAdjust->leave_adjust === 'Full Day' ? 1 : 0) + 
                                                    (in_array($leaveAdjust->leave_adjust, ['Half Day', 'Second Half', 'First Half']) ? 0.5 : 0);
                                            });
                                        @endphp
                                    
                                        @if($previousYearLeaveSum)
                                            @php
                                                $adjustYPLDe =  \App\Models\LeaveAdjust::where('user_id', $employee_id)->where('leave_deduct', 'Previous Year Leave')->get();
                                                
                                                foreach($adjustYPLDe as $adjVal){
                                                    $empid = $adjVal->user_id;
                                                    $employeeNameData = \App\Models\Employee::where('id', $empid)->first();
                                                    $first_name = $employeeNameData ? $employeeNameData->first_name : 'N/A';
                                                }
                                            @endphp

                                            <a href="{{ url('/leave_setting/pyl/' . $employee_id) }}" class="btn btn-info over-day-view" data-toggle="modal" data-target="#details-modal-pyl{{$employee_id}}" style="font-size: 12px;background: #008000; font-weight: bold; color: white;border: #008000;" target="_blank">
                                                <strong style="color: white;"> PYL :: {{ $previousYearLeaveSum }}</strong><br />
                                            </a>
                                        @else
                                            <p> PYL :: 0</p>
                                        @endif
                                    
                                        <!-- Adjust PYL Modal Structure -->
                                        <div id="details-modal-pyl{{$employee_id}}" class="modal fade modal-top-right" tabindex="-1" role="dialog" aria-labelledby="details-modal-pyl{{$employee_id}}" aria-hidden="true">
                                            <div class="modal-dialog modal-top-right" style="max-width: 1440px;">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Previous Year Leave</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <i class="fas fa-user prefix grey-text"></i>
                                                            <label for="emp_name" class="form-label">
                                                                @if($previousYearLeaveSum) 
                                                                    @if($first_name)
                                                                        {{ $first_name }}
                                                                    @endif
                                                                @endif    
                                                            </label>
                                                        </div>
                                                        
                                                        <table id="deduction_pyl_tbl" class="table table-bordered table-striped display">
                                                            <thead>
                                                                <tr>
                                                                    <th>LvType</th>
                                                                    <th>LvDedcution</th>
                                                                    <th>LvAdjust</th>
                                                                    <th>LvDate</th>
                                                                    <th>Month</th>
                                                                    <th>FY</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            
                                                            <tbody>
                                                                @if($previousYearLeaveSum) 
                                                                    @foreach($adjustYPLDe as $adjValue)
                                                                        <tr>
                                                                            <td>{{$adjValue->leave_type}}</td>
                                                                            <td>{{$adjValue->leave_deduct}}</td>
                                                                            <td>{{$adjValue->leave_adjust}}</td>
                                                                            <td>{{$adjValue->leave_date}}</td>
                                                                            <td>{{$adjValue->leave_month}}</td>
                                                                            <td>{{$adjValue->financial_year}}</td>
                                                                            <td>
                                                                                <a href="" class="btn btn-info project-view">
                                                                                    <i class="fas fa-edit"></i>
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endif  
                                                            </tbody>
                                                            
                                                            <tfoot>
                                                                <tr>
                                                                    <th>Lv Type</th>
                                                                    <th>LvDedcution</th>
                                                                    <th>LvAdjust</th>
                                                                    <th>LvDate</th>
                                                                    <th>Month</th>
                                                                    <th>FY</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!--Adjust EXTLeave Display & Modal Structure-->
                                        @php
                                            $adjustExtYLDe = \App\Models\LeaveAdjust::where('user_id', $employee_id)->where('leave_deduct', 'Extra Day')->get();
                                            $firstExtname = 'N/A'; 
                                            
                                            foreach ($adjustExtYLDe as $adjExVal) {
                                                $empExid = $adjExVal->user_id;
                                                $employeeExtNameData = \App\Models\Employee::where('id', $empExid)->first();
                                                
                                                if ($employeeExtNameData) {
                                                    $firstExtname = $employeeExtNameData->first_name;
                                                    break; 
                                                }
                                            }
                                        @endphp

                                        @if($extraDayLeaveSum)
                                            <a href="{{ url('/leave_setting/extl/' . $employee_id) }}" class="btn btn-info over-day-view" data-toggle="modal" data-target="#details-modal-ext{{$employee_id}}" style="font-size: 12px; background: #f39f04; color: #d9534f; border: 1px solid #f39f04; font-weight: bold; text-align: center;" target="_blank">
                                                <strong style="color: white;"> ExtL :: {{ $extraDayLeaveSum }}</strong>
                                            </a>
                                        @else
                                            <p> ExtL :: 0</p>
                                        @endif

                                        <div id="details-modal-ext{{$employee_id}}" class="modal fade modal-top-right" tabindex="-1" role="dialog" aria-labelledby="details-modal-ext{{$employee_id}}" aria-hidden="true">
                                            <div class="modal-dialog modal-top-right" style="max-width: 1440px;">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Extra Leave</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <i class="fas fa-user prefix grey-text"></i>
                                                            <label class="form-label">{{$firstExtname}}</label>
                                                        </div>
                                       
                                                        <table id="deduction_ext_tbl" class="table table-bordered table-striped display">
                                                            <thead>
                                                                <tr>
                                                                    <th>LvType</th>
                                                                    <th>LvDedcution</th>
                                                                    <th>LvAdjust</th>
                                                                    <th>LvDate</th>
                                                                    <th>Month</th>
                                                                    <th>FY</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            
                                                            <tbody>
                                                                @foreach($adjustExtYLDe as $adjExtValue)
                                                                    <tr>
                                                                        <td>{{$adjExtValue->leave_type}}</td>
                                                                        <td>{{$adjExtValue->leave_deduct}}</td>
                                                                        <td>{{$adjExtValue->leave_adjust}}</td>
                                                                        <td>{{$adjExtValue->leave_date}}</td>
                                                                        <td>{{$adjExtValue->leave_month}}</td>
                                                                        <td>{{$adjExtValue->financial_year}}</td>
                                                                        <td>
                                                                            <a href="" class="btn btn-info project-view">
                                                                                <i class="fas fa-edit"></i>
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                            
                                                            <tfoot>
                                                                <tr>
                                                                    <th>Lv Type</th>
                                                                    <th>LvDedcution</th>
                                                                    <th>LvAdjust</th>
                                                                    <th>LvDate</th>
                                                                    <th>Month</th>
                                                                    <th>FY</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if($extraDayLeaveSum || $previousYearLeaveSum)
                                            <p>Total :: {{ $totalLeave  }}</p>
                                        @endif
                                    </td>
                                    <td>test</td>
                                    <td class="deduction">
                                        @php
                                            $getSalaryDeductedData = [];
                                            $leaveTypeCounts = [];
                                            $leaveTypeRecordCount = [];
                                            $halfDayCounts = []; 
                                            $unmatchedCount = 0; 
                                            $notAdjustedCount = 0;  
                                            $empID = $employee_id;

                                            $salaryData = \App\Models\LeaveSalDeduction::where('employee_id', $empID)
                                            ->where('financial_year', financial_year())
                                            ->get();
 
                                            $halfDayLeaveDates = []; 

                                            foreach ($salaryData as $data) {
                                
                                                $isAdjusted = DB::table('leave_adjust')
                                                ->where('user_id', $data->employee_id)
                                                ->where('leave_type', $data->leave_type)
                                                ->where(function ($query) use ($data) {
                                                    $query->where('leave_date', $data->date)
                                                    ->orWhere('leave_deduct', $data->salary_deduction); 
                                                })
                                                ->first();
                                                
                                                $leaveDeduct = $isAdjusted ? $isAdjusted->leave_deduct : null;

                                                if ($isAdjusted) {
                                                    $halfDayLeaveDates[] = $data->date;
                                                    if (!isset($halfDayCounts[$data->leave_type])) {
                                                        $halfDayCounts[$data->leave_type] = 0;
                                                    }
                                                    $halfDayCounts[$data->leave_type] += 0.5;
                                                }
                               
                                                if (!isset($leaveTypeCounts[$data->leave_type])) {
                                                    $leaveTypeCounts[$data->leave_type] = 0;
                                                    $leaveTypeRecordCount[$data->leave_type] = 0;
                                                }

                                                if (!$isAdjusted) {
                                                    $unmatchedCount++;
                                                    $adjustedDeductionValue = match ($data->salary_deduction) {
                                                        'Half Day' => 0.5,
                                                        'First Half' => 0.5,
                                                        'Second Half' => 0.5,
                                                        'Full Day' => 1,
                                                        default => 0,
                                                    };
                                                    if ($adjustedDeductionValue ) {
                                                        $leaveTypeCounts[$data->leave_type] += $adjustedDeductionValue;
                                                        $leaveTypeRecordCount[$data->leave_type]++;
                                                    }

                                                }

                                                $getSalaryDeductedData[] = [
                                                    'employee_id' => $data->employee_id,
                                                    'employee_name' => $data->employee_name,
                                                    'leave_type' => $data->leave_type,
                                                    'salary_deduction' => $data->salary_deduction,
                                                    'leave_adjust' => $data->leave_adjust,
                                                    'reason' => $data->reason,
                                                    'date' => $data->date,
                                                    'month' => $data->month,
                                                    'financial_year' => $data->financial_year,
                                                    'is_adjusted' => $isAdjusted ? 'Yes' : 'No',
                                                    'leave_deduct' => $leaveDeduct,
                                                ];
                                            }
                                        @endphp
                                        
                                        @foreach ($leaveTypeCounts as $leaveType => $count)
                                            @php
                                                $btnClass = match ($leaveType) {
                                                    'CL' => 'btn-success',
                                                    'SL' => 'btn-warning',
                                                    'PL' => 'btn-danger',
                                                    default => 'btn-secondary',
                                                };
                                            @endphp
                                            
                                            @if ($count > 0)
                                                <a href="#" class="btn {{ $btnClass }} over-day-view" data-toggle="modal" data-target="#details-modal-deduction-{{ $empID }}-{{ $leaveType }}" style="font-size: 12px;">
                                                    <strong style="color: white;">
                                                        {{ $leaveType }}: {{ $count }} 
                                                    </strong>
                                                </a>
                                            @else
                                                @if($empID == '2')
                                                    <a href="#" 
                                                       class="btn {{ $btnClass }} over-day-view" 
                                                       data-toggle="modal" 
                                                       data-target="#details-modal-deduction-{{ $empID }}-{{ $leaveType }}" 
                                                       style="font-size: 12px;">
                                                        <strong style="color: white;">
                                                            {{ $leaveType }}: 
                                                            @foreach($getSalaryDeductedData as $salDataVal)
                                                                @if(in_array($salDataVal['salary_deduction'], ['Half Day', 'Second Half', 'First Half']))
                                                                    {{ 0.5 }}
                                                                @endif
                                                            @endforeach

                                                        </strong>
                                                    </a>
                                                @endif
                                            @endif
                                        @endforeach

                        <!-- Modal Details for each Leave Type -->
                        @foreach ($leaveTypeCounts as $leaveType => $count)
                            <div id="details-modal-deduction-{{ $empID }}-{{ $leaveType }}" 
                                 class="modal fade modal-top-right" 
                                 tabindex="-1" 
                                 role="dialog" 
                                 aria-labelledby="details-modal-deduction-{{ $empID }}-{{ $leaveType }}" 
                                 aria-hidden="true">
                                <div class="modal-dialog modal-top-right" style="max-width: 1440px;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                {{ $leaveType }} Deduction ({{ $count }} days)
                                                @if (isset($halfDayCounts[$leaveType]) && $halfDayCounts[$leaveType] > 0)
                                                    - {{ $halfDayCounts[$leaveType] }} Half Day(s)
                                                @endif
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label class="form-label">
                                                    <i class="fas fa-user prefix grey-text"></i> 
                                                    {{ $getSalaryDeductedData[0]['employee_name'] ?? 'Employee' }}
                                                </label>
                                            </div>
                                            <table class="table table-bordered table-striped display">
                                                <thead>
                                                    <tr>
                                                        <th>Lv Type</th>
                                                        <th>Lv Deduction</th>
                                                        <th>Lv Date</th>
                                                        <th>Month</th>
                                                        <th>FY</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($getSalaryDeductedData as $deVal)
                                                        @if ($deVal['leave_type'] == $leaveType && $deVal['is_adjusted'] !== 'Yes')
                                                            <tr @if($deVal['salary_deduction'] === 'Half Day') style="background-color: #f8d7da;" @endif>
                                                                <td>{{ $deVal['leave_type'] }}</td>
                                                                <td>
                                                                    {{ $deVal['salary_deduction'] }}
                                                                    @if($deVal['salary_deduction'] === 'Half Day')
                                                                        <span class="badge badge-info">Half Day</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $deVal['date'] }}</td>
                                                                <td>{{ $deVal['month'] }}</td>
                                                                <td>{{ $deVal['financial_year'] }}</td>
                                                            </tr>
                                                        @elseif($empID == '2')
                                                        <tr @if($deVal['salary_deduction'] === 'Half Day') style="background-color: #0000;" @endif>
                                                            <td>{{ $deVal['leave_type'] }}</td>
                                                            <td>
                                                                {{ $deVal['salary_deduction'] }}
                                                                @if($deVal['leave_adjust'] === 'Half Day')
                                                                    <span class="badge badge-info">Half Day</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $deVal['date'] }}</td>
                                                            <td>{{ $deVal['month'] }}</td>
                                                            <td>{{ $deVal['financial_year'] }}</td>
                                                        </tr>    
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Lv Type</th>
                                                        <th>Lv Deduction</th>
                                                        <th>Lv Date</th>
                                                        <th>Month</th>
                                                        <th>FY</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach


                    </td>
                    <td data-sort="{{  $all_total_leave }}">
                        @if($all_total_leave)
                            <span class="text-danger">
                                {{ $all_total_leave }}
                            </span>    
                            /
                        @endif 
                        <span class="text-success">{{ $total_leave }}</span>
                    </td>
                    <td>{{ $emplyoeeval->financial_year }}</td>
                    <td class="dFlex">
                        <a href="{{ route('leave_show_setting', [$emplyoeeval->id]) }}" class="btn btn-info project-view" data-toggle="modal" data-target="#details-modal-{{ $emplyoeeval->id }}">
                            <i class="fas fa-eye"></i>
                        </a>

                        <div id="details-modal-{{ $emplyoeeval->id }}" class="modal fade modal-top-right" tabindex="-1" role="dialog" aria-labelledby="details-modal-{{ $emplyoeeval->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-top-right">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <div class="modal-body">
                                        <div class="form-group">
                                        <i class="fas fa-user prefix grey-text"></i>
                                        <label for="emp_name" class="form-label">Employee : {{ $emplyoeeval->emp_name }}</label>
                                        </div>
                                        <div class="form-group">
                                            <label for="form2">Sick Leave : {{$emplyoeeval->sick_leave}}
                                            </label>
                                        </div>

                                        <div class="form-group">
                                            <label for="form2">Paid Leave : {{$emplyoeeval->paid_leave}}
                                            </label>
                                        </div>

                                        <div class="form-group">
                                            <label for="form2">Casual Leave : {{$emplyoeeval->casual_leave}}
                                            </label>
                                        </div>

                                        <div class="form-group">
                                            <label for="form2">Previous Year Leave : {{$emplyoeeval->previous_year_leave}}
                                            </label>
                                        </div>
                                        
                                       
                                        <div class="form-group">
                                            @php
                                            $extra_day = $count_extra_days;
                                            $extra_day = (float) $extra_day;
                                            $extra_day = number_format($extra_day, (strpos($extra_day, '.') !== false) ? 1 : 0);
                                            @endphp
                                            
                                            <label for="form2">Extra Day's : {{ $extra_day }}
                                            </label>
                                        </div>

                                        @if($leaveAdjust != 0)
                                        <div class="form-group">
                                            <p style="color: {{ $leaveAdjust < 0 ? '' : '' }};">
                                                Deducted :
                                               @foreach ($leaveTypeCounts as $leaveType => $count)
                                                    @php
                                                        $btnClass = 'btn-info'; 

                                                        if ($leaveType == 'CL') {
                                                            $btnClass = 'btn-success'; 
                                                        } elseif ($leaveType == 'SL') {
                                                            $btnClass = 'btn-warning'; 
                                                        } elseif ($leaveType == 'PL') {
                                                            $btnClass = 'btn-danger'; 
                                                        }
                                                    @endphp

                                                    <a href="{{ url('/leave_setting/' . $empID) }}" 
                                                       class="btn {{ $btnClass }} over-day-view" 
                                                       data-toggle="modal" 
                                                       data-target="#details-modal-deduction{{ $empID }}" 
                                                       style="font-size: 12px;" 
                                                       target="_blank">
                                                        <strong style="color: white;">{{ $leaveType }}: {{ $count }}</strong>
                                                    </a>
                                                    
                                                @endforeach
                                            </p>
                                        </div>
                                        @endif

                                        <div class="form-group">
                                            <label for="form2">Financial Year : {{$emplyoeeval->financial_year}}
                                            </label>   
                                        </div>

                                        <div class="form-group">
                                            <label for="form2">Total Leave : {{ $emplyoeeval->sick_leave + $emplyoeeval->paid_leave + $emplyoeeval->casual_leave + $emplyoeeval->previous_year_leave + $emplyoeeval->extra_days }}
                                            </label>   
                                        </div>
                                        </div>
                                    </div>
                                    <div class="modal-body">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('leave_edit_setting', [$emplyoeeval->id]) }}" class="btn btn-info project-view"> 
                         <i class="fas fa-edit"></i> 
                        </a>
                       
                        <a href="javascript:void(0)" data-url="{{ route('leave_setting.delete', $emplyoeeval->id) }}" data-id="{{ $emplyoeeval->id }}" data-name="{{ $emplyoeeval->emp_name }}" 
                            class="btn btn-danger delete-leave"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                @endforeach
                @endforeach 
            </tbody>
            <tfoot>
                <tr>
                    <th>Employee</th>
                    <th>Paid</th>
                    <th>Sick</th>
                    <th>Casual</th>
                    <th>Prev Year</th>
                    <th>Ex Day's</th>
                    <th>AdjLeave</th>
                    <th>Test</th>
                    <th>Deducted</th>
                    <th>Total</th>
                    <th>Financial Year</th>
                    <th>Action</th>
                </tr>
            </tfoot>
        </table>
        @endif
    </div>
</section>

<!-- DataTables  & Plugins -->
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.js')}}"></script>

<script>
$(document).ready(function() {
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

//Remove Leave Column
$(document).on('click', '.delete-leave', function() {
        var leaveURL = $(this).data('url');
        var trObj = $(this);
        var dataId = $(this).attr('data-id');
        var dataName = $(this).attr('data-name');
        
        if (confirm("Are you sure you want to delete " + dataName + " column?") == true) {
            $.ajax({
                url: leaveURL,
                type: 'DELETE',
                success: function(data) {
                   trObj.parents("tr").remove();
                   //console.log(data);
                    $("#removerowMsg").show();
                }
            });
        }

    });

});

jQuery(function ($) {
     
    $("#leave_setting_tbl").DataTable({
        language: {
            emptyTable: "No leave available in table",  
            loadingRecords: "Please wait .. ", 
            zeroRecords: "No matching records found"
        }, "paging": false, "responsive": true,"lengthChange": false, "autoWidth": false, "searchable": true, "pageLength": 10,
        
    });

    //Filter financial Year
    var Yeartables = $('#leave_setting_tbl').DataTable();
    $("#leave_setting_tbl.dataTables_filter").append($("#financial_year"));
    var YearIndex = 0;

    $("#leave_setting_tbl th").each(function (i) {
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

    var dataTable = $('#leave_setting_tbl').DataTable();
    dataTable.column(7).visible(false);
    
});
 
</script>
@endsection
