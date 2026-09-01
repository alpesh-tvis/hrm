<?php

namespace App\Console\Commands;

use App\Notifications\BreakNotification;
use Illuminate\Console\Command;
use App\Models\WorkReport;
use App\Models\User;
use DateTime;

class BreakCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'break:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today_date = date('Y-m-d');
        $date = new DateTime;
        $date->modify('-5 minutes');
        $formatted_date = $date->format('Y-m-d H:i:s');
        $activity_type_id = [2, 3, 4, 5, 6];
        $get_break = WorkReport::where('work_type','=','3')->where('work_time',null)->where('work_date',$today_date)->where('created_at','<=',$formatted_date)->whereIn('activity_type',$activity_type_id)->get();
        
        //\Log::info($get_break);

        foreach ($get_break as $break) {
            $user_id =  $break->user_id;   
            $user = User::find($user_id);

            // Send the browser notification
            $user->notify(new BreakNotification($user));
        }
    }
}
