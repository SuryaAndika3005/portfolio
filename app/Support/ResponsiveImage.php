<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Builds a srcset string for a storage/public image from pre-generated
 * width derivatives (see scripts/generate-image-derivatives.py provenance
 * -- derivatives live alongside the original as "{stem}-{width}w.webp" and
 * are never generated on the fly). Widths are auto-discovered by globbing
 * the image's own directory, rather than checked against a fixed candidate
 * list, since each image's derivative set is sized to its own native
 * resolution and usage context (a hero portrait vs. a project cover need
 * different tiers) -- a fixed list would silently miss whichever widths
 * don't happen to match. Purely additive: an image with no derivatives on
 * disk yields no srcset and the caller's plain <img src> still renders
 * exactly as before, so this never produces a broken image.
 *
 * The directory listing itself is cached (rememberForever, per directory)
 * -- first measured attempt did this on every request for every cover
 * (Featured grid + accordion share the same 5 covers, so ~18 Storage::files()
 * calls per homepage load) and measurably regressed server-response-time
 * in Lighthouse. Derivatives only change when someone reruns the generator,
 * so `php artisan cache:forget` (or a cache:clear) is the expected step
 * after regenerating a project's derivatives, same as any other build asset.
 */
class ResponsiveImage
{
    /**
     * @param  string  $storagePath  Path relative to the "public" disk, e.g. "projects/dika.webp".
     */
    public static function srcset(string $storagePath): ?string
    {
        $dir = pathinfo($storagePath, PATHINFO_DIRNAME);
        $stem = pathinfo($storagePath, PATHINFO_FILENAME);
        $prefix = $dir === '.' ? '' : "{$dir}/";

        $basenames = Cache::rememberForever(
            "responsive-image-derivatives:{$dir}",
            fn () => Storage::disk('public')->files($dir === '.' ? '' : $dir)
        );

        $pattern = '/^'.preg_quote($stem, '/').'-(\d+)w\.webp$/';

        $entries = [];
        foreach ($basenames as $file) {
            $basename = basename($file);
            if (preg_match($pattern, $basename, $m)) {
                $entries[(int) $m[1]] = asset("storage/{$prefix}{$basename}").' '.$m[1].'w';
            }
        }

        if (! $entries) {
            return null;
        }

        ksort($entries);

        return implode(', ', $entries);
    }
}
