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
}
