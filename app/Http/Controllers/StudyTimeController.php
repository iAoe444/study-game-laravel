<?php

namespace App\Http\Controllers;

use App\StudyTime;
use Illuminate\Http\Request;

class StudyTimeController extends Controller
{
    public function ranking(Request $request)
    {
        $type = $request->input('type');
        if(!isset($type)){
            return response()->json(['Error'=>'lost type']);
        }
        $ranking = StudyTime::join('user',function ($join){
                $join->on('user.openid','=','userStudyTime.openid');
            })->orderBy($type.'Time','desc')
            ->limit(10)
            ->get(['username',$type.'Time'])
            ->toJson();
        return $ranking;
    }

    //TODO 完成completeTomato的基本功能
    //TODO 完成多次内容得输入
    public function completeTomato(Request $request)
    {

    }

    public function getTomato(Request $request)
    {

    }
}