<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'gallery_images' => 'array',
        'is_highlighted' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Extra screenshots for the detail page gallery, as a Collection
     * so views can chain isNotEmpty(), etc.
     */
    public function galleryImages(): Collection
    {
        return collect($this->gallery_images ?? []);
    }

    /**
     * Only projects marked as highlighted, manual order first (nulls last),
     * then most recent.
     */
    public function scopeHighlighted($query)
    {
        return $query->where('is_highlighted', true)
            ->orderByRaw('featured_order IS NULL, featured_order ASC')
            ->latest();
    }

    /**
     * Whether this project was added within the last $days days.
     */
    public function isRecent(int $days = 30): bool
    {
        return $this->created_at && $this->created_at->greaterThanOrEqualTo(now()->subDays($days));
    }
}
