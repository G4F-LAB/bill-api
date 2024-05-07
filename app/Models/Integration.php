<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Integration extends Model
{
    protected $connection =  'data_G4F';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'description',
        'api_path',
        'api_key',
        'api_secret',
        'api_auth',
        'access_token',
        'refresh_token',
    ];

    // Define relationships if needed
    public function tasks()
    {
        return $this->hasMany(IntegrationTask::class);
    }

    // public function runs()
    // {
    //     return $this->hasManyThrough(IntegrationRun::class, IntegrationTask::class);
    // }
}
