<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Executive extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;
    protected $fillable = [
        'name',
        'manager_id',
        'operations'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('Contract')->logOnly([
            'name',
            'manager_id',
            'operations',
        ]);
    }
    public function operations(){
        return $this->hasMany(Operation::class);
    }

    public function operation(){
        return $this->belongsTo(Collaborator::class,'manager_id', 'id');
    }

    public function manager(){
        return $this->belongsTo(Collaborator::class, 'manager_id', 'id');
    }

    public function contracts(){
        return $this->hasMany(Contract::class,'id','contract_id');
    }

    public function contract()
    {
        return $this->hasMany(Contract::class, 'operation_id', 'id');
    }
}
