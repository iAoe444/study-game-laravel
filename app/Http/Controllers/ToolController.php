<?php
namespace App\Http\Controllers;

use App\Study;
use App\Task;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class ToolController extends Controller
{
    /*
     * 获取openId
     * */
    public static function getOpenId(Request $request)
    {
        $jsCode = $request->input('jsCode');
        $client = new Client();
        $url = sprintf(
            "https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code",
            env('WECHAT_APPID'),
            env('WECHAT_SECRET'),
            $jsCode
        );
        try {
            $req = $client->request('GET', $url, ['timeout' => 1.5]);
            $req = $req->getBody();
            $req = json_decode($req);
            $openId = $req->openid;
        } catch (\Throwable $e) {
            response()->json(['result' => 'error']);
        }
        if (isset($openId))
            return response()->json(['result' => 'success', 'openId' => $openId]);
        else
            return response()->json(['result' => 'fail']);
    }

    //DONE 制作模板
    //TODO 精简化信息
    //TODO 将信息记录到任务中
    public function getText(Request $request)
    {
        $file = $request->file('picture');
        if (isset($file)) {
            var_dump(self::img2text($file));
        } else
            return response()->json(['result' => 'fail', 'msg' => 'Lost File']);
    }

    /**
     * 设置用于学习报告的FormId
     * @Param OpenId
     * @Param formId
     */
    public function saveReportFormId(Request $request)
    {
        $openId = $request->input('openId');
        $formId = $request->input('formId');
        if ($openId && $formId) {
            $userStudy = Study::find($openId);
            if ($userStudy) {
                $userStudy->report_form_id = $formId;
                if ($userStudy->save())
                    return response()->json(['result' => 'success', 'msg' => 'success']);
                else
                    return response()->json(['result' => 'fail', 'msg' => 'save error']);
            } else
                return response()->json(['result' => 'fail', 'msg' => 'error openId']);
        } else
            return response()->json(['result' => 'fail', 'msg' => 'Lost param']);
    }

    public function musicOn()
    {
        $fileUrl = 'audio/audio' . mt_rand(1, 10) . '.mp3';
        return response()->file($fileUrl);
    }
    public function musicOff()
    {
        $fileUrl = 'audio/music_off.mp3';
        return response()->file($fileUrl);
    }

    //------------------------------------工具方法------------------------------------------------
    private function img2text($img)
    {
        $token = Cache::get('token');
        $url = 'https://aip.baidubce.com/rest/2.0/ocr/v1/handwriting?access_token=' . $token;
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

    //获取百度的token
    public static function getToken()
    {
        $url = 'https://aip.baidubce.com/oauth/2.0/token';
        $post_data['grant_type']    = 'client_credentials';
        $post_data['client_id']     = env('BD_CLIENT_ID');
        $post_data['client_secret'] = env('BD_CLIENT_SECRET');
        $o = "";
        foreach ($post_data as $k => $v) {
            $o .= "$k=" . urlencode($v) . "&";
        }
        $post_data = substr($o, 0, -1);

        $postUrl = $url;
        $curlPost = $post_data;

        $curl = curl_init(); //初始化curl
        curl_setopt($curl, CURLOPT_URL, $postUrl); //抓取指定网页
        curl_setopt($curl, CURLOPT_HEADER, 0); //设置header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
        curl_setopt($curl, CURLOPT_POST, 1); //post提交方式
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($curl); //运行curl
        curl_close($curl);
        $data = json_decode($data);

        return $data->access_token;
    }

    /**
     * 获取微信的AccessToken
     */
    public static function getAccessToken()
    {
        $appid = env('WECHAT_APPID');
        $secret = env('WECHAT_SECRET');
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";

        //初始化
        $curl = curl_init();
        //设置抓取的URL
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0); //设置header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
        $data = curl_exec($curl); //运行curl
        curl_close($curl);
        $data = json_decode($data);
        return $data->access_token;
    }

    /**
     * 发送模板消息
     */
    public static function sendTemplateMsg($accessToken, $openId, $formId, $template_id, $data = NULL, $page = 'pages/index/index')
    {

        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token={$accessToken}";

        $curl = curl_init($url); //初始化curl

        $keyWord = array();
        $i = 1;
        foreach ($data as $value)
            $keyWord['keyword' . $i++] = ['value' => $value];

        $postData = array(
            'touser' => $openId,
            'template_id' => $template_id,
            'form_id' => $formId,
            'data' => $keyWord,
            'page' => $page,
            'emphasis_keyword' => 'keyword1.DATA'
        );
        $postData = json_encode($postData);

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($postData)
            )
        );
        $data = curl_exec($curl);

        curl_close($curl);
        $data = json_decode($data);
        return $data;
    }

    /**
     * 批量向用户发送学习报告
     */
    public static function sendReport()
    {
        $acccessToken = self::getAccessToken();
        $templateId = 'M9pvZN4zmUUHAkue_cbayofXx1VeQqFeZjSM-m4b50E';

        $users = Study::get()->where('if_study', 1);        //获取所有今天学习的用户
        foreach ($users as $user) {
            //获取需要发送的数据
            $openId = $user->open_id;
            $formId = $user->report_form_id;
            //--------------------1.获取用户的学习时间-----------------------
            $studyTime = StudyController::ts2hm($user->daily_time);
            $studyTime = $studyTime['hour'] . '小时' . $studyTime['min'] . '分钟';

            //-------------2.完成任务情况-------------
            //获取今天凌晨0点的时间
            $today = strtotime(date('Y-m-d', time()));
            //今日完成的任务数量
            $complete = Task::get()
                ->where('openid', $openId)
                ->where('plan_done', 1)
                ->where('update_at', '>', $today)
                ->where('update_at', '<=', $today + 3600 * 24)
                ->count();
            //未完成的任务数量
            $uncomplete = Task::get()->where('openid', $openId)->where('plan_done', 0)->count();

            //-----------------------3.发送模板消息--------------------------------
            //创建data
            $data = [$studyTime, $complete . '个任务', ($complete + $uncomplete) . '个任务', '点击进入小程序查看详细报告'];
            self::sendTemplateMsg($acccessToken, $openId, $formId, $templateId, $data);

            //4.--------------------4.设置用户为未学习-----------------------------
            //用户设置为今天未学习
            $user->if_study = 0;
            $user->save();
        }
    }
}
