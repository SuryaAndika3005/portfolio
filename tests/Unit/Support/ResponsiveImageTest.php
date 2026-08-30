<?php

namespace Tests\Unit\Support;

use App\Support\ResponsiveImage;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResponsiveImageTest extends TestCase
{
    public function test_srcset_is_null_when_no_derivatives_exist(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('projects/solo.webp', 'fake-bytes');

        $this->assertNull(ResponsiveImage::srcset('projects/solo.webp'));
    }

    public function test_srcset_lists_existing_derivatives_sorted_by_width(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('projects/hero.webp', 'fake-bytes');
        Storage::disk('public')->put('projects/hero-768w.webp', 'fake-bytes');
        Storage::disk('public')->put('projects/hero-480w.webp', 'fake-bytes');

        $srcset = ResponsiveImage::srcset('projects/hero.webp');

        $this->assertNotNull($srcset);
        $this->assertStringContainsString('480w', $srcset);
        $this->assertStringContainsString('768w', $srcset);
        $this->assertLessThan(
            strpos($srcset, '768w'),
            strpos($srcset, '480w'),
            'Smaller width should be listed first.'
        );
    }

    /**
     * The whole reason this cache uses a short TTL instead of
     * rememberForever (see the class doc comment): derivatives are
     * uploaded by hand via FTP, with no way to run `cache:forget`
     * afterward. A directory listed before its derivatives exist must
     * self-heal once they're uploaded -- not stay empty permanently.
     */
    public function test_newly_uploaded_derivative_becomes_visible_after_cache_expires(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('projects/new-project.webp', 'fake-bytes');

        // First lookup: no derivatives yet, caches an empty listing for this directory.
        $this->assertNull(ResponsiveImage::srcset('projects/new-project.webp'));

        // Derivatives get uploaded by hand shortly after (same directory).
        Storage::disk('public')->put('projects/new-project-480w.webp', 'fake-bytes');

        // Still within the TTL: the cached (empty) listing is still served.
        $this->assertNull(ResponsiveImage::srcset('projects/new-project.webp'));

        // Past the ~10 minute TTL: the directory listing must be re-read
        // from disk, not served from a permanent cache entry.
        $this->travel(11)->minutes();

        $srcset = ResponsiveImage::srcset('projects/new-project.webp');
        $this->assertNotNull($srcset);
        $this->assertStringContainsString('480w', $srcset);
    }
}
