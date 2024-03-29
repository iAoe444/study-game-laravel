<?php

namespace App\Console;

use App\Http\Controllers\ToolController;
use App\Study;
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
                Study::query()->update(['monthly_time'=>0]);        //月时间
            if(date("D") == "Mon")
                Study::query()->update(['weekly_time'=>0]);         //周时间
            Study::query()->update(['daily_time'=>0]);              //日时间

            //用于清空plan表
            $tasks = Task::get()
                    ->where('plan_classify','everyday');
            foreach($tasks as $task){
                $task->plan_useTime = 0;
                $task->plan_done = 0;
                $task->save();
            }
        })->daily();

        $schedule->call(function (){
            Cache::put('token',ToolController::getToken());
        })->monthly();

        //DONE 5.3到规定时间清空用户的今天完成任务，是否上传，并且传递模板消息
        //TODO 测试学习报告功能
        $schedule->call(function (){
            ToolController::sendReport();
        })->dailyAt('22:00');

        //TODO 定期减低用户的段位
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
