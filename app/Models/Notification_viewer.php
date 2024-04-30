<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Notification_viewer extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'notification_viewed';

    protected $fillable = [
        "user_id",
        "notification_id",
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('Notification')->logOnly([
        'notifications'
        ]);
    }
}
