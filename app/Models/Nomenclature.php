<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nomenclature extends Model
{
    use HasFactory;

    protected $table = 'nomeclatura_arquivo';
    protected $primaryKey = 'id_nomeclatura_arquivo';

    protected $fillable = [
        'nome_arquivo',
        'nomeclatura_padrao_arquivo',
    ];

    public function rules()
    {
        return [
            'id_nomenclatura' => 'required|string',
            'nome_arquivo' => 'required|string',
            'nomeclatura_padrao_arquivo' => 'required|numeric'
        ];
    }

    public function feedback()
    {
        return [
            'id_nomenclatura.required' => 'O ID é obrigatório.',
            'nome_arquivo.required' => 'O campo Nome do arquivo é de preenchimento obrigatório.',
            'nomeclatura_padrao_arquivo.required' => 'O campo Nomenclatura é de preenchimento obrigatório.'
        ];

    }

}
