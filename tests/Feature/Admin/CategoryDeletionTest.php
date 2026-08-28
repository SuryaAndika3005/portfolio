<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_with_projects_cannot_be_deleted(): void
    {
        $category = Category::factory()->create();
        Project::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs(User::factory()->create())
            ->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_category_with_no_projects_can_be_deleted(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    // NOTE: projects.category_id is declared ->onDelete('cascade') at the
    // DB schema level (2026_05_12_143605_create_projects_table.php). The
    // Admin controller's own count-check (tested above) is the only thing
    // preventing that cascade from ever firing through the normal UI flow
    // -- if any other code path ever calls Category::delete() directly,
    // the DB itself would silently destroy every project in that category
    // with no application-level guard. This is flagged, not silently
    // migrated, in LOCALIZATION_THEME_IMPLEMENTATION_REPORT.md pending
    // approval for a follow-up migration changing cascade to restrict.

    public function test_index_shows_protected_by_text_for_categories_with_projects(): void
    {
        $category = Category::factory()->create();
        Project::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs(User::factory()->create())
            ->get(route('admin.categories.index'));

        $response->assertOk();
        $response->assertSee('Protected by 1 project', false);
    }
}
