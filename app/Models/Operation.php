<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Operation extends Model
{
    use HasFactory;
    use LogsActivity;
    // use SoftDeletes;

    protected $connection =  'data_G4F';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'reference',
        'name',
    ];
    // protected $fillables = [
    //     'name',
    //     'manager_id',
    //     'reference',
    //     'executive_id'
    // ];
    protected $appends = ['op_initials'];


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('Operation')->logOnly([
            'name',
            'manager_id',
            'reference',
            'executive_id'
        ]);
    }

    protected function opInitials(): Attribute
    {
        $string = $this->name;

        $numbers = array_filter(str_split($this->name), function ($char) {
          return is_numeric($char);
        });

        $initials = 'OP' . implode("", $numbers);


        return new Attribute(
            get: fn () => $initials,
        );
    }


    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function operationContractUsers()
    {
        return $this->hasMany(OperationContractUser::class);
    }


    // public function contract() {
    //     return $this->hasMany(Contract::class,'operation_id','id');
    // }

    // public function executive() {
    //     return $this->belongsTo(Executive::class);
    // }

    // public function collaborators() {
    //     return $this->belongsToMany(Collaborator::class, 'collaborator_operations', 'operation_id', 'collaborator_id')->withTimestamps();
    // }

    public function collaborator() {
        return $this->belongsTo(Collaborator::class, 'manager_id', 'id');
    }

    // public function executives() {
    //     return $this->belongsTo(Executive::class,'executive_id','id');
    // }

    // public function collaborator_operations() {
    //     return $this->belongsToMany(Collaborator::class, 'collaborator_operations', 'operation_id', 'collaborator_id')

    //                                 ->withTimestamps();
    // }

}
