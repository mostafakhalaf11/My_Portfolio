<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationEntry extends Model
{
    protected $guarded = [];

    protected $casts = [
        'started_on' => 'date',
        'ended_on' => 'date',
    ];
}
