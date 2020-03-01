<?php

namespace App\Models;

use App\Models\UuidModel;

class Site extends UuidModel
{

    protected $fillable = ['site',];
    protected $hidden = ['id',];
}
