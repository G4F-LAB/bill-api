<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class NotificationViewed extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'notification_viewed';

    protected $fillable = [
        "notification_viewed",,
        "user_id"
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('NotificationViewed')->logOnly([
        'notification_viewed'
        ]);
    }
}
