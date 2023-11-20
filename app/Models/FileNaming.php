<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileNaming extends Model
{
    use HasFactory;

    protected $table = 'file_naming';
    protected $primaryKey = 'id_file_naming';

    protected $fillable = [
        'file_name',
        'standard_file_naming',
    ];

    public function rules()
    {
        return [
            'id_file_naming' => 'required|string',
            'file_name' => 'required|string',
            'standard_file_naming' => 'required|numeric'
        ];
    }

    public function feedback()
    {
        return [
            'id_file_naming.required' => 'O ID é obrigatório.',
            'file_name.required' => 'O campo Nome do arquivo é de preenchimento obrigatório.',
            'standard_file_naming.required' => 'O campo Nomenclatura é de preenchimento obrigatório.'
        ];

    }

}
