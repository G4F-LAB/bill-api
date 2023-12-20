<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Executive extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'manager_id',
        'operations'
    ];

    public function operations(){
        return $this->hasMany(Operation::class);
    }

    public function manager(){
        return $this->hasOne(Collaborator::class);
    }
}
