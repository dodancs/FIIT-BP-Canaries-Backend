<?php

namespace App\Models;

use App\Models\UuidModel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends UuidModel implements AuthenticatableContract, AuthorizableContract, JWTSubject {
    use Authenticatable, Authorizable;

    protected $fillable = ['username', 'password'];
    protected $casts = ['permissions' => 'array'];
    protected $hidden = ['password', 'id', 'remember_token'];

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }
}
