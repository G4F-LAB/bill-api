<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Notification extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $fillable = [
        "desc_id",
        "notification_cat_id",
        "notification_type_id",
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('Notification')->logOnly([
        'notifications'
        ]);
    }
}
