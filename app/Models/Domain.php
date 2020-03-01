<?php

namespace App\Models;

use App\Models\UuidModel;

class Domain extends UuidModel {
    protected $fillable = ['domain'];
    protected $hidden = ['id'];
}
