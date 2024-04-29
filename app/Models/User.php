<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
// use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

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
    protected $appends = ['name_initials'];

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


    public function getAuthUser()
    {
        if (Auth::user()) {
            // print_r($this->where('taxvat', Auth::user()['employeeid'])->first());exit;
            return $this->where('taxvat', Auth::user()['employeeid'])->first();
        }
    }

    protected function nameInitials(): Attribute {
        if (!is_null($this->name)) {
            preg_match('/(?:\w+\. )?(\w+).*?(\w+)(?: \w+\.)?$/', $this->name, $result);
            if (count($result) >= 3) {
                $initials = strtoupper($result[1][0] . $result[2][0]);
                return new Attribute(
                    get: fn () => $initials,
                );
            }
        }
        return new Attribute(
            get: fn () => null,
        );
    }


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
