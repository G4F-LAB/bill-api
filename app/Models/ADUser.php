<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use LdapRecord\Models\ActiveDirectory\User;
use Tymon\JWTAuth\Contracts\JWTSubject;

class ADUser extends User implements JWTSubject
{
    use HasFactory;

    // protected $casts = [
    //     'employeeid' => 'array',
    // ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getConvertedGuid();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
