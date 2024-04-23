<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusContract extends Model
{
    use HasFactory;

    protected $table = 'status_contract';

    // public function contract() {
    //     return $this->hasMany(Contract::class);
    // }
}
