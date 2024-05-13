<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;
use App\Models\Operation;

class Contract extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $connection =  'data_G4F';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $hidden = ['pivot'];



    protected $fillable = [
        'operation_id',
        'name',
        'alias',
        'uuid',
        'status',
        'start_date',
        'end_date',
        'renew_date',
        'renew_limit_date',
    ];


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('Contract')->logOnly([
            'operation_id',
            'name',
            'alias',
            'uuid',
            'status',
            'start_date',
            'end_date',
            'renew_date',
            'renew_limit_date',
        ]);
    }

    // public function collaborators() {
    //     return $this->belongsToMany(Collaborator::class,'operations','manager_id', 'manager_id')->withTimestamps();
    // }

    // public function manager() {
    //     return $this->belongsTo(Operation::class,'manager_id', 'id');
    // }

    public function checklist()
    {
        return $this->hasOne(Checklist::class, 'contract_uuid', 'id')->with(['status', 'itens.file_name.task.integration', 'itens.file_name.type', 'itens.files'])
        ->latest();
    }



    
    public function checklists(){

        return $this->hasMany(Checklist::class, 'contract_uuid', 'id')->with('status');
    }


     // Define relationships
     public function operation()
     {
         return $this->belongsTo(Operation::class);
     }

    public function contractUsers()
    {
        return $this->hasMany(ContractUser::class);
    }

    public function operationContractUsers()
    {
        return $this->hasMany(OperationContractUser::class);
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
