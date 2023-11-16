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
}
