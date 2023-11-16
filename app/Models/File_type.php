<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File_type extends Model
{
    use HasFactory;

    protected $primaryKey = "id_tipo_arquivo";
    protected $fillable = [
        "categoria_arquivo",
    ];
    public $timestamps = false;
}
