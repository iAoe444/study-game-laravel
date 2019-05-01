<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'user_task';
    protected $primaryKey = 'task_id';

    public $timestamps = true;
    public function getDateFormat()
    {
        return time();    //自定义时间戳
    }
    protected function asDateTime($val)
    {
        return $val;
    }
    protected $fillable = ['task_content', 'open_id'];
}
