<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WeekHour;
use App\Models\Leave;
use App\Models\WorkreportList;
use App\Models\Employee;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class WeeklyCountHours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weekly:CountHours';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Weekly Total Hours Count';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /*
        |--------------------------------------------------------------------------
        | DEBUG MODE
        |--------------------------------------------------------------------------
        |
        | true  = only log calculation, no database update
        | false = perform database updates
        |
        */
        $debugMode = true;

        // DEBUG TEST: process only User ID 28.
        $debugUserId = 21;

        /*
        |--------------------------------------------------------------------------
        | CURRENT WEEK
        |--------------------------------------------------------------------------
        */

        if ($debugMode) {

            // Fixed test week
            $week_startDate = '2026-07-27';
            $week_endDate   = '2026-08-02';

        } else {

            // Normal cron week
            //$week_startDate = now()->startOfWeek()->format('Y-m-d');
            //$week_endDate   = now()->endOfWeek()->format('Y-m-d');
            $week_startDate = '2026-08-24';
            $week_endDate   = '2026-08-30';
        }

        /*
        |--------------------------------------------------------------------------
        | INACTIVE EMPLOYEES
        |--------------------------------------------------------------------------
        */

        $inactiveUsers = Employee::whereNotNull('service_enddate')
            ->pluck('id');

        /*
        |--------------------------------------------------------------------------
        | USERS HAVING WEEK HOUR RECORD
        |--------------------------------------------------------------------------
        */

        $user_ids = WeekHour::whereDate('week_start_date', $week_startDate)
            ->whereDate('week_end_date', $week_endDate)
            ->whereNotIn('user_id', $inactiveUsers)
            ->distinct()
            ->pluck('user_id')
            ->toArray();

        // DEBUG TEST: only process User ID 28.
        if ($debugMode) {
            $user_ids = [$debugUserId];
            Log::info('DEBUG TEST SETTINGS', [
                'user_id' => $debugUserId,
                'week_start_date' => $week_startDate,
                'week_end_date' => $week_endDate,
                'database_updates' => false,
            ]);
        }

        foreach ($user_ids as $user_id) {

            Log::info('-------------------------------------------------');
            Log::info($debugMode ? 'DEBUG - START USER WEEKLY CALCULATION' : 'CRON - START USER WEEKLY CALCULATION', [
                'user_id' => $user_id,
            ]);

            /*
            |--------------------------------------------------------------------------
            | EMPLOYEE
            |--------------------------------------------------------------------------
            */

            $employee = Employee::where('id', $user_id)->first();

            /*
            |--------------------------------------------------------------------------
            | USER SETTINGS
            |--------------------------------------------------------------------------
            */

            $user = DB::table('users')
                ->where('id', $user_id)
                ->first();

            if (!$user) {
                Log::warning('User not found for weekly calculation', [
                    'user_id' => $user_id,
                ]);

                continue;
            }

            $role = $user->role;

            /*
            |--------------------------------------------------------------------------
            | HOURLY EMPLOYEE
            |--------------------------------------------------------------------------
            |
            | role = 1
            | sift_type = 3
            |
            | Settings are now stored in USERS table.
            |
            */

            $isHourlyEmployee = (
                $role == 1 &&
                $employee?->sift_type == 3
            );

            /*
            |--------------------------------------------------------------------------
            | HOURLY SETTINGS FROM USERS TABLE
            |--------------------------------------------------------------------------
            */

            $hourly_total_hours       = null;
            $hourly_min_full_day_hour = null;
            $hourly_min_half_day_hour = null;
            $hourly_max_carry_forward = null;

            if ($isHourlyEmployee) {

                $hourly_total_hours =
                    $user->total_hours;

                $hourly_min_full_day_hour =
                    $user->min_full_day_hour;

                $hourly_min_half_day_hour =
                    $user->min_half_day_hour;

                $hourly_max_carry_forward =
                    $user->max_carry_forward;

                Log::info('HOURLY EMPLOYEE SETTINGS', [
                    'user_id' => $user_id,
                    'role' => $role,
                    'sift_type' => $employee?->sift_type,
                    'total_hours' => $hourly_total_hours,
                    'min_full_day_hour' => $hourly_min_full_day_hour,
                    'min_half_day_hour' => $hourly_min_half_day_hour,
                    'max_carry_forward' => $hourly_max_carry_forward,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | GET WORKING HOURS FROM WORKREPORT LIST
            |--------------------------------------------------------------------------
            */

            $get_hours = WorkreportList::where('user_id', $user_id)
                ->whereBetween(
                    'work_date',
                    [
                        $week_startDate,
                        $week_endDate
                    ]
                )
                ->get();

            Log::info('WORK REPORTS FOUND', [
                'user_id' => $user_id,
                'employee_name' => $employee?->name ?? null,
                'work_report_count' => $get_hours->count(),
            ]);

            $totalHours = 0;
            $totalMinutes = 0;
            $totalSeconds = 0;

            foreach ($get_hours as $record) {

                Log::info('WORK REPORT DEBUG', [
                    'user_id' => $user_id,
                    'workreport_id' => $record->id,
                    'work_date' => $record->work_date,
                    'working_hours' => $record->working_hours,
                ]);

                if (empty($record->working_hours)) {
                    continue;
                }

                $timeParts = explode(':', $record->working_hours);

                $hours = (int) ($timeParts[0] ?? 0);
                $minutes = (int) ($timeParts[1] ?? 0);
                $seconds = (int) ($timeParts[2] ?? 0);

                $totalHours += $hours;
                $totalMinutes += $minutes;
                $totalSeconds += $seconds;

                Log::info('WORK REPORT TIME ADDED', [
                    'user_id' => $user_id,
                    'workreport_id' => $record->id,
                    'hours' => $hours,
                    'minutes' => $minutes,
                    'seconds' => $seconds,
                    'running_total' => [
                        'hours' => $totalHours,
                        'minutes' => $totalMinutes,
                        'seconds' => $totalSeconds,
                    ],
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | NORMALIZE TIME
            |--------------------------------------------------------------------------
            */

            $totalMinutes += floor($totalSeconds / 60);
            $totalSeconds %= 60;

            $totalHours += floor($totalMinutes / 60);
            $totalMinutes %= 60;

            $totalTimeFormatted = sprintf(
                '%02d:%02d:%02d',
                $totalHours,
                $totalMinutes,
                $totalSeconds
            );

            Log::info('TOTAL WEEKLY WORKING HOURS CALCULATED', [
                'user_id' => $user_id,
                'employee_name' => $employee?->name ?? null,
                'week_start' => $week_startDate,
                'week_end' => $week_endDate,
                'total_working_hours' => $totalTimeFormatted,
            ]);

            /*
            |--------------------------------------------------------------------------
            | CURRENT WEEK HOUR RECORD
            |--------------------------------------------------------------------------
            */

            $total_hours = WeekHour::where('user_id', $user_id)
                ->where('week_start_date', $week_startDate)
                ->where('week_end_date', $week_endDate)
                ->first();

            if (!$total_hours) {

                Log::warning(
                    'Current WeekHour record not found',
                    [
                        'user_id' => $user_id,
                        'week_start' => $week_startDate,
                        'week_end' => $week_endDate,
                    ]
                );

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | PREVIOUS WEEK RECORD
            |--------------------------------------------------------------------------
            */

            $prevWeekHour = WeekHour::where('user_id', $user_id)
                ->where('id', '<', $total_hours->id)
                ->orderBy('id', 'desc')
                ->first();

            /*
            |--------------------------------------------------------------------------
            | PREVIOUS WEEK CARRY FORWARD
            |--------------------------------------------------------------------------
            */

            $prevNextWeek = $prevWeekHour?->next_week_hours;

            /*
            |--------------------------------------------------------------------------
            | CALCULATE WEEKLY TARGET
            |--------------------------------------------------------------------------
            |
            | HOURLY EMPLOYEE:
            |
            | base hours = users.total_hours
            |
            | carry = previous week's next_week_hours
            |
            | carry is capped by users.max_carry_forward
            |
            | final target = base + capped carry
            |
            | Example:
            |
            | base = 40:00
            | previous carry = 05:00
            | max carry = 02:00
            |
            | target = 42:00
            |
            */

            if ($isHourlyEmployee) {

                $baseHours = !empty($hourly_total_hours)
                    ? $hourly_total_hours
                    : ($total_hours->total_hours ?? '40:00:00');

                $carryHours = !empty($prevNextWeek)
                    ? $prevNextWeek
                    : '00:00:00';

                /*
                |--------------------------------------------------------------------------
                | MAX CARRY FORWARD FROM USERS TABLE
                |--------------------------------------------------------------------------
                */

                $maxCarrySeconds = $this->convertHoursToSeconds(
                    $hourly_max_carry_forward
                );

                $carrySeconds = $this->convertTimeToSeconds(
                    $carryHours
                );

                /*
                | Never allow carry to exceed max_carry_forward
                */
                if ($maxCarrySeconds > 0) {
                    $carrySeconds = min(
                        $carrySeconds,
                        $maxCarrySeconds
                    );
                } else {
                    $carrySeconds = 0;
                }

                /*
                |--------------------------------------------------------------------------
                | HOURLY WEEKLY TARGET
                |--------------------------------------------------------------------------
                |
                | The current week's target comes ONLY from users.total_hours.
                | Previous carry is NOT added to the current week's target.
                | Carry is handled when calculating next_week_hours.
                |
                */

                $baseSeconds = $this->convertTimeToSeconds(
                    $baseHours
                );

                $targetSeconds = $baseSeconds;

                $week_total_hour = $this->secondsToTime(
                    $targetSeconds
                );

                Log::info('HOURLY WEEKLY TARGET CALCULATION', [
                    'user_id' => $user_id,
                    'base_hours' => $baseHours,
                    'previous_carry' => $carryHours,
                    'max_carry_forward' => $hourly_max_carry_forward,
                    'used_carry' => $this->secondsToTime($carrySeconds),
                    'weekly_target' => $week_total_hour,
                    'carry_added_to_current_target' => '00:00:00',
                ]);

            } else {

                /*
                |--------------------------------------------------------------------------
                | NORMAL EMPLOYEE LOGIC
                |--------------------------------------------------------------------------
                */

                if (empty($prevNextWeek)) {

                    $week_total_hour =
                        $total_hours->total_hours
                        ?? '40:00:00';

                } else {

                    $week_total_hour =
                        $prevNextWeek;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | CURRENT WEEK WORKING HOURS
            |--------------------------------------------------------------------------
            */

            $var1 = $week_total_hour;
            $var2 = $totalTimeFormatted;

            $targetSeconds = $this->convertTimeToSeconds($var1);
            $workingSeconds = $this->convertTimeToSeconds($var2);

            $comparisonDifferenceSeconds = $targetSeconds - $workingSeconds;

            Log::info('WEEKLY HOURS COMPARISON', [
                'user_id' => $user_id,
                'target_hours' => $var1,
                'actual_working_hours' => $var2,
                'previous_next_week_hours' => $prevNextWeek,
                'is_hourly_employee' => $isHourlyEmployee,
            ]);

            Log::info('BEFORE REMAINING HOURS RESULT', [
                'user_id' => $user_id,
                'target_seconds' => $targetSeconds,
                'actual_seconds' => $workingSeconds,
                'difference_seconds' => abs($comparisonDifferenceSeconds),
            ]);

            /*
            |--------------------------------------------------------------------------
            | REMAINING HOURS
            |--------------------------------------------------------------------------
            */

            $resultSeconds =  $workingSeconds - $targetSeconds;

            $negative = false;

            if ($resultSeconds < 0) {
                $resultSeconds = abs($resultSeconds);
                $negative = true;
            }

            $result = ($negative ? '-' : '+'). $this->secondsToTime($resultSeconds);


            Log::info('CALCULATED REMAINING HOURS', [
                'user_id' => $user_id,
                'remaining_hours' => $result,
            ]);

            /*
            |--------------------------------------------------------------------------
            | DATABASE UPDATE 1
            |--------------------------------------------------------------------------
            */

            if ($debugMode) {

                Log::info(
                    'DEBUG MODE: WeekHour UPDATE SKIPPED',
                    [
                        'user_id' => $user_id,
                        'week_start' => $week_startDate,
                        'week_end' => $week_endDate,
                        'would_update' => [
                            'remaining_hours' => $result,
                            'working_hours' => $var2,
                            'entry_type' => 'c',
                        ],
                    ]
                );

            } else {

                WeekHour::where('user_id', $user_id)
                    ->where(
                        'week_start_date',
                        $week_startDate
                    )
                    ->where(
                        'week_end_date',
                        $week_endDate
                    )
                    ->update([
                        'remaining_hours' => $result,
                        'working_hours' => $var2,
                        'entry_type' => 'c'
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | GET UPDATED WEEK HOUR
            |--------------------------------------------------------------------------
            */

            $WeekHour = WeekHour::where(
                'user_id',
                $user_id
            )
                ->where(
                    'week_start_date',
                    $week_startDate
                )
                ->where(
                    'week_end_date',
                    $week_endDate
                )
                ->first();

            if (!$WeekHour) {

                Log::warning(
                    'WeekHour record not found for carry forward',
                    [
                        'user_id' => $user_id,
                    ]
                );

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | GET CALCULATION VALUES
            |--------------------------------------------------------------------------
            */

            if ($isHourlyEmployee) {

                /*
                | IMPORTANT:
                | For hourly employee the target already contains:
                |
                | users.total_hours + capped previous carry
                |
                */
                $week_total_hour = $week_total_hour;

            } else {

                $week_total_hour =
                    $WeekHour->total_hours
                    ?? $week_total_hour;
            }

            if ($debugMode) {

                $week_remaining_hour = $result;

                $working_hours_for_calculation = $var2;

            } else {

                $week_remaining_hour =
                    $WeekHour->remaining_hours;

                $working_hours_for_calculation =
                    $WeekHour->working_hours;
            }

            Log::info('CARRY FORWARD INPUT VALUES', [
                'user_id' => $user_id,
                'is_hourly_employee' => $isHourlyEmployee,
                'week_total_hours' => $week_total_hour,
                'week_remaining_hours' => $week_remaining_hour,
                'working_hours' => $working_hours_for_calculation,
            ]);

            /*
            |--------------------------------------------------------------------------
            | MAX CARRY FORWARD
            |--------------------------------------------------------------------------
            */

            if ($isHourlyEmployee) {

                /*
                | From users.max_carry_forward
                */
                $maxCarrySeconds =
                    $this->convertHoursToSeconds(
                        $hourly_max_carry_forward
                    );

            } else {

                /*
                | Existing normal employee:
                | maximum 2 hours carry forward
                */
                $maxCarrySeconds = 7200;
            }

            /*
            |--------------------------------------------------------------------------
            | CONVERT WEEK TARGET / REMAINING
            |--------------------------------------------------------------------------
            */

            $weekTotalSeconds =
                $this->convertTimeToSeconds(
                    $week_total_hour
                );

            /*
            | Handle negative remaining hours safely.
            */
            $remainingIsNegative =
                str_starts_with(
                    trim((string) $week_remaining_hour),
                    '-'
                );

            $remainingSeconds =
                $this->convertTimeToSeconds(
                    ltrim(
                        (string) $week_remaining_hour,
                        '-'
                    )
                );

            /*
            |--------------------------------------------------------------------------
            | CALCULATE NEXT WEEK HOURS
            |--------------------------------------------------------------------------
            */

            if ($remainingIsNegative) {

                /*
                |--------------------------------------------------------------------------
                | NEGATIVE HOURS
                |--------------------------------------------------------------------------
                |
                | Employee has worked less than required.
                |
                | Keep existing business logic here, but make the
                | calculation safe for hourly settings.
                |
                */

                if (
                    $remainingSeconds > $maxCarrySeconds &&
                    $user_id == $WeekHour->user_id
                ) {

                    /*
                    | Existing 4-hour adjustment
                    */
                    $add_4_hours = 14400;

                    $workingSeconds =
                        $this->convertTimeToSeconds(
                            $working_hours_for_calculation
                        );

                    $totalWorkingHoursSeconds =
                        $workingSeconds +
                        $add_4_hours;

                    /*
                    |--------------------------------------------------------------------------
                    | LEAVE CREATION
                    |--------------------------------------------------------------------------
                    */

                    $workdate = $week_endDate;

                    if ($debugMode) {

                        Log::warning(
                            'DEBUG MODE: LEAVE CREATION SKIPPED',
                            [
                                'user_id' => $user_id,
                                'leave_date' => $workdate,
                                'leave_status' => 'FH',
                                'leave_reason' =>
                                    'Your Weekly Working Hours is not completed',
                            ]
                        );

                    } else {

                        if ($user_id != '15') {

                            Leave::create([
                                'user_id' => $user_id,
                                'leave_date' => $workdate,
                                'leave_status' => 'FH',
                                'leave_reason' =>
                                    'Your Weekly Working Hours is not completed',
                                'leave_type' => 'CL',
                                'status' => 'Approved'
                            ]);
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CALCULATE NEXT WEEK
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $totalWorkingHoursSeconds >
                        $weekTotalSeconds
                    ) {

                        $difference =
                            $totalWorkingHoursSeconds -
                            $weekTotalSeconds;

                        if ($difference > $maxCarrySeconds) {

                            $resultWeekSeconds =
                                max(
                                    0,
                                    $weekTotalSeconds -
                                    $maxCarrySeconds
                                );

                        } else {

                            $resultWeekSeconds =
                                max(
                                    0,
                                    $weekTotalSeconds -
                                    $difference
                                );
                        }

                    } else {

                        $difference =
                            $weekTotalSeconds -
                            $totalWorkingHoursSeconds;

                        $resultWeekSeconds =
                            $weekTotalSeconds +
                            $difference;
                    }

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | NEGATIVE REMAINING BUT WITHIN LIMIT
                    |--------------------------------------------------------------------------
                    */

                    $resultWeekSeconds =
                        $weekTotalSeconds +
                        $remainingSeconds;
                }

            } else {

                /*
                |--------------------------------------------------------------------------
                | POSITIVE REMAINING HOURS
                |--------------------------------------------------------------------------
                */

                /*
                | Carry forward can never exceed configured limit.
                */
                $carryableSeconds = min(
                    $remainingSeconds,
                    $maxCarrySeconds
                );

                /*
                | Next week target after applying carry.
                */
                $resultWeekSeconds =
                    $weekTotalSeconds -
                    $carryableSeconds;
            }

            /*
            |--------------------------------------------------------------------------
            | SAFETY
            |--------------------------------------------------------------------------
            */

            if ($resultWeekSeconds < 0) {
                $resultWeekSeconds = 0;
            }

            /*
            |--------------------------------------------------------------------------
            | FORMAT NEXT WEEK HOURS
            |--------------------------------------------------------------------------
            */

            $week_result =
                $this->secondsToTime(
                    $resultWeekSeconds
                );

            /*
            |--------------------------------------------------------------------------
            | HOURLY EMPLOYEE:
            | Make sure next_week_hours itself does not exceed
            | max_carry_forward.
            |--------------------------------------------------------------------------
            |
            | This prevents a large carry from accumulating indefinitely.
            |
            */

            if ($isHourlyEmployee && $maxCarrySeconds > 0) {

                /*
                | next_week_hours represents carry.
                | Therefore cap it to configured max carry.
                */
                $week_resultSeconds =
                    $this->convertTimeToSeconds(
                        $week_result
                    );

                $week_resultSeconds =
                    min(
                        $week_resultSeconds,
                        $maxCarrySeconds
                    );

                $week_result =
                    $this->secondsToTime(
                        $week_resultSeconds
                    );
            }

            Log::info('NEXT WEEK HOURS CALCULATED', [
                'user_id' => $user_id,
                'is_hourly_employee' => $isHourlyEmployee,
                'next_week_hours' => $week_result,
                'max_carry_seconds' => $maxCarrySeconds,
            ]);

            /*
            |--------------------------------------------------------------------------
            | DATABASE UPDATE 2
            |--------------------------------------------------------------------------
            */

            if ($debugMode) {

                Log::info(
                    'DEBUG MODE: next_week_hours UPDATE SKIPPED',
                    [
                        'user_id' => $user_id,
                        'week_hour_id' => $WeekHour->id,
                        'would_update' => [
                            'next_week_hours' => $week_result,
                        ],
                    ]
                );

            } else {

                WeekHour::where(
                    'user_id',
                    $user_id
                )
                    ->where(
                        'id',
                        $WeekHour->id
                    )
                    ->update([
                        'next_week_hours' => $week_result
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | FINAL LOG
            |--------------------------------------------------------------------------
            */

            Log::info(
                $debugMode
                    ? 'DEBUG - END USER WEEKLY CALCULATION'
                    : 'CRON - END USER WEEKLY CALCULATION',
                [
                    'user_id' => $user_id,
                    'employee_name' => $employee?->name ?? null,
                    'is_hourly_employee' => $isHourlyEmployee,
                    'weekly_working_hours' => $var2,
                    'target_weekly_hours' => $var1,
                    'remaining_hours' => $result,
                    'calculated_next_week_hours' => $week_result,
                    'database_updated' => !$debugMode,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | END
        |--------------------------------------------------------------------------
        */

        if ($debugMode) {

            Log::info('=================================================');
            Log::info('WEEKLY HOURS DEBUG END');
            Log::info('DATABASE UPDATES WERE SKIPPED');
            Log::info('=================================================');

        } else {

            Log::info('=================================================');
            Log::info('WEEKLY HOURS CRON END');
            Log::info('DATABASE UPDATES WERE PERFORMED');
            Log::info('=================================================');
        }

        $this->info(
            $debugMode
                ? 'Debug completed. No database updates were made.'
                : 'Weekly hours calculated and updated successfully.'
        );

        return Command::SUCCESS;
    }

    /**
     * Convert HH:MM:SS to seconds.
     */
    private function convertTimeToSeconds(?string $time): int
    {
        if (empty($time)) {
            return 0;
        }

        $time = ltrim(trim($time), '-');

        $parts = explode(':', $time);

        $hours = (int) ($parts[0] ?? 0);
        $minutes = (int) ($parts[1] ?? 0);
        $seconds = (int) ($parts[2] ?? 0);

        return ($hours * 3600)
            + ($minutes * 60)
            + $seconds;
    }

    /**
     * Convert decimal hours to seconds.
     *
     * Example:
     * 2    => 7200
     * 2.5  => 9000
     * 1.25 => 4500
     */
    private function convertHoursToSeconds($hours): int
    {
        if ($hours === null || $hours === '') {
            return 0;
        }

        return (int) round(
            ((float) $hours) * 3600
        );
    }

    /**
     * Convert seconds to HH:MM:SS.
     */
    private function secondsToTime(int $seconds): string
    {
        $seconds = max(0, $seconds);

        $hours = floor($seconds / 3600);

        $seconds %= 3600;

        $minutes = floor($seconds / 60);

        $seconds %= 60;

        return sprintf(
            '%02d:%02d:%02d',
            $hours,
            $minutes,
            $seconds
        );
    }

    /**
     * Add two HH:MM:SS values.
     */
    private function addTime(string $time1, string $time2): string
    {
        $seconds =
            $this->convertTimeToSeconds($time1)
            +
            $this->convertTimeToSeconds($time2);

        return $this->secondsToTime($seconds);
    }
}