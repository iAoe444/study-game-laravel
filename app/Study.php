<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Study extends Model
{
    protected $table='user_study';
    protected $primaryKey = 'open_id';

    public $timestamps = true;
    public function getDateFormat()
    {
        return time();	//自定义时间戳
    }
}