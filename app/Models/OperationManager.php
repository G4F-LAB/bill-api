<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class OperationManager extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $connection =  'book';
    protected $primaryKey = 'id';

    protected $table = 'operation_managers';
    // protected $keyType = 'string';
    // public $incrementing = false;
    // protected $appends = ['name_initials'];

    protected $fillable = [
        'operation_id',
        'executive_id',
        'manager_id',
    ];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('OperationManager')->logOnly([
            'operation_id',
            'executive_id',
            'manager_id',
        ]);
    }

    public function operation()
    {
        return $this->hasOne(Operation::class);
    }

    public function manager()
    {
        return $this->hasOne(User::class, 'id','manager_id');
    }
    public function executive()
    {
        return $this->hasOne(User::class, 'id','executive_id');
    }

}
