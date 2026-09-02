<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    protected $guarded = [];

    protected $casts = [
        'issued_on' => 'date',
        'expires_on' => 'date',
    ];
}
