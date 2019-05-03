<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\store;
use App\Study;

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
    public function test(Request $request)
    {
        $acccessToken = ToolController::getAccessToken();
        $templateId = 'M9pvZN4zmUUHAkue_cbayofXx1VeQqFeZjSM-m4b50E';

        $users = Study::get()->where('if_upload', 1);        //获取所有今天学习的用户
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
            $user->if_upload = 0;
            $user->save();
        }
    }
}
