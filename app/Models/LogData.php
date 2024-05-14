<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class LogData extends Model
{


    protected $connection =  'data_G4F';
    protected $table = 'activity_log';
}
