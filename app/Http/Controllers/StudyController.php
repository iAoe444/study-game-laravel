<?php

namespace App\Http\Controllers;

use App\CompleteTomato;
use App\Study;
use Illuminate\Http\Request;

include_once "Utils\ReqUtils.php";
class StudyController extends Controller
{
    /*
     * 返回用户学习时间前十的排行版
     * @param type = weekly,monthly,daily or null
     * @return 如果是指定类型，如weekly，则返回周排行，如果参数为空的话，则返回三个时间的排行
     * */
    public function ranking(Request $request)
    {
        $req = $request->getContent();
        $req = json_decode($req);

        //如果存在type
        //根据要取得类型，返回日，周，月排行榜
        if(isset($req) && array_key_exists("type", $req)){
            $type = $req->type;
            //先从数据库中搜索获取处理过后的前10榜单
            $ranking = self::getRanking($type);
            return response()->json(['result' => 'success','msg' => $ranking]);
        }else{
            //这里的是没有type的情况,返回所有排行版
            $dailyRanking = self::getRanking('daily');
            $weeklyRanking = self::getRanking('weekly');
            $monthlyRanking = self::getRanking('monthly');
            return response()->json(['result' => 'success','msg' => [
                'dailyRanking'=>$dailyRanking,
                'weeklyRanking'=>$weeklyRanking,
                'monthlyRanking'=>$monthlyRanking
            ]]);
        }
    }

    /*
     * 用户完成一个番茄钟后，记录下来时间和任务详情
     * @param 单任务直接传openId, comment, taskContent, startAt, endAt就行,
     *        如果是多任务的话, 就传多个单任务的信息
     * @return 成功或者失败
     * */
    public function completeTomato(Request $request)
    {
        $req = $request->getContent();
        $req = json_decode($req);

        //查看是否传入的是多任务
        if(isset($req) && array_key_exists("tasks", $req))
        {
            //多任务处理
            $tasks = $req->tasks;
            foreach ($tasks as $task){
                $this->setCompleteTomato($task);
            }
        }elseif (isset($req))
        {
            //单任务处理
            $this->setCompleteTomato($req);
        }
        else
        {
            //如果不存在类型，则返回错误
            return response()->json(['result' => 'fail','msg'=>'lost json']);
        }
        return response()->json(['result' => 'success']);
    }

    //TODO 看看要传什么时间的任务回去
    //TODO 规定传回的时间格式是时间戳还是别的
    /*
     * 获取用户完成的任务
     * 未完成
     * */
    public function getTomato(Request $request)
    {
        $req = $request->getContent();
        $openId = json_decode($req)->openId;
        $completeTomatoes = CompleteTomato::get()
            ->where('open_id','=',$openId);
        $tomatoArr = array();
        foreach ($completeTomatoes as $tomato)
        {
            $tomatoArr[$tomato->complete_id] = [
                'task_content' => $tomato->task_content,
                'comment' => $tomato->comment,
                'startAt' => $tomato->start_at,
                'endAt' => $tomato->end_at
            ];
        }
        return response()->json(['result' => 'success','msg' => $tomatoArr]);
    }


    //COMPLETE 4.19 用户点击完成学习后的功能
    /**
     * 用户完成学习后的功能
     * @param 用户的openId
     * 点击完成学习后，我们通过今日的学习时间，来计算用户金币的添加量，以及用户段位的提升
     * @return 用户今日学习时间，用户金币的增加量
    */
    public function completeStudy(Request $request)
    {
        $req = \ReqUtils::paramIntact($request,['openId']);
        if ($req['result']=='success')
        {
            $req = $req['msg'];
            $openId = $req->openId;
            $userStudy = Study::find($openId);

            //1. 完成一个任务就是添加一个金币
            $completeTask = $userStudy->complete_task;  //获取用户今天完成的任务
            $userStudy->coin += $completeTask;

            //2. 学习25分钟就是升一颗星
            $dailyTime = $userStudy->daily_time;
            $duanWei = intval($userStudy->study_time/60/25);
            $yesterdayDuanWei = intval(($userStudy->study_time-$dailyTime)/60/25);

            //3. 因为用户上传了自己的学习记录，所以设置为true，防止日更新时再次更新数据
            $userStudy->if_upload = true;

            $userStudy->save();
            
            return response()->json([
                'completeTask'=>$completeTask,
                'getCoin'=>$completeTask,
                'todyStudyTime'=>self::ts2hm($dailyTime),
                'duanWei'=>$duanWei,
                'yesterdayDanWei'=>$yesterdayDuanWei
            ]);
        }
        else return response()->json($req);
    }
    //----------------------------------工具方法--------------------------------------------------
    /*
     * 完成一个番茄任务后要做的事情，记录下这次任务，并且累加用户的学习时间
     * */
    private function setCompleteTomato($task)
    {
        $taskContent = $task->taskContent;
        $openId = $task->openId;
        $startAt = $task->startAt;
        $endAt = $task->endAt;
        $comment = $task->comment;

        //1. 往数据库中的tomato表中记录下这次任务
        $completeTomato = CompleteTomato::create([
            'task_content' => $taskContent,
            'comment' => $comment,
            'open_id' => $openId,
            'start_at' => $startAt,
            'end_at' => $endAt
        ]);

        //获取两个时间戳的时间差
        $timediff = $endAt - $startAt;

        //2. 往用户学习时间表中添加时长
        $study = Study::find($openId);
        $study->daily_time += $timediff;
        $study->weekly_time += $timediff;
        $study->monthly_time += $timediff;
        $study->study_time += $timediff;

        //3. 完成一个任务给今日任务总数加1
        $study->complete_task+=1;

        $study->save();
    }

    //时间戳转时钟分钟函数
    public static function ts2hm($timestamp)
    {
        $hour = intval($timestamp/3600);
        $min = intval($timestamp%3600/60);
        return ['hour'=>$hour, 'min'=>$min];
    }

    //获取指定类型得排行的函数
    private static function getRanking($type)
    {
        $ranking = Study::join('user',function ($join){
            $join->on('user.open_id','=','user_study.open_id');
        })->orderBy($type.'_time','desc')
            ->limit(10)
            ->get(['user_name',$type.'_time','avatar_url']);
        $studyTimeArr = array();
        $i = 1;
        //由于数据库中的时间是时间戳类型，所以需要进行预处理
        foreach ($ranking as $studyTime)
        {
            $typeTime = $type.'_time';
            $time = self::ts2hm($studyTime->$typeTime);
            $studyTimeArr[$i++] = [
                'userName' => $studyTime->user_name,
                'avatarUrl' => $studyTime->avatar_url,
                'time'=> $time
            ];
        }
        return $studyTimeArr;
    }
}