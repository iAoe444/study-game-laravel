<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\store;
use App\Study;
use App\Task;

include "Utils/getid3/getid3.php";
class TestController extends Controller
{
    //用于上传图片
    public function uploadImg(Request $request)
    {
        $file = $request->file('img');
        if (isset($file)) {
            $newName = 'img' . time() . "." . $file->extension();
            $file->move("goods", $newName);
        } else
            return response()->json(['result' => 'fail', 'msg' => 'Lost File']);
    }

    //用户获取照片的实际地址
    public function getImgAddress(Request $request)
    {
        var_dump(env('APP_URL') . store::find(4)->img);
    }

    //用户发送模板消息
    public function sendMsg(Request $request)
    {
        $openId = 'o8CVp5DVr4Bs8N8ZEPgNA5GUxEB8';
        $formId = '82ac0e82fce24fd9a5716342b29896b1';
        $templateId = 'M9pvZN4zmUUHAkue_cbayofXx1VeQqFeZjSM-m4b50E';
        $data = ['1小时30分钟', '10个任务', '15个任务', '点击进入小程序查看详细报告'];
        $acccessToken = ToolController::getAccessToken();
        var_dump(ToolController::sendTemplateMsg($acccessToken, $openId, $formId, $templateId, $data));
    }

    //批量发模板消息
    public function sendReport(Request $request)
    {
        $acccessToken = ToolController::getAccessToken();
        $templateId = 'M9pvZN4zmUUHAkue_cbayofXx1VeQqFeZjSM-m4b50E';

        $users = Study::get()->where('study', 1);        //获取所有今天学习的用户
        foreach ($users as $user) {
            //获取需要发送的数据
            $openId = $user->open_id;
            $formId = $user->report_form_id;
            //--------------------1.获取用户的学习时间-----------------------
            $studyTime = StudyController::ts2hm($user->daily_time);
            $studyTime = $studyTime['hour'].'小时'.$studyTime['min'].'分钟';

            //---------------------2.完成任务情况-----------------------------
            //获取今天凌晨0点的时间
            $today = strtotime(date('Y-m-d', time()));
            //今日完成的任务数量
            $complete = Task::get()
                ->where('open_id', $openId)
                ->where('if_complete', 1)
                ->where('updated_at', '>', $today)
                ->where('updated_at', '<=', $today + 3600 * 24)
                ->count();
            //未完成的任务数量
            $uncomplete = Task::get()->where('open_id', $openId)->where('if_complete', 0)->count();

            //-----------------------3.发送模板消息--------------------------------
            //创建data
            $data = [$studyTime, $complete.'个任务', ($complete+$uncomplete).'个任务', '点击进入小程序查看详细报告'];
            ToolController::sendTemplateMsg($acccessToken, $openId, $formId, $templateId, $data);

            //4.--------------------4.设置用户为未学习-----------------------------
            //用户设置为今天未学习
            $user->if_study = 0;
            $user->save();
        }
    }

    //传输音频
    public function sendAudio(Request $request)
    {
        return response()->file('audio\kokyu.m4a');
    }

    //剪辑音频
    public function cutAudio(Request $request)
    {
        $time = 25*60;
        $audioNum = 88;
        $audioList = "";
        $audioArr = "";

        //获取音乐列表
        $getID3 = new \getID3();
        $audio = array();    //TODO 用于记录选择个歌曲，不重复
        $totalTime = 0;
        $i = 0;
        while($totalTime<$time)
        {
            $chooseAudio = mt_rand(1,$audioNum);
            $fileUrl = "audio\\".$chooseAudio.".mp3";
            //为command做准备
            $audioList .= " -i D:/PHP_WorkSpace/laravel/public/audio/".$chooseAudio.".mp3";
            $audioArr .= "[".$i.":0] ";

            $audio[$i++] = $fileUrl;

            $totalTime += $getID3->analyze($fileUrl)['playtime_seconds'];
        }
        //获取最后一个音频要剪的位置
        $cutTime = $getID3->analyze($fileUrl)['playtime_seconds']-($totalTime-$time);
        //把它转为分钟：秒数的形式
        $cutTime = gmdate('H:i:s',$cutTime);

        //合并音频
        $command = "ffmpeg".$audioList." -filter_complex '".$audioArr."concat=n=".$i.":v=0:a=1 [a]' -map [a] 123.mp3";

        var_dump($command);

        // return response()->file('audio\123.mp3');
    }

    //修改字段
    public function edit(Request $request)
    {
        $tasks = Task::get()
            ->where('plan_classify','everyday');
        foreach($tasks as $task){
            $task->plan_useTime = 0;
            $task->plan_done = 0;
            $task->save();
        }
    }

    public function test(Request $request)
    {
        ToolController::sendReport();
    }
}
