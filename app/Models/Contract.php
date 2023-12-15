<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Contract extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $primaryKey = 'id';
    protected $hidden = ['pivot'];
    protected $fillable = [
        'client_id',
        'name',
        'contractual_situation',
        'manager_id',
    ];
    

    public function getActivitylogOptions(): LogOptions
    {        
        return LogOptions::defaults()->useLogName('Contract')->logOnly([
            'client_id',
            'name',
            'contractual_situation',
            'manager_id'
        ]);
    }

    public function collaborator() {
        return $this->belongsToMany(Collaborator::class,'collaborator_contracts', 'contract_id', 'collaborator_id')->withTimestamps();
    }

    public function manager() {
        return $this->belongsTo(Operation::class,'manager_id', 'id');
    }

    public function checklist(){
        return $this->hasMany(Checklist::class);
    }

    public function operation() {
        return $this->belongsTo(Operation::class);
    }
}
