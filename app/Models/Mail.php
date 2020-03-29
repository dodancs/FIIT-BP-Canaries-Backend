<?php

namespace App\Models;

use App\Models\UuidModel;

class Mail extends UuidModel {
    protected $guarded = ['uuid', 'canary', 'received_on', 'from', 'subject', 'body'];
    protected $hidden = ['id'];

    protected $table = 'mail';
}
