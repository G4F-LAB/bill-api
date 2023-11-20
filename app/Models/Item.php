<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'itens';
    protected $primaryKey = 'id_item';
    protected $fillable = [
        'competence',
        'status',
    ];

    public function rules()
    {
        return [            
            'checklist_id' => 'required',
            'file_naming_id' => 'required',
            'file_type_id' => 'required',
            'status' => 'required',
            'competence' => 'required'
        ];
    }

    public function feedback() {
        return[
            'checklist_id.required' => 'O campo do checklist_id é de preenchimento obrigatório.',
            'file_naming_id.required' => 'O campo do id da nomeclatura de arquivo é de preenchimento obrigatório.',
            'file_type_id.required' => 'O campo do id de tipo de arquivo é de preenchimento obrigatório.',
            'status.required' => 'O campo de status é de preenchimento obrigatório.',
            'competence.required' => 'O campo do competencia é de preenchimento obrigatório.'

        ];

    }
    public function checklist() {
        return $this->belongsTo(Item::class,'checklist', 'checklist_id', 'id')->withTimestamps();
    }

    public function file_nomenclatures() {
        return $this->belongsTo(Item::class,'file_nomenclatures', 'file_naming_id', 'id')->withTimestamps();
    }

    public function file_types() {
        return $this->belongsTo(Item::class,'file_types', 'file_type_id', 'id')->withTimestamps();
    }
    
}
