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

    /**
     * `resume_path` is relative to public/. The download button only appears once the
     * PDF is actually there, so a configured path can never render a dead link.
     */
    public function resumeUrl(): ?string
    {
        if (blank($this->resume_path) || ! is_file(public_path($this->resume_path))) {
            return null;
        }

        return asset($this->resume_path);
    }

    /** `avatar_path` is relative to public/; null until the image exists, so no broken photo. */
    public function avatarUrl(): ?string
    {
        if (blank($this->avatar_path) || ! is_file(public_path($this->avatar_path))) {
            return null;
        }

        return asset($this->avatar_path);
    }
}
