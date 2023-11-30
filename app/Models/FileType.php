<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class FileType extends Model
{
    use HasFactory;
    use LogsActivity;
    
    protected $fillable = [
        "files_category",
    ];

    public function getActivitylogOptions(): LogOptions
    {        
        return LogOptions::defaults()->useLogName('FileType')->logOnly([
        'files_category'        
        ]);
    }
    public $timestamps = false;
}
