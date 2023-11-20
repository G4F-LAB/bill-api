<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $table = 'itens';
    protected $primaryKey = 'id_item';
    protected $fillable = [
        'competence',
        'status',
    ];
    public function checklist() {
        return $this->belongsToMany(Item::class,'checklist', 'id_checklist', 'id_checklist')->withTimestamps();
    }

    public function arquivos() {
        return $this->hasMany(Item::class,'files', 'id_file')->withTimestamps();
    }

    
}
