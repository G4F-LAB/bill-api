<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileKind extends Model
{
    use HasFactory;

    protected $table = 'tipo_arquivo';
    protected $primaryKey = 'id_tipo_arquivo';

    protected $fillable = [
        'categoria_arquivo'
    ];
}
