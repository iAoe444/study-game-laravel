<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class StudyTime extends Model
{
    protected $table='userStudyTime';
    protected $primaryKey = 'openid';

    public $timestamps = true;
    public function getDateFormat()
    {
        return time();	//自定义时间戳
    }
}