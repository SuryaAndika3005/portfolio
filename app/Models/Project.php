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
     * Public listing surfaces (homepage, archive, prev/next) only ever show
     * published projects. An unpublished project's own detail page still
     * resolves directly by ID -- it's unlisted, not deleted or 404'd.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
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

    /**
     * Localized project copy for the active locale (Global Language
     * Catalog System) — source-based, not a DB column: looks up
     * lang/{locale}/project_content.php, keyed by this project's own ID,
     * and returns $field from that array when present.
     *
     * Only role/description/problem/process/result are ever meant to be
     * looked up this way — title, client, year, and tool/tech names stay
     * as plain DB reads everywhere in the app, deliberately never routed
     * through this accessor, so they're never accidentally "translated"
     * into something a portfolio visitor wouldn't recognize.
     *
     * Falls back to the raw DB column whenever a translation is missing
     * -- no catalog entry for this project at all, or an entry that
     * simply omits this one field (both are expected/normal, not error
     * states: only the 5 Featured projects are translated so far, see
     * lang/id/project_content.php's own header comment). trans() returns
     * the literal lookup key (a string, not an array) when nothing
     * resolves, which is exactly what the is_array() check below is
     * guarding against -- a public page must never be able to render a
     * raw "project_content.21.problem"-style key. (The catalog group is
     * named project_content, not projects, specifically to avoid
     * colliding with the app's own bare __('Project')/__('Projects') UI
     * strings -- see that file's header comment for the exact failure
     * mode this sidesteps.)
     */
    public function localized(string $field): ?string
    {
        $translations = trans('project_content.'.$this->id);

        if (is_array($translations) && filled($translations[$field] ?? null)) {
            return $translations[$field];
        }

        return $this->{$field};
    }
}