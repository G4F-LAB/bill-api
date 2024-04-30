<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class FileCompetence extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        "competence",
    ];
    public function getActivitylogOptions(): LogOptions
    {        
        return LogOptions::defaults()->useLogName('Contract')->logOnly([
            'competence',       
        ]);
    }
}
