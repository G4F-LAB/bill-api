<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract',
        'name',
        'contractual_situation',
        'manager_id',
    ];
    

    public function collaborator() {
        return $this->belongsToMany(Collaborator::class,'collaborator_contracts', 'contract_id', 'collaborator_id')->withTimestamps();
    }

    public function checklist(){
        return $this->hasMany(Checklist::class,'Checklist','id');
    }
}
