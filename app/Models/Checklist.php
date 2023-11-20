<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checklist extends Model

{
    protected $primaryKey = 'id_checklist';
    protected $fillable = [ 
        'contract',
        'date_checklist',
        'object_contract',
        'shipping_method',
        'obs',
        'accept',
        'sector',
        'signed_by'
    ];

    public function rules(){
        return [
        'contract' => 'required|string',
        'date_checklist' => 'required|date',
        'object_contract' => 'required|string',
        'shipping_method' => 'required|string',
        'obs' => 'string',
        'accept' => 'boolean',
        'sector' => 'required|string',
        'signed_by' => 'string'
        
        ];
    }
    
    public function feedback() {
        return[
            'contract.required' => 'O campo do contrato é de preenchimento obrigatório.',
            'date_checklist.required' => 'O campo do data é de preenchimento obrigatório.',
            'shipping_method.required' => 'O campo do forma de envio é de preenchimento obrigatório.',
            'sector.required' => 'O campo do setor é de preenchimento obrigatório.'

        ];

    }

    public function contratos (){
        return $this->hasMany(Contract::class,'contract');
    }
}
