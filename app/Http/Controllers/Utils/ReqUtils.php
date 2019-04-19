<?php

use Illuminate\Http\Request;

//----------------------------------工具方法--------------------------------------
class ReqUtils
{
    /**
     * 检测参数是否完整的方法
     * @param Request   将request传过来就行
     * @param array     需要验证的参数名字的数组
     * @return array    返回结果成功或失败，成功会返回需要的信息
     */
    public static function paramIntact(Request $request,$params)
    {
        $req = json_decode($request->getContent());
        //判断是否为json
        if ($req && (is_object($req)) || (is_array($req) && !empty(current($req)))) {
            foreach ($params as $param)
            {
                //判断数组中的值是否都存在
                if(!array_key_exists($param, $req))
                {
                    return ['result'=>'fail',',msg'=>'lost param'];
                }
            }
            return ['result'=>'success','msg'=>$req];
        }
        return ['result'=>'fail','msg'=>'is not json'];
    }
}