<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ToolController extends Controller
{
    /*
     * 获取openId
     * */
    public static function getOpenId(Request $request)
    {
        $req = $request->getContent();
        $jsCode = json_decode($req)->jsCode;
        $client = new Client();
        $url = sprintf("https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code",
            env('WECHAT_APPID'), env('WECHAT_SECRET'), $jsCode);
        try {
            $req = $client->request('GET', $url, ['timeout' => 1.5]);
            $req = $req->getBody();
            $req = json_decode($req);
            $openId = $req->openid;
        } catch(\Throwable $e) {
            response()->json(['result'=>'error']);
        }
        if (isset($openId))
            return response()->json(['result'=>'success','openId'=>$openId]);
        else
            return response()->json(['result'=>'fail']);
    }

    //TODO 制作模板
    //TODO 精简化信息
    //TODO 将信息记录到任务中
    public function getText(Request $request)
    {
        $file = $request->file('picture');
        if(isset($file))
        {
            var_dump(self::img2text($file));
        }else
            return response()->json(['result'=>'fail','msg'=>'Lost File']);
    }

    //------------------------------------工具方法------------------------------------------------
    private function img2text($img)
    {
        $token = Cache::get('token');
        $url = 'https://aip.baidubce.com/rest/2.0/ocr/v1/handwriting?access_token='.$token;
        $img = file_get_contents($img);
        $img = base64_encode($img);
        $bodys = array(
            "image" => $img,
        );
        $postUrl = $url;
        $curlPost = $bodys;
        // 初始化curl
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $postUrl);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        // 要求结果为字符串且输出到屏幕上
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        // post提交方式
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        // 运行curl
        $data = curl_exec($curl);
        curl_close($curl);

        $data = json_decode($data);

        return $data;
    }

    //获取token
    public static function getToken()
    {
        $url = 'https://aip.baidubce.com/oauth/2.0/token';
        $post_data['grant_type']    = 'client_credentials';
        $post_data['client_id']     = env('BD_CLIENT_ID');
        $post_data['client_secret'] = env('BD_CLIENT_SECRET');
        $o = "";
        foreach ( $post_data as $k => $v )
        {
            $o.= "$k=" . urlencode( $v ). "&" ;
        }
        $post_data = substr($o,0,-1);

        $postUrl = $url;
        $curlPost = $post_data;

        $curl = curl_init();//初始化curl
        curl_setopt($curl, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($curl, CURLOPT_HEADER, 0);//设置header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($curl);//运行curl
        curl_close($curl);
        $data = json_decode($data);

        return $data->access_token;
    }
}