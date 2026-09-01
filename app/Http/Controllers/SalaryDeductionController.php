<?php
namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Employee;
use App\Models\User;
use App\Models\LeaveSetting;
use Illuminate\Http\Request;
use App\Models\SalaryDeduction; 
use App\Models\LeaveSalDeduction; 
use App\Models\LeaveAdjust; 
use Auth;
use Carbon\Carbon;
use DB;

class SalaryDeductionController extends Controller
{   
    public function financial_year(){
        $date = date('m');
        if ($date > 3) {
        $year = date('Y')."-".(date('Y') +1);
        }
        else {
        $year = (date('Y')-1)."-".date('Y');
        }
        return $year;
    }

    public function financialYearsFromModel(string $modelClass, string $dateColumn = 'created_at')
    {
        // Get min & max dates from the model
        $minDate = $modelClass::min($dateColumn);
        $maxDate = $modelClass::max($dateColumn);

        if (!$minDate || !$maxDate) {
            return [];
        }

        $start = Carbon::parse($minDate);
        $end   = Carbon::parse($maxDate);

        $startFY = $start->month >= 4 ? $start->year : $start->year - 1;
        $endFY   = $end->month >= 4 ? $end->year : $end->year - 1;

        $years = [];

        for ($year = $startFY; $year <= $endFY; $year++) {
            $years[] = $year . '-' . substr($year + 1, 0);
        }

        // dd($years);
        return $years;
    }

    public function index(Request $request){

        // Redirects to login if the user is not authenticated.
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Current User Role  
        $current_user_roll = auth()->user()->role;


        $employeerec = [];
        $employeLeave = [];
        
        if ($current_user_roll == '2') {
            $employees = DB::table('employees')
                ->whereNull('service_enddate')
                ->whereNotIn('id', [1, 15])
                ->get();

            // Active Employees    
            $employeerec = $employees;

            // Approved Leave    
            $employeLeave = DB::table('leaves')
            ->select('leave_type')
            ->where('status', 'Approved')
            ->whereNotIn('id', [1, 15]) 
            ->get();

        } else {
            $leave_user_id = auth()->user()->id;
            $employees = DB::table('employees')
                ->where('id', $leave_user_id)
                ->get();
        }

        // dd($employeLeave);
        $financialYears = $this->financialYearsFromModel(
            Leave::class,
            'leave_date'
        );

        // dd($financialYears);
        $financialY = $this->financial_year();
        
        // dd($financialY);
        $allLeaveData = [];
        foreach ($employees as $employee) {

            $leave_user_id = $employee->id;
            
            $leaveSettings = LeaveSetting::where('employee_id', $leave_user_id)
                ->where('financial_year', $financialY)
                ->first();
            
            if (!$leaveSettings) {
                continue;
            }

            $allLeaveData[$leave_user_id] = [
                'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                'leaves' =>
                [
                    'CL' => [],
                    'PL' => [],
                    'SL' => [],
                ],
            ];

            //CL Leave
            $total_per_year_casualLeaves = $leaveSettings->casual_leave;

            $count_half_CL = Leave::where('user_id', $leave_user_id)
                ->where('leave_type', 'CL')
                ->where('leave_status', '!=', 'F')
                ->where('status', 'Approved')
                ->count();

            $total_half_CL = $count_half_CL * 0.5;
            $formatted_half_CL = $count_half_CL == 0 ? 0 : rtrim(rtrim($total_half_CL, '0'), '.');
            
            $count_full_CL = Leave::where('user_id', $leave_user_id)
                ->where('leave_type', 'CL')
                ->where('leave_status', 'F')
                ->where('status', 'Approved')
                ->count();

            $taken_cl_leave = $count_full_CL + $formatted_half_CL;


            if ($taken_cl_leave > $total_per_year_casualLeaves) {

                $excess_leaves = $taken_cl_leave - $total_per_year_casualLeaves;
                


                $allCLLeaves = Leave::where('user_id', $leave_user_id)
                    ->where('leave_type', 'CL')
                    ->where('status', 'Approved')
                    ->orderBy('leave_date', 'desc')
                    ->get();

                $accumulated_leaves = 0;
                $lastFewCLLeaves = [];

                foreach ($allCLLeaves as $leave) {
                    
                    $leaveValue = ($leave->leave_status === 'F') ? 1 : 0.5;
                    $remainingExcess = $excess_leaves - $accumulated_leaves;

                    if ($remainingExcess >= $leaveValue) {
                        $accumulated_leaves += $leaveValue;
                        $lastFewCLLeaves[] = $leave;
                    }
                    else
                    {
                        $accumulated_leaves += $remainingExcess;
                        $partialLeave = clone $leave;
                        $partialLeave->deducted_value = $remainingExcess;
                        
                        if ($leave->leave_status === 'F' && $remainingExcess < 1) {
                            $partialLeave->leave_status = 'FH'; 
                        }

                        $lastFewCLLeaves[] = $partialLeave;
                        break; 
                    }
                    
                    if ($accumulated_leaves >= $excess_leaves) {
                        break;
                    }
                }
                
                $allLeaveData[$leave_user_id]['leaves']['CL'] = $lastFewCLLeaves;
            }
            else
            {
                $allLeaveData[$leave_user_id]['leaves']['CL'] = [];
            }
            //End CL Leave
            
            //PL Leave
            $total_per_year_paidLeave = $leaveSettings->paid_leave;
            
            $count_half_PL = Leave::where('user_id', $leave_user_id)
                ->where('leave_type', 'PL')
                ->where('leave_status', '!=', 'F')
                ->where('status', 'Approved')
                ->count();

            $total_half_PL = $count_half_PL * 0.5;
            $formatted_half_PL = $total_half_PL == 0 ? 0 : rtrim(rtrim($total_half_PL, '0'), '.');

            $count_full_PL = Leave::where('user_id', $leave_user_id)
                ->where('leave_type', 'PL')
                ->where('leave_status', 'F')
                ->where('status', 'Approved')
                ->count();

            $taken_pl_leave = $count_full_PL + $formatted_half_PL;

            if ($taken_pl_leave > $total_per_year_paidLeave) {
                
                $excess_leavesPL = $taken_pl_leave - $total_per_year_paidLeave;

                $allPLLeaves = Leave::where('user_id', $leave_user_id)
                    ->where('leave_type', 'PL')
                    ->where('status', 'Approved')
                    ->orderBy('leave_date', 'desc')
                    ->get();

                $accumulated_leavesPL = 0;
                $lastFewPLLeaves = [];

                foreach ($allPLLeaves as $leave) {
                    
                    $leaveValuePL = ($leave->leave_status === 'F') ? 1 : 0.5;
                    $remainingExcessPL = $excess_leavesPL - $accumulated_leavesPL;

                    if ($remainingExcessPL >= $leaveValuePL) {
                        $accumulated_leavesPL += $leaveValuePL;
                        $lastFewPLLeaves[] = $leave;
                    }
                    else
                    {
                        $accumulated_leavesPL += $remainingExcessPL;
                        $partialLeavePL = clone $leave;
                        $partialLeavePL->deducted_value = $remainingExcessPL;
                        
                        if ($leave->leave_status === 'F' && $remainingExcessPL < 1) {
                            $partialLeavePL->leave_status = 'FH'; 
                        }
                        
                        $lastFewPLLeaves[] = $partialLeavePL;
                        break; 
                    }
                    
                    if ($accumulated_leavesPL >= $excess_leavesPL) {
                        break;
                    }
                }

                $allLeaveData[$leave_user_id]['leaves']['PL'] = $lastFewPLLeaves;
            }
            else
            {
                $allLeaveData[$leave_user_id]['leaves']['PL'] = [];
            }
            //End PL Leave

            //SL Leave
            $total_per_year_sickLeave = $leaveSettings->sick_leave;
            
            $count_half_SL = Leave::where('user_id', $leave_user_id)
                ->where('leave_type', 'SL')
                ->where('leave_status', '!=', 'F')
                ->where('status', 'Approved')
                ->count();

            $total_half_SL = $count_half_SL * 0.5;
            $formatted_half_SL = $total_half_SL == 0 ? 0 : rtrim(rtrim($total_half_SL, '0'), '.');

            $count_full_SL = Leave::where('user_id', $leave_user_id)
                ->where('leave_type', 'SL')
                ->where('leave_status', 'F')
                ->where('status', 'Approved')
                ->count();

            $taken_sl_leave = $count_full_SL + $formatted_half_SL;

            if ($taken_sl_leave > $total_per_year_sickLeave) {
                
                $excess_leavesSL = $taken_sl_leave - $total_per_year_sickLeave;

                $allSLLeaves = Leave::where('user_id', $leave_user_id)
                    ->where('leave_type', 'SL')
                    ->where('status', 'Approved')
                    ->orderBy('leave_date', 'desc')
                    ->get();

                $accumulated_leavesSL = 0;
                $lastFewSLLeaves = [];
                
                foreach ($allSLLeaves as $leave) {

                    $leaveValueSL = ($leave->leave_status === 'F') ? 1 : 0.5;
                    $remainingExcessSL = $excess_leavesSL - $accumulated_leavesSL;

                    if ($remainingExcessSL >= $leaveValueSL) {
                        $accumulated_leavesSL += $leaveValueSL;
                        $lastFewSLLeaves[] = $leave;
                    }
                    else
                    {
                        $accumulated_leavesSL += $remainingExcessSL;
                        $partialLeaveSL = clone $leave;
                        $partialLeaveSL->deducted_value = $remainingExcessSL;
                        
                        if ($leave->leave_status === 'F' && $remainingExcessSL < 1) {
                            $partialLeaveSL->leave_status = 'FH'; 
                        }
                        $lastFewSLLeaves[] = $partialLeaveSL;
                        break; 
                    }

                    if ($accumulated_leavesSL >= $excess_leavesSL) {
                        break;
                    }
                }
                
                $allLeaveData[$leave_user_id]['leaves']['SL'] = $lastFewSLLeaves;
               
            } else {
                $allLeaveData[$leave_user_id]['leaves']['SL'] = [];
            }
            //End SL Leave
        }
       
        return view('salary-deduction.index', compact('allLeaveData', 'employeerec', 'employeLeave', 'financialY', 'current_user_roll','financialYears'));
    }

    public function updateLeaveDeduction(Request $request) {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $leave_id = $request->leave_id;
        $leave_date = $request->leave_date;
        $leave_reason = $request->leave_reason;

        $leaveDate = $leave_date;
        $createdAt = strtotime($leaveDate);
        $leaveYear = date('Y', $createdAt);
        $leaveMonth = date('F', $createdAt);
        
        $userEmpID = $request->user_id;
        $checkPaidUnpaidleavededuct = $request->leave_deduct;
        $checkLeaveType = $request->leave_type;
        $checkLeaveStatus = $request->leave_status;
        if ($checkLeaveStatus == 'First Half' || $checkLeaveStatus == 'Second Half') {
            $leaveSta = '0.5';
        }elseif($checkLeaveStatus == 'Full Day'){
            $leaveSta = '1';
        }else{
            $leaveSta = '0';
        }
        
        $fincyear = $this->financial_year();

        //LeaveSetting Data Get
        $leaveSettData = DB::table('leave_setting')
            ->where('employee_id', $userEmpID)
            ->where('financial_year', $fincyear)->first();
        
        $id = $leaveSettData->id ?? null;
        $paid_leave = $leaveSettData->paid_leave ?? null;
        $taken_pl_leave = $leaveSettData->taken_pl_leave ?? null;
        $remaining_pl_leave = $leaveSettData->remaining_pl_leave ?? null;

        $sick_leave = $leaveSettData->sick_leave ?? null;
        $taken_sl_leave = $leaveSettData->taken_sl_leave ?? null;
        $remaining_sl_leave = $leaveSettData->remaining_sl_leave ?? null;

        $casual_leave = $leaveSettData->casual_leave ?? null;
        $taken_cl_leave = $leaveSettData->taken_cl_leave ?? null;
        $remaining_cl_leave = $leaveSettData->remaining_cl_leave ?? null;

        $previous_year_leave = $leaveSettData->previous_year_leave ?? null;
        $remain_prev_yl = $leaveSettData->remain_prev_yl ?? null;
        $extra_days = $leaveSettData->extra_days ?? null;
        $remain_extdays = $leaveSettData->remain_extdays ?? null;
        
        //PL LEAVE CALCULATION

        if ($checkLeaveType == 'PL') {
            if ($checkPaidUnpaidleavededuct == '1') {
                $paidleaveTaken = $taken_pl_leave + $leaveSta;
                LeaveSetting::where('employee_id', $userEmpID)->where('financial_year', $fincyear)
                    ->update([
                        'taken_pl_leave' => $paidleaveTaken,
                    ]);
            }

            if ($taken_pl_leave > $paid_leave) {
                if ($remaining_pl_leave > 0) {
                    $remainingPlLeave = $remaining_pl_leave - $leaveSta;
                } else {
                    $remainingPlLeave = $remaining_pl_leave - $leaveSta;
                }
            } else {
                if ($remaining_pl_leave < 0) {
                    if (fmod($remaining_pl_leave, 2) == 0) { 
                        $remainingPlLeave = $remaining_pl_leave - 1; 
                    } else {
                        $remainingPlLeave = $remaining_pl_leave + 1; 
                    }
                } else {
                    $remainingPlLeave = $remaining_pl_leave - $leaveSta;
                }
            }
            LeaveSetting::where('employee_id', $userEmpID)->where('financial_year', $fincyear)
                ->update([
                    'remaining_pl_leave' => $remainingPlLeave,
                ]); 
            
            if ($remain_prev_yl > 0) {
                $prevYLPL = $remain_prev_yl - $leaveSta;

                //Addjst in Table
                if ($prevYLPL == '1') {
                $preYAdleave = 'Full Day';
                }elseif($prevYLPL == '-0.5'){
                $preYAdleave = 'Half Day';
                }else{
                $preYAdleave = 'Full Day';
                }
                
                //Previous Leave Insert
                $leave_deduct = 'Previous Year Leave';
                $leaveAdjustData = new LeaveAdjust();
                $leaveAdjustData->user_id = $userEmpID;
                $leaveAdjustData->leave_type = $checkLeaveType;
                $leaveAdjustData->leave_deduct = $leave_deduct;
                $leaveAdjustData->leave_adjust = $preYAdleave;
                $leaveAdjustData->leave_date = $leave_date;
                $leaveAdjustData->leave_month = $leaveMonth;
                $leaveAdjustData->leave_reason = $leave_reason;
                $leaveAdjustData->financial_year = $fincyear;
                $leaveAdjustData->save();

                if ($prevYLPL < 0) {
                    $leaveSta = abs($prevYLPL); 
                    $prevYLPL = 0; 
                } else {
                    $leaveSta = 0; 
                }
                $update_plRemain_prev_yl = LeaveSetting::where('employee_id', $userEmpID)
                ->where('financial_year', $fincyear)
                ->update(['remain_prev_yl' => $prevYLPL]);
            }

            if ($leaveSta > 0 && $remain_extdays > 0) { 
                $remainExtdays = $remain_extdays - $leaveSta;
                
                //Addjst in Table
                if ($remainExtdays == '1' || $remainExtdays == '-1') {
                $extYAdleave = 'Full Day';
                } elseif ($remainExtdays == '-0.5' || $remainExtdays == '0.5') {
                $extYAdleave = 'Half Day';
                } elseif ($remainExtdays == '0') {
                $extYAdleave = 'Full Day';
                } else {
                $extYAdleave = 'Unknown';
                }
                
                //Extra Leave Insert
                $leave_deduct = 'Extra Day';
                $leaveExtAdjustData = new LeaveAdjust();
                $leaveExtAdjustData->user_id = $userEmpID;
                $leaveExtAdjustData->leave_type = $checkLeaveType;
                $leaveExtAdjustData->leave_deduct = $leave_deduct;
                $leaveExtAdjustData->leave_adjust = $extYAdleave;
                $leaveExtAdjustData->leave_date = $leave_date;
                $leaveExtAdjustData->leave_month = $leaveMonth;
                $leaveExtAdjustData->leave_reason = $leave_reason;
                $leaveExtAdjustData->financial_year = $fincyear;
                $leaveExtAdjustData->save();

                if ($remainExtdays < 0) {
                    $leaveSta = abs($remainExtdays); 
                    $remainExtdays = 0; 
                } else {
                    $leaveSta = 0; 
                }
                $update_remain_extdays = LeaveSetting::where('employee_id', $userEmpID)
                    ->where('financial_year', $fincyear)
                    ->update(['remain_extdays' => $remainExtdays]);
            }
            
            $leaveSalDeduct = $preYAdleave ?? $extYAdleave ?? $checkLeaveStatus ?? 0;

            //LeaveData Get From Leave Table
            $request->validate([
                'leave_id' => 'required|integer|exists:leaves,id',
            ]);
            $leaveData = Leave::find($request->leave_id); 
            if (!$leaveData) {
                return response()->json(['status' => 'error', 'message' => 'Leave record not found.'], 404);
            }

            $userID = $leaveData->user_id;
            $leaveType = $leaveData->leave_type;
            $leaveReason = $leaveData->leave_reason;

            $leaveStatus = $leaveData->leave_status;
            $leave_status = match ($leaveStatus) {
            'FH', 'SH' => '0.5',
            'F' => '1',
            default => '',
            };

            $leaveDate = $leaveData->leave_date;
            $createdAt = strtotime($leaveDate);
            $year = date('Y', $createdAt);
            $month = date('F', $createdAt);

            $financialYear = $month >= 4
            ? $year . '-' . ($year + 1)
            : ($year - 1) . '-' . $year;

            $employeeData = Employee::find($userID);
            $empName = $employeeData ? $employeeData->first_name : null;

            //Save Data in leave_sal_deduction table
            $storeData = new LeaveSalDeduction();
            $storeData->employee_id = $userID;
            $storeData->employee_name = $empName;
            $storeData->leave_type = $leaveType;
            $storeData->salary_deduction = $leaveSalDeduct;
            $storeData->reason = $leaveReason;
            $storeData->date = $leaveDate;
            $storeData->month = $month;
            $storeData->financial_year = $financialYear;

            if ($storeData->save()) {
            return response()->json(['message' => 'Paid Leave deduction updated successfully.']);
            }
            return response()->json(['status' => 'error'], 500);
        }
        
        //SL LEAVE CALCULATION
        if ($checkLeaveType == 'SL') {
            if ($checkPaidUnpaidleavededuct == '1') {
                $sickleaveTaken = $taken_sl_leave + $leaveSta;
                LeaveSetting::where('employee_id', $userEmpID)->where('financial_year', $fincyear)
                    ->update([
                        'taken_sl_leave' => $sickleaveTaken,
                    ]);
            }

            if ($taken_sl_leave > $sick_leave) {
                if ($remaining_sl_leave > 0) {
                    $remainingSlLeave = $remaining_sl_leave - $leaveSta;
                } else {
                    $remainingSlLeave = $remaining_sl_leave - $leaveSta;
                }
            } else {
                if ($remaining_sl_leave < 0) {
                    if (fmod($remaining_sl_leave, 2) == 0) { 
                        $remainingSlLeave = $remaining_sl_leave - 1; 
                    } else {
                        $remainingSlLeave = $remaining_sl_leave + 1; 
                    }
                } else {
                    $remainingSlLeave = $remaining_sl_leave - $leaveSta;
                }
            }
            LeaveSetting::where('employee_id', $userEmpID)->where('financial_year', $fincyear)
                ->update([
                    'remaining_sl_leave' => $remainingSlLeave,
                ]);
            

            if ($remain_prev_yl > 0) {
                $prevYLSL = $remain_prev_yl - $leaveSta;

                //Addjst in Table
                if ($prevYLSL == '1') {
                $preYAdleave = 'Full Day';
                }elseif($prevYLSL == '-0.5'){
                $preYAdleave = 'Half Day';
                }else{
                $preYAdleave = 'Full Day';
                }
                
                //Previous Leave Insert
                $leave_deduct = 'Previous Year Leave';
                $leaveAdjustData = new LeaveAdjust();
                $leaveAdjustData->user_id = $userEmpID;
                $leaveAdjustData->leave_type = $checkLeaveType;
                $leaveAdjustData->leave_deduct = $leave_deduct;
                $leaveAdjustData->leave_adjust = $preYAdleave;
                $leaveAdjustData->leave_date = $leave_date;
                $leaveAdjustData->leave_month = $leaveMonth;
                $leaveAdjustData->leave_reason = $leave_reason;
                $leaveAdjustData->financial_year = $fincyear;
                $leaveAdjustData->save();
                
                if ($prevYLSL < 0) {
                    $leaveSta = abs($prevYLSL); 
                    $prevYLSL = 0; 
                } else {
                    $leaveSta = 0; 
                }
                $update_slRemain_prev_yl = LeaveSetting::where('employee_id', $userEmpID)
                ->where('financial_year', $fincyear)
                ->update(['remain_prev_yl' => $prevYLSL]);
            }

            if ($leaveSta > 0 && $remain_extdays > 0) { 
                $remainExtdays = $remain_extdays - $leaveSta;
                
                //Addjst in Table
                if ($remainExtdays == '1' || $remainExtdays == '-1') {
                $extYAdleave = 'Full Day';
                } elseif ($remainExtdays == '-0.5' || $remainExtdays == '0.5') {
                $extYAdleave = 'Half Day';
                } elseif ($remainExtdays == '0') {
                $extYAdleave = 'Full Day';
                } else {
                $extYAdleave = 'Unknown';
                }
                
                //Extra Leave Insert
                $leave_deduct = 'Extra Day';
                $leaveExtAdjustData = new LeaveAdjust();
                $leaveExtAdjustData->user_id = $userEmpID;
                $leaveExtAdjustData->leave_type = $checkLeaveType;
                $leaveExtAdjustData->leave_deduct = $leave_deduct;
                $leaveExtAdjustData->leave_adjust = $extYAdleave;
                $leaveExtAdjustData->leave_date = $leave_date;
                $leaveExtAdjustData->leave_month = $leaveMonth;
                $leaveExtAdjustData->leave_reason = $leave_reason;
                $leaveExtAdjustData->financial_year = $fincyear;
                $leaveExtAdjustData->save();

                if ($remainExtdays < 0) {
                    $leaveSta = abs($remainExtdays); 
                    $remainExtdays = 0; 
                } else {
                    $leaveSta = 0; 
                }
                $update_remain_extdays = LeaveSetting::where('employee_id', $userEmpID)
                    ->where('financial_year', $fincyear)
                    ->update(['remain_extdays' => $remainExtdays]);
            }
            
            $leaveSalDeduct = $preYAdleave ?? $extYAdleave ?? $checkLeaveStatus ?? 0;

            //LeaveData Get From Leave Table
            $request->validate([
                'leave_id' => 'required|integer|exists:leaves,id',
            ]);
            $leaveData = Leave::find($request->leave_id); 
            if (!$leaveData) {
                return response()->json(['status' => 'error', 'message' => 'Leave record not found.'], 404);
            }

            $userID = $leaveData->user_id;
            $leaveType = $leaveData->leave_type;
            $leaveReason = $leaveData->leave_reason;

            $leaveStatus = $leaveData->leave_status;
            $leave_status = match ($leaveStatus) {
            'FH', 'SH' => '0.5',
            'F' => '1',
            default => '',
            };

            $leaveDate = $leaveData->leave_date;
            $createdAt = strtotime($leaveDate);
            $year = date('Y', $createdAt);
            $month = date('F', $createdAt);

            $financialYear = $month >= 4
            ? $year . '-' . ($year + 1)
            : ($year - 1) . '-' . $year;

            $employeeData = Employee::find($userID);
            $empName = $employeeData ? $employeeData->first_name : null;

            //Save Data in leave_sal_deduction table
            $storeData = new LeaveSalDeduction();
            $storeData->employee_id = $userID;
            $storeData->employee_name = $empName;
            $storeData->leave_type = $leaveType;
            $storeData->salary_deduction = $leaveSalDeduct;
            $storeData->reason = $leaveReason;
            $storeData->date = $leaveDate;
            $storeData->month = $month;
            $storeData->financial_year = $financialYear;
              
            if ($storeData->save()) {
            return response()->json(['message' => 'Sick Leave deduction updated successfully.']);
            }
            return response()->json(['status' => 'error'], 500);
        }
        
        //CL LEAVE CALCULATION
        if ($checkLeaveType == 'CL') {
            if ($checkPaidUnpaidleavededuct == '1') {
                $casualleaveTaken = $taken_cl_leave + $leaveSta;
                LeaveSetting::where('employee_id', $userEmpID)->where('financial_year', $fincyear)
                    ->update([
                        'taken_cl_leave' => $casualleaveTaken,
                    ]);
            }

            if ($taken_cl_leave > $casual_leave) {
                if ($remaining_cl_leave > 0) {
                    $remainingClLeave = $remaining_cl_leave - $leaveSta;
                } else {
                    $remainingClLeave = $remaining_cl_leave - $leaveSta;
                }
            } else {
                if ($remaining_cl_leave < 0) {
                    if (fmod($remaining_cl_leave, 2) == 0) { 
                        $remainingClLeave = $remaining_cl_leave - 1; 
                    } else {
                        $remainingClLeave = $remaining_cl_leave + 1; 
                    }
                } else {
                    $remainingClLeave = $remaining_cl_leave - $leaveSta;
                }
            }
            LeaveSetting::where('employee_id', $userEmpID)->where('financial_year', $fincyear)
                ->update([
                    'remaining_cl_leave' => $remainingClLeave,
                ]);

            if ($remain_prev_yl > 0) {
                $prevYLCL = $remain_prev_yl - $leaveSta;
                
                //Addjst in Table
                if ($prevYLCL == '1' && $prevYLCL == '-1') {
                $preYAdleave = 'Full Day';
                }elseif($prevYLCL == '-0.5' && $prevYLCL == '0.5'){
                $preYAdleave = 'Half Day';
                }else{
                $preYAdleave = 'Full Day';
                }
                
                //Previous Leave Insert
                $leave_deduct = 'Previous Year Leave';
                $leaveAdjustData = new LeaveAdjust();
                $leaveAdjustData->user_id = $userEmpID;
                $leaveAdjustData->leave_type = $checkLeaveType;
                $leaveAdjustData->leave_deduct = $leave_deduct;
                $leaveAdjustData->leave_adjust = $preYAdleave;
                $leaveAdjustData->leave_date = $leave_date;
                $leaveAdjustData->leave_month = $leaveMonth;
                $leaveAdjustData->leave_reason = $leave_reason;
                $leaveAdjustData->financial_year = $fincyear;
                $leaveAdjustData->save();

                if ($prevYLCL < 0) {
                    $leaveSta = abs($prevYLCL); 
                    $prevYLCL = 0; 
                } else {
                    $leaveSta = 0; 
                }
                $update_clRemain_prev_yl = LeaveSetting::where('employee_id', $userEmpID)
                    ->where('financial_year', $fincyear)
                    ->update(['remain_prev_yl' => $prevYLCL]);
            
            }
            
            if ($leaveSta > 0 && $remain_extdays > 0) { 
                $remainExtdays = $remain_extdays - $leaveSta;
                
                //Addjst in Table
                if ($remainExtdays == '1' || $remainExtdays == '-1') {
                $extYAdleave = 'Full Day';
                } elseif ($remainExtdays == '-0.5' || $remainExtdays == '0.5') {
                $extYAdleave = 'Half Day';
                } elseif ($remainExtdays == '0') {
                $extYAdleave = 'Full Day';
                } else {
                $extYAdleave = 'Unknown';
                }
                
                //Extra Leave Insert
                $leave_deduct = 'Extra Day';
                $leaveExtAdjustData = new LeaveAdjust();
                $leaveExtAdjustData->user_id = $userEmpID;
                $leaveExtAdjustData->leave_type = $checkLeaveType;
                $leaveExtAdjustData->leave_deduct = $leave_deduct;
                $leaveExtAdjustData->leave_adjust = $extYAdleave;
                $leaveExtAdjustData->leave_date = $leave_date;
                $leaveExtAdjustData->leave_month = $leaveMonth;
                $leaveExtAdjustData->leave_reason = $leave_reason;
                $leaveExtAdjustData->financial_year = $fincyear;
                $leaveExtAdjustData->save();

                if ($remainExtdays < 0) {
                    $leaveSta = abs($remainExtdays); 
                    $remainExtdays = 0; 
                } else {
                    $leaveSta = 0; 
                }
                
                $update_remain_extdays = LeaveSetting::where('employee_id', $userEmpID)
                    ->where('financial_year', $fincyear)
                    ->update(['remain_extdays' => $remainExtdays]);
            }
            $leaveSalDeduct = $preYAdleave ?? $extYAdleave ?? $checkLeaveStatus ?? 0;

            //LeaveData Get From Leave Table
            $request->validate([
                'leave_id' => 'required|integer|exists:leaves,id',
            ]);
            $leaveData = Leave::find($request->leave_id); 
            if (!$leaveData) {
                return response()->json(['status' => 'error', 'message' => 'Leave record not found.'], 404);
            }

            $userID = $leaveData->user_id;
            $leaveType = $leaveData->leave_type;
            $leaveReason = $leaveData->leave_reason;

            $leaveStatus = $leaveData->leave_status;
            $leave_status = match ($leaveStatus) {
            'FH', 'SH' => '0.5',
            'F' => '1',
            default => '',
            };

            $leaveDate = $leaveData->leave_date;
            $createdAt = strtotime($leaveDate);
            $year = date('Y', $createdAt);
            $month = date('F', $createdAt);

            $financialYear = $month >= 4
            ? $year . '-' . ($year + 1)
            : ($year - 1) . '-' . $year;

            $employeeData = Employee::find($userID);
            $empName = $employeeData ? $employeeData->first_name : null;

            //Save Data in leave_sal_deduction table
            $storeData = new LeaveSalDeduction();
            $storeData->employee_id = $userID;
            $storeData->employee_name = $empName;
            $storeData->leave_type = $leaveType;
            $storeData->salary_deduction = $leaveSalDeduct;
            $storeData->reason = $leaveReason;
            $storeData->date = $leaveDate;
            $storeData->month = $month;
            $storeData->financial_year = $financialYear;
            
            if ($storeData->save()) {
            return response()->json(['message' => 'Casual Leave deduction updated successfully.']);
            }
            return response()->json(['status' => 'error'], 500);
            
        }
    }

    /*public function create(Request $request)
    {   
        $user_id = Auth::user()->id;
        $current_user_roll = auth()->user()->role;

        $fincyear = $this->financial_year();
        $get_leavesettings = LeaveSetting::where('employee_id', $user_id)->where('financial_year', $fincyear)->get();
        foreach ($get_leavesettings as $setting) {
            $total_per_year_casualLeave = $setting->casual_leave;
            $total_per_year_paidLeave = $setting->paid_leave;
            $total_per_year_sickLeave = $setting->sick_leave;
            $total_per_year_pre_yearLeave = $setting->previous_year_leave;
            $total_per_year_extraLeave = $setting->extra_days;
            $financial_year = $setting->financial_year;
        }
        
        //Get Emp ID
        if($request->users){
            $user_id = $request->users;
        }
        //Start Get CL leave
        //Cl Count
        $count_half_CL =  Leave::where('user_id',$user_id)->where('leave_type','CL')->where('leave_status','!=','F')->where('status','Approved')->count(); 
        
        if($count_half_CL == 0){
            $formatted_half_CL = 0;
        }
        else{
            $total_half_CL = $count_half_CL * 0.5;
            $formatted_half_CL = rtrim(rtrim($total_half_CL, '0'), '.');
        } 
        
        $count_full_CL =  Leave::where('user_id',$user_id)->where('leave_type','CL')->where('leave_status','F')->where('status','Approved')->count(); 
        $taken_cl_leave = $count_full_CL + $formatted_half_CL;
        $remaining_cl_leave = $total_per_year_casualLeave - $taken_cl_leave;
        
        //CL Update
        $update_cl_leave = LeaveSetting::where('employee_id', $user_id)->where('financial_year', $fincyear)
            ->update([
                'taken_cl_leave' => $taken_cl_leave, 
                'remaining_cl_leave' => $remaining_cl_leave, 
         ]);
        //End CL Count

        //Start Get SL leave
        $count_half_SL =  Leave::where('user_id',$user_id)->where('leave_type','SL')->where('leave_status','!=','F')->where('status','Approved')->count(); 

        if($count_half_SL == 0){
            $formatted_half_SL = 0;
        }
        else{
            $total_half_SL = $count_half_SL * 0.5;
            $formatted_half_SL = rtrim(rtrim($total_half_SL, '0'), '.');
        } 

        $count_full_SL =  Leave::where('user_id',$user_id)->where('leave_type','SL')->where('leave_status','F')->where('status','Approved')->count(); 

        $taken_sl_leave = $count_full_SL + $formatted_half_SL;
        $remaining_sl_leave = $total_per_year_sickLeave - $taken_sl_leave;
        
        $update_sl_leave = LeaveSetting::where('employee_id', $user_id)->where('financial_year', $fincyear)
            ->update([
                'taken_sl_leave' => $taken_sl_leave, 
                'remaining_sl_leave' => $remaining_sl_leave, 
            ]);
        //End SL Count

        //Start Get PL leave
        $count_half_PL =  Leave::where('user_id',$user_id)->where('leave_type','PL')->where('leave_status','!=','F')->where('status','Approved')->count(); 
        if($count_half_PL == 0){
            $formatted_half_PL = 0;
        }
        else{
            $total_half_PL = $count_half_PL * 0.5;
            $formatted_half_PL = rtrim(rtrim($total_half_PL, '0'), '.');
        } 

        $count_full_PL =  Leave::where('user_id',$user_id)->where('leave_type','PL')->where('leave_status','F')->where('status','Approved')->count(); 
        $taken_pl_leave = $count_full_PL + $formatted_half_PL;
        $remaining_pl_leave = $total_per_year_paidLeave - $taken_pl_leave;
        
        $update_pl_leave = LeaveSetting::where('employee_id', $user_id)->where('financial_year', $fincyear)
            ->update([
                'taken_pl_leave' => $taken_pl_leave, 
                'remaining_pl_leave' => $remaining_pl_leave, 
            ]);
        //End PL Count
        
        //Check Sick leave Adjust
        $salary_deduction_sl_leave = 0; 
        if ($total_per_year_sickLeave < $taken_sl_leave) {
            $over_sl_leave = $taken_sl_leave - $total_per_year_sickLeave;
            if ($total_per_year_pre_yearLeave > 0) {
                $over_sl_leave = max(0, $over_sl_leave - $total_per_year_pre_yearLeave);
            }
            
            if ($total_per_year_extraLeave > 0) {
                $salary_deduction_sl_leave = max(0, $over_sl_leave - $total_per_year_extraLeave);
            } else {
                $salary_deduction_sl_leave = $over_sl_leave;
            }

            if ($salary_deduction_sl_leave > 0) {
                $salary_deduction_sl_leave;
            } else {
                
            }
        }

        //Check Casual Leave Adjust
        $salary_deduction_cl_leave = 0;
        if ($taken_cl_leave > $total_per_year_casualLeave) {
            $over_cl_leave = $taken_cl_leave - $total_per_year_casualLeave;
            if ($total_per_year_pre_yearLeave > 0) {
                $over_cl_leave = max(0, $over_cl_leave - $total_per_year_pre_yearLeave);
            }
            if ($total_per_year_extraLeave > 0) {
                $over_cl_leave = max(0, $over_cl_leave - $total_per_year_extraLeave);
            }
            $salary_deduction_cl_leave = $over_cl_leave;
            if ($salary_deduction_cl_leave > 0) {
                $salary_deduction_cl_leave;
            }else {
                
            }
        }
  
        //Check Paid Leave Adjust
        $salary_deduction_pl_leave = 0; 
        if ($total_per_year_paidLeave < $taken_pl_leave) {
            $over_pl_leave = $taken_pl_leave - $total_per_year_paidLeave;
            
            if ($total_per_year_pre_yearLeave > 0) {
                $over_pl_leave = max(0, $over_pl_leave - $total_per_year_pre_yearLeave);
            }

            if ($total_per_year_extraLeave > 0) {
                $salary_deduction_pl_leave = max(0, $over_pl_leave - $total_per_year_extraLeave);
            } else {
                $salary_deduction_pl_leave = $over_pl_leave;
            }

            if ($salary_deduction_pl_leave > 0) {
                $salary_deduction_pl_leave;
            } else {
               
            }
        }
        
        $employeeData = Employee::find($user_id);
        
        //Get Emplyoee Name
        if ($employeeData) {
        $employeename = $employeeData->first_name;
        } 

        //Check leave type
        if ($salary_deduction_pl_leave) {
            $leave_type = 'PL';
            $salary_deduction = $salary_deduction_pl_leave;
            $reason = 'No Any PL, Previous Year Leave, Extra Leave Available';
        }elseif ($salary_deduction_sl_leave) {
            $leave_type = 'SL';
            $salary_deduction = $salary_deduction_sl_leave;
            $reason = 'No Any SL, Previous Year Leave, Extra Leave Available';
        }elseif ($salary_deduction_cl_leave) {
            $leave_type = 'CL';
            $salary_deduction = $salary_deduction_cl_leave;
            $reason = 'No Any CL, Previous Year Leave, Extra Leave Available';
        }else{
            $leave_type = '';
            $salary_deduction = '';
            $reason = '';
        }
        
        $lastFewLeavesQuery = Leave::where('user_id', $user_id)
        ->where('leave_type', $leave_type)
        ->where('status', 'Approved')
        ->orderBy('leave_date', 'desc');
      
        $salary_deduction = (float)$salary_deduction; 
        $integerPart = floor($salary_deduction);
        $fractionalPart = $salary_deduction - $integerPart;
        
        $lastFewLeaves = $lastFewLeavesQuery->take($integerPart)->get();
        if ($fractionalPart > 0) {
            $extraLeave = $lastFewLeavesQuery->skip($integerPart)->first();
            if ($extraLeave) {
                if ($fractionalPart == 0.5) {
                    $extraLeave->leave_status = 'FH';  
                    $extraLeave->leave_days = 0.5;
                }
              $lastFewLeaves->push($extraLeave);
            }
        }
        
        $lastFewLeaves = Leave::where('user_id', $user_id)
            ->where('leave_type', $leave_type)
            ->where('status', 'Approved')
            ->orderBy('leave_date', 'desc');
        
        if ($salary_deduction == 0.5) {
            $lastFewLeaves = $lastFewLeaves->take(1);
        } elseif (fmod($salary_deduction, 1) == 0.5) {
            $integer_part = (int) $salary_deduction;
            $lastFewLeaves = $lastFewLeaves->take($integer_part + 1);
        } else {
            $lastFewLeaves = $lastFewLeaves->take((int) $salary_deduction);
        }
        $lastFewLeaves = $lastFewLeaves->get();

        $salary_deductions = [];
        foreach ($lastFewLeaves as $leave) {
            $leave_status = $leave->leave_status; 

            if ($salary_deduction == 0.5) {
                $leave_day = 'Half Day'; 
            } elseif ($leave_status == 'F') {
                $leave_day = 'Full Day';
            } elseif ($leave_status == 'FH') {
                $leave_day = 'First Half';
            } elseif ($leave_status == 'SH') {
                $leave_day = 'Second Half';
            } else {
                continue; 
            }
            $leave_type = $leave->leave_type; 
            $formattedDate = Carbon::parse($leave->leave_date)->format('Y-m-d'); 
            $month = Carbon::parse($leave->leave_date)->format('F');
            
            $salary_deductions[] = [
                'employee_id' => $user_id,
                'employee_name' => $employeename,
                'leave_type' => $leave_type,
                'salary_deduction' => $leave_day,
                'reason' => $reason,
                'date' => $formattedDate,
                'month' => $month,
                'financial_year' => $fincyear,
                'created_at' => now(), 
                'updated_at' => now()  
            ];
        }    
        SalaryDeduction::insert($salary_deductions);
       return view('salary-deduction.create', compact('salary_deduction_data', 'employeeData', 'salary_deduction_pl_leave', 'salary_deduction_sl_leave', 'salary_deduction_cl_leave'));
    }*/

    public function create(Request $request){
        $userID = Auth::user()->id;
        $financialY = $this->financial_year();
        $leaveData = Leave::where('user_id', $userID)->get();
        foreach ($leaveData as $leaveValue) {
            $leave_user_id = $leaveValue->user_id;
        }
        $leave_user_id =  $leave_user_id;
        $current_user_roll = auth()->user()->role;
        $currentadmin_id = Auth::id();
        
        //ALL Per Year Leave setting data
        $get_leavesettings = LeaveSetting::where('employee_id', $leave_user_id)->where('financial_year', $financialY)->get();
        foreach ($get_leavesettings as $settingsdata) {
            $total_per_year_casualLeaves = $settingsdata->casual_leave;
            $total_per_year_paidLeave = $settingsdata->paid_leave;
            $total_per_year_sickLeave = $settingsdata->sick_leave;
            $total_per_year_pre_yearLeave = $settingsdata->previous_year_leave;
            $remain_prev_yl = $settingsdata->remain_prev_yl;
            $total_per_year_extraLeave = $settingsdata->extra_days;
            $remain_extdays = $settingsdata->remain_extdays;
            $financial_year = $settingsdata->financial_year;
        }
       
        //GET ALL LEAVE
        /* $all_approved_leaves = Leave::where('user_id', $leave_user_id)
            ->where('status', 'Approved')  
            ->get();
        $all_leave_count = $all_approved_leaves->count();*/    
         
        //GET CL LEAVE
        $count_half_CL = Leave::where('user_id', $leave_user_id)
            ->where('leave_type', 'CL')  
            ->where('leave_status', '!=', 'F')  
            ->where('status', 'Approved')  
            ->latest('created_at')  
            ->count();  
        if ($count_half_CL == 0) {
            $formatted_half_CL = 0;
        } else {
            $total_half_CL = $count_half_CL * 0.5;
            $formatted_half_CL = rtrim(rtrim($total_half_CL, '0'), '.');  
        }
        $count_full_CL = Leave::where('user_id', $leave_user_id)
            ->where('leave_type', 'CL') 
            ->where('leave_status', 'F')  
            ->where('status', 'Approved')  
            ->latest('created_at')  
            ->count(); 
        $taken_cl_leave = $count_full_CL + $formatted_half_CL;
        
        $allLeave = [];
        if ($taken_cl_leave > $total_per_year_casualLeaves) {
            $remaining_cl_leave = $total_per_year_casualLeaves - $taken_cl_leave; 
            $remaining_cl_leave =  round($remaining_cl_leave);
            $remaining_cl_leave =  abs($remaining_cl_leave);
            
            $offset = 10;
            $remaining_cl_leave = (int)($remaining_cl_leave);

            $lastFewCLLeavesQuery = Leave::where('user_id', $leave_user_id)
                ->where('leave_type', 'CL')
                ->where('status', 'Approved')
                ->orderBy('leave_date', 'desc')
                ->take($remaining_cl_leave)
                ->get();
        $allLeave[] = $lastFewCLLeavesQuery;
        } 
       
        //GET PL LEAVE
        $count_half_PL = Leave::where('user_id', $leave_user_id)
            ->where('leave_type', 'PL')  
            ->where('leave_status', '!=', 'F')  
            ->where('status', 'Approved')  
            ->latest('created_at')  
            ->count();  
        if ($count_half_PL == 0) {
            $formatted_half_PL = 0;
        } else {
            $total_half_PL = $count_half_PL * 0.5;
            $formatted_half_PL = rtrim(rtrim($total_half_PL, '0'), '.');  
        }
        $count_full_PL = Leave::where('user_id', $leave_user_id)
            ->where('leave_type', 'PL') 
            ->where('leave_status', 'F')  
            ->where('status', 'Approved')  
            ->latest('created_at')  
            ->count(); 
        $taken_pl_leave = $count_full_PL + $formatted_half_PL; 

        if ($taken_pl_leave > $total_per_year_paidLeave) {
            $remaining_pl_leave = $total_per_year_paidLeave - $taken_pl_leave; 
            $remaining_pl_leave =  round($remaining_pl_leave);
            $remaining_pl_leave =  abs($remaining_pl_leave);
            
            $offset = 10;
            $remaining_pl_leave = (int)($remaining_pl_leave);

            $lastFewPLLeavesQuery = Leave::where('user_id', $leave_user_id)
            ->where('leave_type', 'PL')
            ->where('status', 'Approved')
            ->orderBy('leave_date', 'desc')
            ->take($remaining_pl_leave)
            ->get();
        $allLeave[] = $lastFewPLLeavesQuery;
        } 

        //GET SL LEAVE
        $count_half_SL = Leave::where('user_id', $leave_user_id)
            ->where('leave_type', 'SL')  
            ->where('leave_status', '!=', 'F')  
            ->where('status', 'Approved')  
            ->latest('created_at')  
            ->count();  
        if ($count_half_SL == 0) {
            $formatted_half_SL = 0;
        } else {
            $total_half_SL = $count_half_SL * 0.5;
            $formatted_half_SL = rtrim(rtrim($total_half_SL, '0'), '.');  
        }
        $count_full_SL = Leave::where('user_id', $leave_user_id)
            ->where('leave_type', 'SL') 
            ->where('leave_status', 'F')  
            ->where('status', 'Approved')  
            ->latest('created_at')  
            ->count(); 
        $taken_sl_leave = $count_full_SL + $formatted_half_SL;
        if ($taken_sl_leave > $total_per_year_sickLeave) {
            $remaining_sl_leave = $total_per_year_sickLeave - $taken_sl_leave; 
            $remaining_sl_leave =  round($remaining_sl_leave);
            $remaining_sl_leave =  abs($remaining_sl_leave);
            
            $offset = 10;
            $remaining_sl_leave = (int)($remaining_sl_leave);

        $lastFewSLLeavesQuery = Leave::where('user_id', $leave_user_id)
            ->where('leave_type', 'SL')
            ->where('status', 'Approved')
            ->orderBy('leave_date', 'desc')
            ->take($remaining_sl_leave)
            ->get();
        $allLeave[] = $lastFewSLLeavesQuery;
        } 
        // dd($allLeave);
        /*if($currentadmin_id == 1){
        return view('salary-deduction.index', compact('allLeave'));
        }else{
        return redirect()->back();
        }*/
        //Save Leave Deduction Data
    }

    public function edit(Request $request)
    {   
        $salaryDedid = $request->id;
        $salaryDeductionData = SalaryDeduction::find($salaryDedid);
        $currentadmin_id = Auth::id();
        $employeerec = DB::table('employees')
        ->whereNull('service_enddate')
        ->whereNotIn('id', [1, 15]) 
        ->get();

        $selectedId = $salaryDeductionData ? $salaryDeductionData->employee_id : null;
        if($currentadmin_id == 1){
        return view('salary-deduction.edit', compact('salaryDeductionData','employeerec','selectedId'));
        }else{
        return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {   
        $requestID = $request->id;
        $salaryDeductionData = SalaryDeduction::find($requestID);
        /*foreach ($salaryDeductionData as  $salvalue) {
            $empName = $salvalue->employee_name;
        }*/
        //dd($empName);

        $request->validate([
            'leave_type' => 'required|string',
            'reason' => 'nullable|string',
            'date' => 'required|date',
            'month' => 'required|string',
            'salary_deduction' => 'required|string',
        ]);

        $finc_year = $this->financial_year();

        $salaryDeduction = SalaryDeduction::findOrFail($requestID);
        
        $salaryDeduction->leave_type = $request->input('leave_type');
        $salaryDeduction->reason = $request->input('reason');
        $salaryDeduction->date = $request->input('date');
        $salaryDeduction->month = $request->input('month');
        $salaryDeduction->salary_deduction = $request->input('salary_deduction');
        $salaryDeduction->financial_year = $finc_year;
        
        $salaryDeduction->save();
        return redirect()->back()->with('Salary_updateSuccess', ' 💸 The salary deduction has been updated successfully.');
    }

    public function destroy(Request $request, $id)
    {   
        $requestID = $request->id;
        $fincyear = $this->financial_year();
        $leaveSaldedCalData = SalaryDeduction::findOrFail($requestID);

        $empID = $leaveSaldedCalData->employee_id;
        $leave_type = $leaveSaldedCalData->leave_type;
        $salary_deduction = $leaveSaldedCalData->salary_deduction;
        
        //SalaryDeduction::find($requestID)->delete();

        $leaveSettingCalData = LeaveSetting::where('employee_id', $empID)->where('financial_year', $fincyear)->get();
        foreach ($leaveSettingCalData as  $calLeavevalue) {
            $taken_sl_leave = $calLeavevalue->taken_sl_leave;
            $remaining_sl_leave = $calLeavevalue->remaining_sl_leave;

            $taken_cl_leave = $calLeavevalue->taken_cl_leave;
            $remaining_cl_leave = $calLeavevalue->remaining_cl_leave;

            $taken_pl_leave = $calLeavevalue->taken_pl_leave;
            $remaining_pl_leave = $calLeavevalue->remaining_pl_leave;
        }
        
        $deductSalSL = 0;
        $deductSalCL = 0;
        $deductSalPL = 0;

        if ($leave_type == 'SL') {
            if ($salary_deduction == 'Half Day' || $salary_deduction == 'First Half' || $salary_deduction == 'Second Half') {
                $deductSalSL = 0.5;
            } elseif ($salary_deduction == 'Full Day') {
                $deductSalSL = 1;
            }
        } elseif ($leave_type == 'CL') {
            if ($salary_deduction == 'Half Day' || $salary_deduction == 'First Half' || $salary_deduction == 'Second Half') {
                $deductSalCL = 0.5;
            } elseif ($salary_deduction == 'Full Day') {
                $deductSalCL = 1;
            }
        } elseif ($leave_type == 'PL') {
            if ($salary_deduction == 'Half Day' || $salary_deduction == 'First Half' || $salary_deduction == 'Second Half') {
                $deductSalPL = 0.5;
            } elseif ($salary_deduction == 'Full Day') {
                $deductSalPL = 1;
            }
        }

        // Perform calculations
        $takenSLCalForDelete = $taken_sl_leave - $deductSalSL;
        $deductSalSL = -abs($deductSalSL);
        $remainSLCalForDelete = $remaining_sl_leave - $deductSalSL;

        $takenCLCalForDelete = $taken_cl_leave - $deductSalCL;
        $deductSalCL = -abs($deductSalCL);
        $remainCLCalForDelete = $remaining_cl_leave - $deductSalCL;
         
        $takenPLCalForDelete = $taken_pl_leave - $deductSalPL;
        $deductSalPL = -abs($deductSalPL);
        $remainPLCalForDelete = $remaining_pl_leave - $deductSalPL;
        
        //Update Data
        if ($leave_type == 'SL') {
            $update_sl_leave = LeaveSetting::where('employee_id', $empID)->where('financial_year', $fincyear)
            ->update([
            'taken_sl_leave' => $takenSLCalForDelete, 
            'remaining_sl_leave' => $remainSLCalForDelete, 
            ]);
        }

        if($leave_type == 'CL') {
            //update CL in LeaveSetting
            $update_cl_leave = LeaveSetting::where('employee_id', $empID)->where('financial_year', $fincyear)
            ->update([
            'taken_cl_leave' => $takenCLCalForDelete, 
            'remaining_cl_leave' => $remainCLCalForDelete, 
            ]);
        }

        if($leave_type == 'PL'){
            //update PL in LeaveSetting
            $update_pl_leave = LeaveSetting::where('employee_id', $empID)->where('financial_year', $fincyear)
            ->update([
            'taken_pl_leave' => $takenPLCalForDelete, 
            'remaining_pl_leave' => $remainPLCalForDelete, 
            ]);
        }
         
        $leaveSaldedCalData->delete();
        
        $trash_can_emoji = "\u{1F5D9}";
        return response()->json(['Salary_removeSuccess' => '❌ The salary deduction has been remove successfully. '.  $trash_can_emoji]);
    }

    /*public function destroy(Request $request, $id)
    {
        $requestID = $request->id;
        $fincyear = $this->financial_year();

        // Fetch the salary deduction record
        $leaveSaldedCalData = SalaryDeduction::findOrFail($requestID);

        // Extract relevant data
        $empID = $leaveSaldedCalData->employee_id;
        $leave_type = $leaveSaldedCalData->leave_type;
        $salary_deduction = $leaveSaldedCalData->salary_deduction;

        // Fetch leave settings for the employee
        $leaveSettingCalData = LeaveSetting::where('employee_id', $empID)
            ->where('financial_year', $fincyear)
            ->first();

        if (!$leaveSettingCalData) {
            return redirect()->back()->withErrors('No leave settings found for this employee and financial year.');
        }

        // Extract existing leave values
        $taken_sl_leave = $leaveSettingCalData->taken_sl_leave;
        $remaining_sl_leave = $leaveSettingCalData->remaining_sl_leave;

        $taken_cl_leave = $leaveSettingCalData->taken_cl_leave;
        $remaining_cl_leave = $leaveSettingCalData->remaining_cl_leave;

        $taken_pl_leave = $leaveSettingCalData->taken_pl_leave;
        $remaining_pl_leave = $leaveSettingCalData->remaining_pl_leave;

        // Initialize deduction variables
        $deductSalSL = 0;
        $deductSalCL = 0;
        $deductSalPL = 0;

        // Determine deduction values
        if ($leave_type == 'SL') {
            $deductSalSL = ($salary_deduction == 'Full Day') ? 1 : 0.5;
        } elseif ($leave_type == 'CL') {
            $deductSalCL = ($salary_deduction == 'Full Day') ? 1 : 0.5;
        } elseif ($leave_type == 'PL') {
            $deductSalPL = ($salary_deduction == 'Full Day') ? 1 : 0.5;
        }

        // Perform calculations
        if ($leave_type == 'SL') {
            $takenSLCalForDelete = $taken_sl_leave - $deductSalSL;
            $remainSLCalForDelete = $remaining_sl_leave + $deductSalSL;

            LeaveSetting::where('employee_id', $empID)
                ->where('financial_year', $fincyear)
                ->update([
                    'taken_sl_leave' => $takenSLCalForDelete,
                    'remaining_sl_leave' => $remainSLCalForDelete,
                ]);
        } elseif ($leave_type == 'CL') {
            $takenCLCalForDelete = $taken_cl_leave - $deductSalCL;
            $remainCLCalForDelete = $remaining_cl_leave + $deductSalCL;

            LeaveSetting::where('employee_id', $empID)
                ->where('financial_year', $fincyear)
                ->update([
                    'taken_cl_leave' => $takenCLCalForDelete,
                    'remaining_cl_leave' => $remainCLCalForDelete,
                ]);
        } elseif ($leave_type == 'PL') {
            $takenPLCalForDelete = $taken_pl_leave - $deductSalPL;
            $remainPLCalForDelete = $remaining_pl_leave + $deductSalPL;

            LeaveSetting::where('employee_id', $empID)
                ->where('financial_year', $fincyear)
                ->update([
                    'taken_pl_leave' => $takenPLCalForDelete,
                    'remaining_pl_leave' => $remainPLCalForDelete,
                ]);
        }

        // Delete the record after updates
        $leaveSaldedCalData->delete();

        return redirect()->back()->with('Salary_removeSuccess', '❌ The salary deduction has been removed successfully.');
    }*/

    public function salary_deduction(Request $request){ 
       if (!auth()->check()) {
            return redirect()->route('login');
        }  
        $current_user_roll = auth()->user()->role;
        $leave_user_id = auth()->user()->id;
        $financialY = $this->financial_year();

        $employeerec = DB::table('employees')
            ->whereNull('service_enddate')
            ->whereNotIn('id', [1, 15]) 
            ->get();
        
        $employeLeave = DB::table('leaves')
            ->select('leave_type')
            ->where('status', 'Approved')
            ->whereNotIn('id', [1, 15]) 
            ->get();

        if ($current_user_roll == '2') {
            $employees = DB::table('employees')
                ->whereNull('service_enddate')
                ->whereNotIn('id', [1, 15])
                ->get();
        } else {
            $employees = DB::table('employees')
                ->where('id', $leave_user_id)
                ->get();
        }

        $getSalaryDeductedData = [];
        
        foreach ($employees as $employee) {
            $leave_user_id = $employee->id;
            $salaryData = LeaveSalDeduction::where('employee_id', $leave_user_id)
                ->where('financial_year', $financialY)
                ->get();

            foreach ($salaryData as $data) {
                $isAdjusted = DB::table('leave_adjust')
                    ->where('user_id', $data->employee_id)
                    ->where('leave_type', $data->leave_type)
                    ->where('leave_date', $data->date)
                    ->first();
                $leaveDeduct = $isAdjusted ? $isAdjusted->leave_deduct : null;

                $getSalaryDeductedData[] = [
                    'id' => $data->id,
                    'employee_id' => $data->employee_id,
                    'employee_name' => $data->employee_name,
                    'leave_type' => $data->leave_type,
                    'salary_deduction' => $data->salary_deduction,
                    'reason' => $data->reason,
                    'date' => $data->date,
                    'month' => $data->month,
                    'financial_year' => $data->financial_year,
                    'is_adjusted' => $isAdjusted ? 'Yes' : 'No',
                    'leave_deduct' => $leaveDeduct,
                ];
            }
        }
       
        return view('salary-deduction.salary-deducted', compact('getSalaryDeductedData', 'employeLeave', 'employeerec', 'current_user_roll'));
    }

    public function leave_adjust(Request $request){
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $currentadmin_id = auth()->check() ? Auth::id() : null;
        $current_user_role = auth()->user()->role;
        $leave_user_id = auth()->user()->id;
        $financialY = $this->financial_year();

        $employeerec = DB::table('employees')
            ->whereNull('service_enddate')
            ->whereNotIn('id', [1, 15]) 
            ->get();
         $employeLeave = DB::table('leaves')
            ->select('leave_type')
            ->where('status', 'Approved')
            ->whereNotIn('id', [1, 15]) 
            ->get();
        
        if ($current_user_role == '2') {
            $employees = DB::table('employees')
                ->whereNull('service_enddate')
                ->whereNotIn('id', [1, 15])
                ->get();
        } else {
            $employees = DB::table('employees')
                ->where('id', $leave_user_id)
                ->get();
        }

        $getSalaryAdjustData = [];

        foreach ($employees as $employee) {
            $leave_user_id = $employee->id;
            $leaveAdjData = LeaveAdjust::where('user_id', $leave_user_id)
                ->where('financial_year', $financialY)
                ->get();
            $getSalaryAdjustData = array_merge($getSalaryAdjustData, $leaveAdjData->toArray());
        }

        foreach ($getSalaryAdjustData as &$data) {
            $employee = \App\Models\Employee::find($data['user_id']);
            $data['employee_name'] = $employee ? $employee->first_name . ' ' . $employee->last_name : 'N/A';
        }
        
        return view('salary-deduction.leave-adjust', compact('getSalaryAdjustData', 'employeerec', 'employeLeave', 'current_user_role'));
        
    }
      
}
