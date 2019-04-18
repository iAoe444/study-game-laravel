<?php
    //判断时间的日星期
//    if(date("d") == "01")
//    var_dump("日");
//    if(date("D") == "Mon")
//    var_dump("月");

    //获取时间戳之间的分钟数
//    $startAt = "1555557684";    //2019/4/18 11:21:24
//    $endAt = "1555557820";      //2019/4/18 11:23:40
//
//
//    $timediff = $endAt-$startAt;
//    var_dump(intval($timediff/60));

/*
 function request_post() {
    $url = 'https://aip.baidubce.com/oauth/2.0/token';
    $post_data['grant_type']       = 'client_credentials';
    $post_data['client_id']      = 'IK2PyA6XiMlBHmRuOrzRb71j';
    $post_data['client_secret'] = '9s9pEuhOpKH8m9ZNA9uTGMsXa3WFjU8E';
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

    return $data;
}

	$url = 'https://aip.baidubce.com/oauth/2.0/token';
    $post_data['grant_type']       = 'client_credentials';
    $post_data['client_id']      = 'IK2PyA6XiMlBHmRuOrzRb71j';
    $post_data['client_secret'] = '9s9pEuhOpKH8m9ZNA9uTGMsXa3WFjU8E';
    $o = "";
    foreach ( $post_data as $k => $v )
    {
        $o.= "$k=" . urlencode( $v ). "&" ;
    }
    $post_data = substr($o,0,-1);

    $res = request_post($url, $post_data);

    var_dump($res);

*/

/**
 * 发起http post请求(REST API), 并获取REST请求的结果
 * @param string $url
 * @param string $param
 * @return - http response body if succeeds, else false.
 */
function request_post($url = '', $param = '')
{
    if (empty($url) || empty($param)) {
        return false;
    }

    $postUrl = $url;
    $curlPost = $param;
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

    return $data;
}

$token = '24.608b23f0bb36a1ec24467a54cbede0fa.2592000.1558164429.282335-16051361';
$url = 'https://aip.baidubce.com/rest/2.0/ocr/v1/handwriting?access_token=' . $token;
$img = file_get_contents("C:\Users\iAoe\Desktop\Snipaste_2019-04-18_15-45-30.png");
$img = base64_encode($img);
$bodys = array(
    "image" => $img,
);
$res = request_post($url, $bodys);

var_dump($res);