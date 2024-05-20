<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;  


class LogData extends Activity
{


    protected $connection =  'data_G4F';
    protected $table = 'activity_log';
}
