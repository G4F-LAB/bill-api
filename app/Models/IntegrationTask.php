<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationTask extends Model
{
    protected $connection =  'data_G4F';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'integration_id',
        'name',
        'reference',
        'cron_time',
        'description',
        'status',
    ];

    // Define relationships
    public function integration()
    {
        return $this->belongsTo(Integration::class);
    }

    public function file_names()
    {
        return $this->hasMany(FileName::class);
    }
}
