<?php

namespace App\Http\Controllers;

use App\Study;
use App\User;
use Illuminate\Http\Request;
use App\TargetTime;

class UserController extends Controller
{
    /*
     * 判断用户是否存在
     * $parm jsCode
     * */
    public function userOrCreate(Request $request)
    {
        // //先获取openid
        $res = json_decode(ToolController::getOpenId($request)->getContent());

        // //这里用于测试openId
        // // $openId = 't'.rand(1000,9999);

        if (isset($res->openId)) {
            $openId = $res->openId;
            $user = User::find($openId);
            //如果存在openid，则继续，如果不存在，则创建一个新用户
            if (!isset($user)) {
                $num = User::count() + 10000;
                //1. 创建用户表
                $user = new User();
                $user->open_id = $openId;
                $user->user_name = '微信用户' . $num;

                //2. 创建用户学习表
                $study = new Study();
                $study->open_id = $openId;

                $user->save();
                $study->save();

                //创建成功返回创建成功，并返回openid
                return response()->json(['result' => 'success', 'msg' => ['openid' => $openId]]);
            } else
                //已经存在返回exist
                return response()->json(['result' => 'exist', 'msg' => ['openid' => $openId]]);
        } else
            return response()->json(['result' => 'fail', 'msg' => 'Error jsCode']);
    }

    /*
     * 更新用户信息
     * $parm openid(必须)，userinfo或target或slogan
     * */
    public function updateUser(Request $request)
    {
        try {
            $openId = $request->input('openId');
            //检查是否有传入openId
            if ($openId) {
                $updateItem = '';
                $user = User::find($openId);
                //用户不存在就返回用户不存在
                if (!isset($user)) {
                    return response()->json(['result' => 'fail', 'msg' => 'user not exits']);
                }

                $userInfo = $request->input('userInfo');
                $slogan = $request->input('slogan');
                $target = $request->input('target');
                $targetTime = $request->input('targetTime');

                //导入用户详情
                if ($userInfo) {
                    $user->user_name = $userInfo['nickName'];
                    $user->avatar_url = $userInfo['avatarUrl'];
                    $user->gender = $userInfo['gender'];
                    $user->province = $userInfo['province'];
                    $user->city = $userInfo['city'];
                    $user->country = $userInfo['country'];
                    $updateItem .= 'userInfo ';
                }
                //导入口号
                if ($slogan) {
                    $user->slogan = $slogan;
                    $updateItem .= 'slogan ';
                }
                //导入目标
                if (isset($target)) {
                    $user->target = $target;
                    //导入目标时间
                    //如果该用户有自定义目标时间，则直接设置目标时间
                    if (isset($targetTime)) {
                        $targetTime = strtotime($targetTime[0].'-'.$targetTime[1].'-'.$targetTime[2]);
                        $user->target_time = $targetTime;
                    }
                    $updateItem .= 'target targetTime';
                }
                $user->save();
                return response()->json(['result' => 'success', 'msg' => ['updateItem:' => $updateItem]]);
            } else
                return response()->json(['result' => 'fail', 'msg' => 'lost openid']);
        } catch (Exception $ex) {
            return response()->json(['result' => 'fail', 'msg' => $ex->getMessage()]);
        }
    }
}
