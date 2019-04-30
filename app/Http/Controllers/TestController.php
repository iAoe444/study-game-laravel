<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\store;

class TestController extends Controller
{
    //用于上传图片
    public function uploadImg(Request $request)
    {
        $file = $request->file('img');
        if(isset($file))
        {
            $newName = 'img'.time().".".$file->extension();
            $file->move("goods",$newName);
        }else
            return response()->json(['result'=>'fail','msg'=>'Lost File']);
    }

    public function test(Request $request)
    {
        var_dump(env('APP_URL').store::find(4)->img);
    }
}
