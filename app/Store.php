<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class store extends Model
{
    protected $table='store';
    protected $primaryKey = 'id';

    //关闭自定义时间戳
    public $timestamps = false;
    protected $guarded = [];
}