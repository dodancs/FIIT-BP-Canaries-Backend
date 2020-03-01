<?php

namespace App\Models;

use App\Models\UuidModel;

class Canary extends UuidModel
{

    protected $fillable = ['domain', 'site', 'assignee', 'testing', 'data',];
    protected $casts = ['data' => 'array',];
    protected $hidden = ['id',];
}
