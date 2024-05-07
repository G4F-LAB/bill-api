<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Str;
class OperationContractUser extends Model
{
    use LogsActivity;
    protected $connection =  'data_G4F';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'operation_id',
        'contract_id',
        'user_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('OperationContractUser')->logOnly([
            'operation_id',
            'contract_id',
            'user_id',
        ]);
    }

    // Define relationships
    public function operation()
    {
        return $this->belongsTo(Operation::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contractUser()
    {
        return $this->hasOne(ContractUser::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = Str::uuid(); // Generate UUID if not already set
            }
        });
    }
}
