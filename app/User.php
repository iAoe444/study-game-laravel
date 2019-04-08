<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table='user';
    protected $primaryKey = 'open_id';

    public $timestamps = true;
    public function getDateFormat()
    {
        return time();
    }
    /**
     * 不可被批量赋值的属性,为空表示都可以被批量赋值
     *
     * @var array
     */
    protected $guarded = [];
}