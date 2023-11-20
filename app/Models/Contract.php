<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'contrato',
        'name',
        'situacao_contratual',
        'id_gerente',
    ];
    
    protected $primaryKey = 'id_contrato';

    public function collaborator() {
        return $this->belongsToMany(Collaborator::class,'collaborator_contracts', 'id_contrato', 'id_colaborador')->withTimestamps();
    }

    public function checklist(){
        return $this->hasMany(Checklist::class,'Checklist','id_checklist');
    }
}
