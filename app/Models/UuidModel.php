<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class UuidModel extends Model {
    public $incrementing = false;
    public $primaryKey = 'uuid';

    public static function boot() {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = Uuid::uuid4()->toString();
        });
    }
}
