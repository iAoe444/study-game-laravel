<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class WeChatController extends Controller
{
    public function getOpenId(Request $request)
    {
        $jsCode = $request->input('jsCode');
        $client = new Client();
        $url = sprintf("https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code",
            env('WECHAT_APPID'), env('WECHAT_SECRET'), $jsCode);
        try {
            $res = $client->request('GET', $url, ['timeout' => 1.5]);
            $res = $res->getBody();
            $res = json_decode($res);
            $openid = $res->openid;
        } catch(\Throwable $e) {
            response()->json(['result'=>'error']);
        }
        if (isset($openid))
            return response()->json(['result'=>'success','openid'=>$openid]);
        else
            return response()->json(['result'=>'fail']);
    }
}