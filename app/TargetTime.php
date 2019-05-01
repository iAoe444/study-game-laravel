<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TargetTime extends Model
{
    protected $table='target_time';
    protected $primaryKey = 'id';

    public $timestamps = false;
}
