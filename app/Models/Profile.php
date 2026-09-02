<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $guarded = [];

    protected $casts = [
        'languages' => 'array',
        'strengths' => 'array',
        'seo' => 'array',
        'is_available' => 'boolean',
    ];

    /** The site has exactly one profile; every page reads it through here. */
    public static function current(): self
    {
        return static::query()->firstOrFail();
    }
}
