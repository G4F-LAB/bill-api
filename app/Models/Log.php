<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{


    protected $table = 'activity_log';

    protected $appends = ['name'];
    public function getNameAttribute()
    {

        return 2;
    }
}
