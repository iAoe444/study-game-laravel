<?php

namespace App\Console;

use App\Http\Controllers\ToolController;
use App\StudyTime;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//         $schedule->command('inspire')
//                  ->hourly();
        $schedule->call(function () {
            //这个用于清空用户学习时间
            if(date("d") == "01")
                StudyTime::query()->update(['monthly_time'=>0]);        //月时间
            if(date("D") == "Mon")
                StudyTime::query()->update(['weekly_time'=>0]);         //周时间
            StudyTime::query()->update(['daily_time'=>0]);              //日时间
        })->daily();

        $schedule->call(function (){
            Cache::put('token',ToolController::getToken());
        })->monthly();
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
