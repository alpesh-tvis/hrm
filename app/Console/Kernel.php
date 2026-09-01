<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected $commands = [
        Commands\BreakCron::class,
        Commands\WorkreportDaily::class,
        Commands\WeeklyCountHours::class,
    ];
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('break:cron')->everyMinute(); // Command run every minute
        
        $schedule->command('work:report-daily')
             ->dailyAt('23:00')
             ->weekdays()
             // ->days([1, 2, 3, 4, 5, 6])
             ->name('work-report-daily-cron'); 
             // Task to run daily at 11:00 PM (23:00) from Monday to Friday

        $schedule->command('weekly:CountHours')
        ->weeklyOn(0, '23:00')
        ->onFailure(function () {
            \Log::error('weekly:CountHours failed at ' . now());
        })
        ->onSuccess(function () {
            \Log::info('weekly:CountHours succeeded at ' . now());
        });

        // $schedule->call(function () {
        //     file_put_contents(
        //         storage_path('logs/cron-test.txt'),
        //         now() . PHP_EOL,
        //         FILE_APPEND
        //     );
        // })->dailyAt('18:30');


        // $schedule->command('work:report-daily')->everyMinute();     
        
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
