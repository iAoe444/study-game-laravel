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
}