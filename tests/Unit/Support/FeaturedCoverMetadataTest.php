<?php

namespace Tests\Unit\Support;

use App\Support\FeaturedCoverMetadata;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FeaturedCoverMetadataTest extends TestCase
{
    /**
     * getimagesize() needs real, decodable image bytes -- Storage::fake
     * with an arbitrary string won't do. Generated with GD rather than a
     * checked-in fixture file, so the test carries its own data.
     */
    private function realPng(int $width, int $height): string
    {
        $image = imagecreatetruecolor($width, $height);
        ob_start();
        imagepng($image);
        $bytes = ob_get_clean();
        imagedestroy($image);

        return $bytes;
    }

    public function test_portrait_and_landscape_dimensions_are_read_correctly(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('projects/portrait.webp', $this->realPng(100, 200));
        Storage::disk('public')->put('projects/landscape.webp', $this->realPng(200, 100));

        $metadata = FeaturedCoverMetadata::forPaths(['projects/portrait.webp', 'projects/landscape.webp']);

        $this->assertSame(['width' => 100, 'height' => 200], $metadata['projects/portrait.webp']);
        $this->assertSame(['width' => 200, 'height' => 100], $metadata['projects/landscape.webp']);
    }

    public function test_missing_or_unreadable_image_fails_safely(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('projects/not-an-image.webp', 'this is not image data');

        $metadata = FeaturedCoverMetadata::forPaths([
            'projects/not-an-image.webp',
            'projects/does-not-exist.webp',
        ]);

        $this->assertNull($metadata['projects/not-an-image.webp']);
        $this->assertNull($metadata['projects/does-not-exist.webp']);
    }

    /**
     * The whole point of caching the set: a normal homepage request must
     * not re-read every Featured cover from disk. Proven here by deleting
     * the underlying file after the first (cache-populating) call --  if
     * the second call re-probed the filesystem, it would now see a
     * missing file and return null; instead it must still return the
     * original dimensions from cache.
     */
    public function test_repeated_calls_use_cached_metadata_without_reprobing_filesystem(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('projects/hero.webp', $this->realPng(400, 300));

        $first = FeaturedCoverMetadata::forPaths(['projects/hero.webp']);
        $this->assertSame(['width' => 400, 'height' => 300], $first['projects/hero.webp']);

        Storage::disk('public')->delete('projects/hero.webp');

        $second = FeaturedCoverMetadata::forPaths(['projects/hero.webp']);
        $this->assertSame(['width' => 400, 'height' => 300], $second['projects/hero.webp']);
    }

    public function test_different_cover_path_set_gets_independent_metadata(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('projects/a.webp', $this->realPng(120, 80));
        Storage::disk('public')->put('projects/b.webp', $this->realPng(80, 120));

        $setA = FeaturedCoverMetadata::forPaths(['projects/a.webp']);
        $setB = FeaturedCoverMetadata::forPaths(['projects/b.webp']);

        $this->assertSame(['width' => 120, 'height' => 80], $setA['projects/a.webp']);
        $this->assertSame(['width' => 80, 'height' => 120], $setB['projects/b.webp']);
        $this->assertArrayNotHasKey('projects/a.webp', $setB);
        $this->assertArrayNotHasKey('projects/b.webp', $setA);
    }

    /**
     * Mirrors ResponsiveImage's self-healing TTL test: covers are uploaded
     * by hand via FTP, with no way to run cache:forget afterward, so a
     * path that was missing/broken at first lookup must pick up a
     * subsequently-uploaded file once the short TTL expires -- not stay
     * null forever.
     */
    public function test_cache_self_heals_after_ttl_expires(): void
    {
        Storage::fake('public');

        $first = FeaturedCoverMetadata::forPaths(['projects/late-upload.webp']);
        $this->assertNull($first['projects/late-upload.webp']);

        Storage::disk('public')->put('projects/late-upload.webp', $this->realPng(50, 75));

        // Still within the TTL: the cached (null) result is still served.
        $stillCached = FeaturedCoverMetadata::forPaths(['projects/late-upload.webp']);
        $this->assertNull($stillCached['projects/late-upload.webp']);

        $this->travel(11)->minutes();

        $healed = FeaturedCoverMetadata::forPaths(['projects/late-upload.webp']);
        $this->assertSame(['width' => 50, 'height' => 75], $healed['projects/late-upload.webp']);
    }
}
