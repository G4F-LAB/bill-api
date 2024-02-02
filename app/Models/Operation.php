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
    use SoftDeletes;
    protected $fillables = [
        'name',
        'manager_id',
        'reference',
        'executive_id'
    ];
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
        preg_match('/(?:\w+\. )?(\w+).*?(\w+)(?: \w+\.)?$/', $this->name, $result);
        $initials =  strtoupper($result[1][0] . $result[1][1] . $result[2][0]);

        return new Attribute(
            get: fn () => $initials,
        );
    }

    public function contract() {
        return $this->hasMany(Contract::class,'operation_id','id');
    }

    public function executive() {
        return $this->belongsTo(Executive::class);
    } 

    public function collaborator() {
        return $this->belongsTo(Collaborator::class,'manager_id','id');
    }


}
