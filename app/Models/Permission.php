<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];
    public $timestamps = false;
    protected $primaryKey = 'id_permission';

    public function colaborador() {
        //hasMany: (Nome da classe de modelo, foreign_key, 'local_key')
        return $this->hasMany(Collaborator::class, 'id_permission', 'id_permission');
    }
}
