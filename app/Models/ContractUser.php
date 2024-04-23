<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractUser extends Model
{
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

    // Define relationships
    public function operationContractUser()
    {
        return $this->belongsTo(OperationContractUser::class);
    }
}
