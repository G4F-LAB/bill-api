<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilesItens extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'files_itens';
    use HasFactory;
}
