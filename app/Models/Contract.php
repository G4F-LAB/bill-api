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
        'id_manager',
    ];
    
    protected $primaryKey = 'id_contract';

    public function collaborator() {
        return $this->belongsToMany(Collaborator::class,'collaborator_contracts', 'id_contract', 'id_collaborator')->withTimestamps();
    }

    public function checklist(){
        return $this->hasMany(Checklist::class,'Checklist','id_checklist');
    }
}
