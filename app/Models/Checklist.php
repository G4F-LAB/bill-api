<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checklist extends Model

{
    protected $primaryKey = 'id_checklist';
    protected $fillable = [ 
        'id_contrato',
        'data_checklist',
        'objeto_contrato',
        'forma_envio',
        'obs',
        'aceite',
        'setor',
        'assinado_por'
    ];

    public function rules(){
        return [
        'id_contrato' => 'required|string',
        'data_checklist' => 'required|date',
        'objeto_contrato' => 'required|string',
        'forma_envio' => 'required|string',
        'obs' => 'string',
        'aceite' => 'boolean',
        'setor' => 'required|string',
        'assinado_por' => 'string'
        
        ];
    }
    
    public function feedback() {
        return[
            'id_contrato.required' => 'O campo do contrato é de preenchimento obrigatório.',
            'data_checklist.required' => 'O campo do data é de preenchimento obrigatório.',
            'forma_envio.required' => 'O campo do forma de envio é de preenchimento obrigatório.',
            'setor.required' => 'O campo do setor é de preenchimento obrigatório.'

        ];

    }
}
