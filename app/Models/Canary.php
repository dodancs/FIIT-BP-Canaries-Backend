<?php

namespace App\Models;

use App\Models\UuidModel;

class Canary extends UuidModel {
    protected $fillable = ['domain', 'site', 'assignee', 'testing', 'setup', 'email', 'password', 'data'];
    protected $casts = ['data' => 'array', 'testing' => 'boolean', 'setup' => 'boolean'];
    protected $hidden = ['id'];
}
