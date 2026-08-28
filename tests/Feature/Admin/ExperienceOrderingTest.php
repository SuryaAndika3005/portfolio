<?php

namespace Tests\Feature\Admin;

use App\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExperienceOrderingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * All rows below share one identical created_at (matching the real
     * seeded data) so the test can't accidentally pass because of
     * insertion-order luck -- it only passes if the duration string is
     * actually being parsed.
     */
    private function seedOutOfOrder(): void
    {
        $now = now();

        Experience::insert([
            ['role' => 'Older Job', 'company' => 'A', 'category' => 'Professional Work', 'duration' => 'May - Jul 2024', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['role' => 'Current Job', 'company' => 'B', 'category' => 'Professional Work', 'duration' => 'Jun 2025 - Present', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['role' => 'Older Org', 'company' => 'C', 'category' => 'Organization', 'duration' => 'Dec 2023 - Feb 2024', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['role' => 'Newer Org', 'company' => 'D', 'category' => 'Organization', 'duration' => 'Dec 2024 - Feb 2025', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function test_chronology_end_parses_present_as_the_farthest_future(): void
    {
        $current = new Experience(['duration' => 'Jun 2025 - Present']);
        $past = new Experience(['duration' => 'May - Jul 2024']);

        $this->assertTrue($current->chronologyEnd()->gt($past->chronologyEnd()));
    }

    public function test_chronology_end_orders_by_the_later_end_date(): void
    {
        $newer = new Experience(['duration' => 'Dec 2024 - Feb 2025']);
        $older = new Experience(['duration' => 'Dec 2023 - Feb 2024']);

        $this->assertTrue($newer->chronologyEnd()->gt($older->chronologyEnd()));
    }

    public function test_admin_index_groups_by_category_and_sorts_chronologically_within_group(): void
    {
        $this->seedOutOfOrder();

        $response = $this->actingAs(User::factory()->create())
            ->get(route('admin.experiences.index'));

        $response->assertOk();

        $roles = $response->viewData('experiences')->pluck('role')->all();

        // Professional Work tier first (current before older), then
        // Organization tier (newer end date before older) -- never the
        // raw insertion/created_at order the rows were seeded in.
        $this->assertSame(['Current Job', 'Older Job', 'Newer Org', 'Older Org'], $roles);
    }

    public function test_sort_chronologically_is_deterministic_on_identical_duration_strings(): void
    {
        $now = now();
        Experience::insert([
            ['role' => 'Tie A', 'company' => 'X', 'category' => 'Organization', 'duration' => '2024 - 2025', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['role' => 'Tie B', 'company' => 'Y', 'category' => 'Organization', 'duration' => '2024 - 2025', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $sortedOnce = Experience::sortChronologically(Experience::all())->pluck('role')->all();
        $sortedAgain = Experience::sortChronologically(Experience::all())->pluck('role')->all();

        $this->assertSame($sortedOnce, $sortedAgain);
    }
}
