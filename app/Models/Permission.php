<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Permission extends Model
{
    use HasFactory;
    // use LogsActivity;

    protected $fillable = [
        'name',
    ];
    public $timestamps = false;
    public function getActivitylogOptions(): LogOptions
    {        
        return LogOptions::defaults()->useLogName('Contract')->logOnly([
            'name',           
        ]);
    }
    public function colaborador() {
        //hasMany: (Nome da classe de modelo, foreign_key, 'local_key')
        return $this->hasMany(Collaborator::class, 'id', 'permission_id');
    }
}
