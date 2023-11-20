<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $table = 'item';
    protected $primaryKey = 'id_item';
    protected $fillable = [
        'competencia',
        'status',
    ];
    public function checklist() {
        return $this->belongsToMany(Item::class,'checklist', 'id_checklist', 'id_checklist')->withTimestamps();
    }

    public function arquivos() {
        return $this->hasMany(Item::class,'arquivos', 'id_arquivo', 'id_arquivo')->withTimestamps();
    }

    
}