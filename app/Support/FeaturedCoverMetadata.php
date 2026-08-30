<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Width/height for the homepage's Featured cover images, computed once per
 * request for the WHOLE set rather than once per project. index.blade.php's
 * Featured loop used to call getimagesize() directly inline (5 synchronous
 * filesystem reads, every single homepage request) to derive
 * $isPortraitCover and $imageAspectRatio -- purely presentational values
 * (crop/object-position per content type, see index.blade.php's own
 * comment), never anything that should vary by visitor. That's a real cost
 * on shared hosting, where production TTFB is already highly variable.
 *
 * One Cache::remember call for the whole Featured set (not five), keyed by
 * a hash of the ordered cover-path list -- so a reorder, a swapped project,
 * or an added/removed Featured slot all naturally produce a different key
 * and a fresh read; nothing goes stale because it's still keyed by an old
 * ID. TTL is short (~10 minutes), matching ResponsiveImage's cache: covers
 * are uploaded by hand via FTP/File Manager on this portfolio, with no SSH
 * access to run cache:forget, so a permanent cache could lock in metadata
 * from before a replaced file finished uploading and never self-correct.
 */
class FeaturedCoverMetadata
{
    /**
     * @param  string[]  $coverPaths  Ordered list of storage-relative cover paths, e.g. "projects/dika.webp".
     * @return array<string, array{width: int, height: int}|null> Keyed by cover path; null when dimensions couldn't be read (missing file, unreadable, corrupt).
     */
    public static function forPaths(array $coverPaths): array
    {
        if (! $coverPaths) {
            return [];
        }

        $key = 'featured-cover-metadata:v1:'.sha1(implode('|', $coverPaths));

        return Cache::remember($key, now()->addMinutes(10), function () use ($coverPaths) {
            $metadata = [];

            foreach ($coverPaths as $path) {
                $dimensions = @getimagesize(Storage::disk('public')->path($path));
                $metadata[$path] = $dimensions
                    ? ['width' => $dimensions[0], 'height' => $dimensions[1]]
                    : null;
            }

            return $metadata;
        });
    }
}
