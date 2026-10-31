<?php

namespace App\Models;

use App\Enums\ContentStatus;
use App\Enums\ProjectStage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CaseStudy extends Model
{
    protected $guarded = [];

    protected $casts = [
        'options_rejected' => 'array',
        'outcomes' => 'array',
        'architecture_diagram' => 'array',
        'tech' => 'array',
        'stage' => ProjectStage::class,
        'roadmap' => 'array',
        'is_featured' => 'boolean',
        'status' => ContentStatus::class,
        'published_at' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Published means both flagged and past its publish time.
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', ContentStatus::Published)
            ->where(fn (Builder $q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('is_featured')->orderBy('sort_order')->orderByDesc('published_at');
    }

    /** "2025–present", "2024–2025" or "2026": the span shown beside the stage badge. */
    public function yearsLabel(): string
    {
        return trim(($this->year_from ?? '').($this->year_to ? '–'.$this->year_to : ($this->year_from ? '–present' : '')));
    }

    public function isPublished(): bool
    {
        return $this->status === ContentStatus::Published
            && (is_null($this->published_at) || $this->published_at->isPast());
    }
}
