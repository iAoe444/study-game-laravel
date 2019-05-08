<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Study extends Model
{
    protected $table='user_study_time';
    protected $primaryKey = 'open_id';
    protected $keyType = 'string';

    public $timestamps = true;
    public function getDateFormat()
    {
        return time();	//自定义时间戳
    }
}