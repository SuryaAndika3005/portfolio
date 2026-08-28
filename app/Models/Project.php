<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'gallery_images' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Gallery image paths, resolved once at seed/upload time and stored
     * in the gallery_images column — no filesystem probing at render time.
     */
    public function galleryImages(): array
    {
        return $this->gallery_images ?? [];
    }

    /**
     * The image used for the Selected Works accordion cover. Prefers the
     * dedicated cover_image_path override (lets a project use a different
     * crop/composition there than on its own detail page) and falls back
     * to the main image_path when no override is set. The detail page
     * itself always uses image_path directly, unaffected by this.
     */
    public function coverImagePath(): ?string
    {
        return $this->cover_image_path ?: $this->image_path;
    }
}