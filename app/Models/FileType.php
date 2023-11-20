<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileType extends Model
{
    use HasFactory;

    protected $primaryKey = "id_file_type";
    protected $fillable = [
        "files_category",
    ];
    public $timestamps = false;
}
