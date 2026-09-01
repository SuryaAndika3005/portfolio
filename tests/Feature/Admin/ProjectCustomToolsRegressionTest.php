<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regression coverage for a real pre-existing Admin data-integrity bug: the
 * "Other tools" field in resources/views/admin/projects/_form.blade.php used
 * to render from old('tools_custom') only, which is empty on a normal
 * (non-validation-error) page load. Since ProjectController's update flow
 * unconditionally rebuilds the `tools` column from the checkbox array plus
 * that one field (ProjectRequest::toolsString()), opening Edit on a project
 * whose tools included anything outside the fixed checkbox list and saving
 * without manually retyping those tools back in would silently delete them.
 *
 * This test drives the real GET edit -> PUT update round trip and extracts
 * the actual rendered form field values (not the Blade source) to prove the
 * fix holds through the application's real request/controller semantics.
 */
class ProjectCustomToolsRegressionTest extends TestCase
{
    use RefreshDatabase;

    private const CUSTOM_TOOLS = ['TensorFlow', 'Keras', 'Librosa', 'Streamlit', 'scikit-learn', 'SciPy'];

    private function projectWithMixedTools(): Project
    {
        return Project::factory()->create([
            'category_id' => Category::factory()->create()->id,
            'tools' => implode(', ', array_merge(['Python'], self::CUSTOM_TOOLS)),
        ]);
    }

    /**
     * Parses the real rendered edit-page HTML for the checked tools[]
     * checkboxes and the tools_custom field's actual value attribute --
     * i.e. exactly what a real browser would submit on Save without the
     * user touching anything, not a hand-built request payload.
     */
    private function formFieldsFromRenderedEditPage(Project $project): array
    {
        $html = $this->actingAs(User::factory()->create())
            ->get(route('admin.projects.edit', $project))
            ->getContent();

        preg_match_all('/<input type="checkbox" name="tools\[\]" value="([^"]+)"[^>]*>/', $html, $checkboxMatches, PREG_SET_ORDER);
        $checkedTools = collect($checkboxMatches)
            ->filter(fn ($m) => str_contains($m[0], 'checked'))
            ->map(fn ($m) => $m[1])
            ->values()
            ->all();

        preg_match('/<input type="text" name="tools_custom"[^>]*value="([^"]*)"/', $html, $customMatch);
        $toolsCustom = $customMatch[1] ?? '';

        return ['tools' => $checkedTools, 'tools_custom' => html_entity_decode($toolsCustom)];
    }

    public function test_editing_and_saving_a_project_does_not_silently_delete_its_custom_tools(): void
    {
        $project = $this->projectWithMixedTools();

        // Exactly what a real browser would submit: the rendered form's own
        // checked boxes and tools_custom value, untouched by the user.
        $formFields = $this->formFieldsFromRenderedEditPage($project);

        // Sanity check on the fix itself: the rendered form must already
        // carry the project's real custom tools before we ever submit
        // anything, otherwise this test would pass for the wrong reason.
        foreach (self::CUSTOM_TOOLS as $tool) {
            $this->assertStringContainsString($tool, $formFields['tools_custom']);
        }

        $this->actingAs(User::factory()->create())
            ->put(route('admin.projects.update', $project), array_merge([
                'category_id' => $project->category_id,
                'title' => $project->title,
            ], $formFields))
            ->assertRedirect(route('admin.projects.index'));

        $savedTools = $project->fresh()->tools;

        foreach (array_merge(['Python'], self::CUSTOM_TOOLS) as $tool) {
            $this->assertStringContainsString($tool, $savedTools);
        }
    }

    public function test_deliberately_removing_a_tool_still_saves_the_smaller_set(): void
    {
        $project = $this->projectWithMixedTools();

        $formFields = $this->formFieldsFromRenderedEditPage($project);
        $formFields['tools_custom'] = collect(self::CUSTOM_TOOLS)
            ->reject(fn ($tool) => $tool === 'SciPy')
            ->implode(', ');

        $this->actingAs(User::factory()->create())
            ->put(route('admin.projects.update', $project), array_merge([
                'category_id' => $project->category_id,
                'title' => $project->title,
            ], $formFields))
            ->assertRedirect(route('admin.projects.index'));

        $savedTools = $project->fresh()->tools;

        $this->assertStringContainsString('Librosa', $savedTools);
        $this->assertStringNotContainsString('SciPy', $savedTools);
    }
}
