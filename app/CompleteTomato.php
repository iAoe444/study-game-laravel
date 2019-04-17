<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class CompleteTomato extends Model
{
    protected $table='user_complete_tomato';
    protected $primaryKey = 'complete_id';

    //关闭自定义时间戳
    public $timestamps = false;
}