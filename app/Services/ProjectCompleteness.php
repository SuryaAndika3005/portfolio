<?php

namespace App\Services;

use App\Models\Project;

/**
 * Deterministic Project completeness checklist (V1.2, Sections 27-31).
 * No AI involved -- every check is a plain presence test against a real,
 * existing Project column. Answers "are the important fields filled in?",
 * never "is this project good?" (that's the separate AI Quality Review).
 *
 * The 10 checks below are deliberately only the genuinely-optional fields.
 * title/category_id are required by ProjectRequest validation, so every
 * saved Project already has both -- including them here would just pad
 * the denominator with permanently-true checks that never distinguish one
 * project from another. No category-aware differentiation is applied:
 * the current Project schema has no category-specific optional fields
 * (e.g. no live-demo/repository URL column exists for any category --
 * confirmed against the migrations and show.blade.php's own comment
 * documenting that absence), so there is nothing genuine to differentiate
 * on yet (Section 29) -- every check below applies the same way to every
 * category rather than inventing a distinction the schema doesn't support.
 */
class ProjectCompleteness
{
    private const CHECKS = [
        'description' => 'Description',
        'role' => 'Role',
        'year' => 'Year',
        'image_path' => 'Main Image',
        'gallery' => 'Gallery',
        'problem' => 'Problem',
        'process' => 'Process',
        'result' => 'Result',
        'tools' => 'Tools',
        'client' => 'Client',
    ];

    /**
     * @return array{score: int, total: int, checks: array<int, array{key: string, label: string, complete: bool}>}
     */
    public static function evaluate(Project $project): array
    {
        $checks = [];

        foreach (self::CHECKS as $key => $label) {
            $checks[] = [
                'key' => $key,
                'label' => $label,
                'complete' => self::isComplete($project, $key),
            ];
        }

        $score = collect($checks)->where('complete', true)->count();

        return [
            'score' => $score,
            'total' => count($checks),
            'checks' => $checks,
        ];
    }

    private static function isComplete(Project $project, string $key): bool
    {
        return match ($key) {
            'gallery' => filled($project->galleryImages()),
            default => filled($project->{$key}),
        };
    }

    /**
     * V1.2 UX Refinement — one deterministic, locally-computed suggestion
     * for "what should I do next" (Section 6). Never calls Gemini; it's
     * guidance derived from the same real Project columns evaluate()
     * already reads, nothing more. `anchor` is a DOM id the Project
     * Status widget's "Open Review"-style button scrolls to — see
     * scrollToField()/scrollToAnchor() in project-assistant.js.
     *
     * Deliberately does not attempt to reason about AI-session state
     * (whether Analyze/Quality Review have run this pageload) — that's
     * inherently not known at server-render time. project-assistant.js
     * updates this same widget's text reactively after a real Quality
     * Review response comes back, without ever triggering a second call.
     *
     * @param  array  $evaluation  The return value of evaluate($project).
     * @return array{label: string, anchor: string}
     */
    public static function recommendedNextAction(Project $project, array $evaluation): array
    {
        if (blank($project->problem) && blank($project->process) && blank($project->result)) {
            return ['label' => 'Complete the case study', 'anchor' => 'workspace-write'];
        }

        if (count($project->galleryImages()) >= 2 && blank($project->cover_image_path)) {
            return ['label' => 'Choose a cover', 'anchor' => 'workspace-curate'];
        }

        if ($evaluation['score'] < $evaluation['total']) {
            return ['label' => 'Complete missing fields', 'anchor' => 'workspace-review'];
        }

        return ['label' => 'Review search & sharing', 'anchor' => 'workspace-ready'];
    }
}
