<?php

namespace App\Models;

use App\Enums\ContentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    protected $guarded = [];

    protected $casts = [
        'highlights' => 'array',
        'tech' => 'array',
        'started_on' => 'date',
        'ended_on' => 'date',
        'status' => ContentStatus::class,
    ];

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', ContentStatus::Published);
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('started_on');
    }

    /** "Jul 2025 - Present" — the resume's own date format. */
    public function period(): string
    {
        return $this->started_on->format('M Y').' - '.($this->ended_on?->format('M Y') ?? 'Present');
    }

    public function isCurrent(): bool
    {
        return is_null($this->ended_on);
    }
}
