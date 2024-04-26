<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
// use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $connection =  'data_G4F';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'username',
        'email',
        'email_corporate',
        'taxvat',
        'phone',
        'status',
        'type',
        'password',
    ];

    
    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()->useLogName('User')->logOnly([
    //         'name',
    //         'email',
    //         'username',
    //     ]);
    // }




    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Define relationships
    public function files()
    {
        return $this->hasMany(UserFile::class);
    }

    public function integrationIds()
    {
        return $this->hasMany(UserIntegrationId::class);
    }

    public function operationContractUsers()
    {
        return $this->hasMany(OperationContractUser::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = Str::uuid();
            }
        });
    }
}
