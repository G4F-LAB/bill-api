<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $table = 'arquivos';
    protected $primaryKey = 'id_arquivo';

    protected $fillable = [
        'nome_complementar'
    ];
}
