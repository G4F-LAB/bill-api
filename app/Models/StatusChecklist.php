<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusChecklist extends Model
{
    use HasFactory;

    public function checklist() {
        return $this->hasMany(Checklist::class);
    }
}
