<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Operation extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;
    protected $fillables = [
        'name',
        'manager_id',
        'reference'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('Operation')->logOnly([
            'name',
            'manager_id',
            'reference'
        ]);
    }

    public function contract() {
        return $this->hasMany(Contract::class);
    }

    public function executive() {
        return $this->belongsTo(Executive::class);
    }

    public function collaborator() {
        return $this->belongsTo(Collaborator::class,'manager_id','id');
    }
}
