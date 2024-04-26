<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Str;

class Contract extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $connection =  'data_G4F';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $hidden = ['pivot'];


    protected $appends = ['checklist_current'];

    protected $fillable = [
        'client_id',
        'name',
        'contractual_situation',
        'manager_id',
        'status_id',
        'alias'
    ];


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('Contract')->logOnly([
            'client_id',
            'name',
            'contractual_situation',
            'manager_id',
            'status_id',
            'alias'
        ]);
    }

    // public function collaborators() {
    //     return $this->belongsToMany(Collaborator::class,'operations','manager_id', 'manager_id')->withTimestamps();
    // }

    // public function manager() {
    //     return $this->belongsTo(Operation::class,'manager_id', 'id');
    // }

    // public function checklist(){
    //     return $this->hasMany(Checklist::class);
    // }


    // public function operation() {
    //     return $this->belongsTo(Operation::class);
    // }


    //current checklist
    protected function checklistCurrent(): Attribute
    {
        $initials =  [];



        if (!Str::isUuid($this->id)) {
            $current_checklist = [];

        }else{
            $current_checklist = Checklist::where('contract_uuid', $this->id)->with('itens.fileNaming')->latest()->first();
        }


        return new Attribute(
            get: fn () => $current_checklist,
        );
    }


    public function checklists(){

        return $this->hasMany(Checklist::class, 'contract_uuid', 'id');
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
