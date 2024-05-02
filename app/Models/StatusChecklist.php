<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusChecklist extends Model
{
    use HasFactory;

    protected $table = 'status_checklist';
    protected $primaryKey = 'id';

    public function checklist() {
        return $this->belongsTo(Checklist::class);
    }
}
