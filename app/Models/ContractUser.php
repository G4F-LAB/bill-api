<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ContractUser extends Model
{
    use LogsActivity;
    protected $connection =  'data_G4F';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'operation_contract_user_id',
        'work_shift',
        'hire_date',
        'dismissal_date',
        'position_id',
        'position_name',
        'department_id',
        'department_name',
        'department_code',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('ContractUser')->logOnly([
            'operation_contract_user_id',
            'work_shift',
            'hire_date',
            'dismissal_date',
            'position_id',
            'position_name',
            'department_id',
            'department_name',
            'department_code',
        ]);
    }

    // Define relationships
    public function operationContractUser()
    {
        return $this->belongsTo(OperationContractUser::class);
    }
}
