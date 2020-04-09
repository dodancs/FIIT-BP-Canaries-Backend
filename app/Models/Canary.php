<?php

namespace App\Models;

use App\Models\UuidModel;
use Tymon\JWTAuth\Facades\JWTAuth;

class Canary extends UuidModel {
    protected $fillable = ['domain', 'site', 'assignee', 'testing', 'setup', 'email', 'password', 'data'];
    protected $casts = ['data' => 'array', 'testing' => 'boolean', 'setup' => 'boolean'];
    protected $hidden = ['id'];

    public static function boot() {
        parent::boot();
        self::updating(function ($model) {
            $me = JWTAuth::user();
            $model->updated_by = $me->uuid;
        });
    }
}
