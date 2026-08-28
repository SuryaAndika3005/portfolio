<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\Gemini\GeminiException;
use App\Services\Gemini\ImageInput;
use App\Services\GeminiProjectAssistant;
use App\Services\ProjectAssistantSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Admin-only (see routes/web.php — nested inside the existing 'auth' +
 * 'throttle:ai-assistant' group). Every action here is orchestration only:
 * pull request/Storage bytes together, hand them to GeminiProjectAssistant,
 * fold the structured result into ProjectAssistantSession, and return JSON.
 * Nothing here ever mutates a Project row except applyCover() — a single,
 * explicit, one-field, user-clicked action (Section 65) — everything else
 * (Description/Problem/Process/Result) only ever reaches the Project form
 * fields in the browser; the normal Create/Save Changes submit is still
 * what persists them (Section 52).
 */
class ProjectAssistantController extends Controller
{
    private const MAX_IMAGES = 8;

    private const MAX_IMAGE_KB = 4096;

    public function __construct(private readonly GeminiProjectAssistant $assistant)
    {
    }

    public function analyze(Request $request, ?Project $project): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'client' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'string', 'max:4'],
            'tools' => ['nullable', 'array'],
            'tools.*' => ['string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            ...$this->imageValidationRules(),
        ]);

        $session = $this->session($project);

        try {
            $images = $this->resolveImages($request, $project);
        } catch (GeminiException $e) {
            return $this->errorResponse($e);
        }

        $session->mergeFacts($this->factsFromRequest($validated, $project));

        try {
            $result = $this->assistant->analyze($session->facts(), $images);
        } catch (GeminiException $e) {
            return $this->errorResponse($e, 'analyze');
        }

        $session->setInteractionId($result['interaction_id']);
        $session->mergeFacts([
            'confirmed_facts' => $result['confirmed_facts'],
            'visual_observations' => $result['visual_observations'],
        ]);
        $session->setFact('unknowns', $result['unknowns']);
        $session->setUnderstanding([
            'status' => $result['understanding'],
            'inferences_needing_confirmation' => $result['inferences_needing_confirmation'],
            'questions' => $result['questions'],
            'ready_for_draft' => $result['ready_for_draft'],
        ]);

        if ($result['questions'] !== []) {
            $session->appendTranscript('assistant', implode("\n", array_map(
                fn ($q, $i) => ($i + 1).". {$q}",
                $result['questions'],
                array_keys($result['questions']),
            )));
        }

        return response()->json([
            'understanding' => $session->understanding(),
            'unknowns' => $session->facts()['unknowns'] ?? [],
            'images_used' => array_map(fn (ImageInput $i) => $i->label, $images),
            'transcript' => $session->transcript(),
        ]);
    }

    public function reply(Request $request, ?Project $project): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $session = $this->session($project);

        if (! $session->interactionId()) {
            return response()->json([
                'message' => 'Analyze the project before replying — the Assistant has nothing to continue yet.',
            ], 422);
        }

        $session->appendTranscript('user', $validated['message']);

        try {
            $result = $this->assistant->reply($validated['message'], $session->interactionId(), $session->facts());
        } catch (GeminiException $e) {
            return $this->errorResponse($e, 'reply');
        }

        $session->setInteractionId($result['interaction_id']);
        $this->applyFactCorrections($session, $result['fact_corrections']);
        $session->mergeFacts(['confirmed_facts' => $result['new_confirmed_facts']]);
        $session->setFact('unknowns', $result['updated_unknowns']);
        $session->setUnderstanding([
            'status' => $result['understanding'],
            'inferences_needing_confirmation' => $session->understanding()['inferences_needing_confirmation'] ?? [],
            'questions' => $result['next_questions'],
            'ready_for_draft' => $result['ready_for_draft'],
        ]);
        $session->appendTranscript('assistant', $result['message']);

        return response()->json([
            'message' => $result['message'],
            'unknowns' => $session->facts()['unknowns'] ?? [],
            'questions' => $result['next_questions'],
            'ready_for_draft' => $result['ready_for_draft'],
            'transcript' => $session->transcript(),
        ]);
    }

    public function generateDraft(Request $request, ?Project $project): JsonResponse
    {
        $session = $this->session($project);

        if (! $session->interactionId()) {
            return response()->json([
                'message' => 'Analyze the project before generating a draft.',
            ], 422);
        }

        try {
            $result = $this->assistant->generateDraft($session->facts(), $session->interactionId());
        } catch (GeminiException $e) {
            return $this->errorResponse($e, 'generate_draft');
        }

        $session->setInteractionId($result['interaction_id']);
        $this->applyFactCorrections($session, $result['fact_corrections']);
        $draft = array_diff_key($result, ['interaction_id' => null, 'fact_corrections' => null]);
        $session->setDraft($draft);

        return response()->json($draft);
    }

    public function refine(Request $request, ?Project $project): JsonResponse
    {
        $validated = $request->validate([
            'feedback' => ['required', 'string', 'max:2000'],
            'target_field' => ['nullable', 'in:description,problem,process,result'],
            'target_language' => ['nullable', 'in:en,id'],
        ]);

        $session = $this->session($project);

        if (! $session->interactionId() || ! $session->draft()) {
            return response()->json([
                'message' => 'Generate a draft before refining it.',
            ], 422);
        }

        $previousDraft = $session->draft();

        try {
            $result = $this->assistant->refine(
                feedback: $validated['feedback'],
                currentDraft: $previousDraft,
                facts: $session->facts(),
                previousInteractionId: $session->interactionId(),
                targetField: $validated['target_field'] ?? null,
                targetLanguage: $validated['target_language'] ?? null,
            );
        } catch (GeminiException $e) {
            return $this->errorResponse($e, 'refine');
        }

        $session->setInteractionId($result['interaction_id']);
        $this->applyFactCorrections($session, $result['fact_corrections']);
        $draft = array_diff_key($result, ['interaction_id' => null, 'fact_corrections' => null]);
        $session->setDraft($draft);

        return response()->json([
            ...$draft,
            'changed_fields' => $this->changedFields($previousDraft, $draft),
        ]);
    }

    public function reset(Request $request, ?Project $project): JsonResponse
    {
        $this->session($project)->reset();

        return response()->json(['ok' => true]);
    }

    /**
     * V1.2 — upgraded from V1.1's single-pick recommendation to ranked
     * candidates (Sections 22-26). Same endpoint/route name kept
     * (recommendCover) to avoid touching routes/web.php or the Blade
     * component's data-cover-url — only the response shape changed.
     */
    public function recommendCover(Request $request, ?Project $project): JsonResponse
    {
        $request->validate($this->imageValidationRules());

        $session = $this->session($project);

        try {
            $images = $this->resolveImages($request, $project);
        } catch (GeminiException $e) {
            return $this->errorResponse($e);
        }

        if (count($images) < 2) {
            return response()->json([
                'message' => 'Select at least two images to compare for a cover recommendation.',
            ], 422);
        }

        try {
            $result = $this->assistant->rankCovers($images, $session->facts(), $session->interactionId());
        } catch (GeminiException $e) {
            return $this->errorResponse($e, 'rank_covers');
        }

        $rankings = collect($result['rankings'])->map(fn ($r) => [
            'rank' => $r['rank'],
            'label' => $images[$r['index']]->label,
            'path' => $images[$r['index']]->existingPath ?? null,
            'reason' => $r['reason'],
        ])->values();

        return response()->json(['rankings' => $rankings]);
    }

    /**
     * V1.2 — AI Suggested Gallery Order (Sections 12-16). Edit-page only:
     * only implemented once manual reorder genuinely works, and only
     * against the project's own already-saved gallery — Create-page
     * ordering (freshly selected, not-yet-uploaded files) is explicitly
     * deferred (Section 11's own escape hatch), consistent with manual
     * reorder itself being Edit-only in this release (see
     * SMART_PORTFOLIO_WORKFLOW_V1_2_REPORT.md).
     *
     * gallery_paths[] carries the CURRENT on-screen order (which may
     * already differ from the last-saved order if the Admin has been
     * dragging thumbnails around before saving) — validated against the
     * project's own stored gallery, never trusted blindly.
     */
    public function suggestGalleryOrder(Request $request, Project $project): JsonResponse
    {
        $request->validate([
            'gallery_paths' => ['nullable', 'array'],
            'gallery_paths.*' => ['string'],
        ]);

        $owned = collect($project->galleryImages());
        $submittedOrder = collect($request->input('gallery_paths', []))->intersect($owned)->values();
        $paths = $submittedOrder->count() === $owned->count() ? $submittedOrder : $owned;

        if ($paths->count() < 2) {
            return response()->json([
                'message' => 'The gallery needs at least two images for an order suggestion.',
            ], 422);
        }

        try {
            $images = $paths->map(fn ($path, $i) => $this->imageFromStorage($path, 'Gallery '.($i + 1)))->all();
        } catch (GeminiException $e) {
            return $this->errorResponse($e);
        }

        $session = $this->session($project);

        try {
            $result = $this->assistant->suggestGalleryOrder($images, $session->facts(), $session->interactionId());
        } catch (GeminiException $e) {
            return $this->errorResponse($e, 'suggest_gallery_order');
        }

        $reasonByIndex = collect($result['reasoning_summary'])->keyBy('index');

        $suggested = collect($result['ordered_indices'])->map(fn ($i) => [
            'path' => $images[$i]->existingPath,
            'reason' => $reasonByIndex[$i]['reason'] ?? null,
        ])->values();

        return response()->json([
            'current_order' => $paths->values(),
            'suggested_order' => $suggested,
        ]);
    }

    /**
     * V1.2 — AI Tool Suggestions (Sections 17-21). Text-only, cheap
     * (Section 52). Never checks a box itself — the frontend only marks a
     * suggestion "confirmed", which checks the matching checklist box or
     * appends to the custom-tools text, exactly like a human typing/
     * clicking would (Section 21).
     */
    public function suggestTools(Request $request, ?Project $project): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'client' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'string', 'max:4'],
            'tools' => ['nullable', 'array'],
            'tools.*' => ['string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $session = $this->session($project);
        $facts = array_merge($session->facts(), $this->factsFromRequest($validated, $project));

        try {
            $result = $this->assistant->suggestTools($facts, config('portfolio.tool_options'));
        } catch (GeminiException $e) {
            return $this->errorResponse($e, 'suggest_tools');
        }

        return response()->json(['suggested_tools' => $result['suggested_tools']]);
    }

    /**
     * V1.2 — AI Project Quality Review (Sections 33-42). Text-only, reads
     * the current live form values (so it works before the first save
     * too), falling back to the saved Project's own fields when editing
     * and a field wasn't overridden in the request.
     */
    public function reviewQuality(Request $request, ?Project $project): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'problem' => ['nullable', 'string'],
            'process' => ['nullable', 'string'],
            'result' => ['nullable', 'string'],
            'tools' => ['nullable', 'array'],
            'tools.*' => ['string', 'max:255'],
        ]);

        $session = $this->session($project);
        $facts = array_merge($session->facts(), $this->factsFromRequest($validated, $project));
        $this->mergeCaseStudyFields($facts, $validated, $project);

        if (blank($facts['description'] ?? null) && blank($facts['problem'] ?? null)
            && blank($facts['process'] ?? null) && blank($facts['result'] ?? null)) {
            return response()->json([
                'message' => 'Add at least some case-study text before requesting a review.',
            ], 422);
        }

        try {
            $result = $this->assistant->reviewQuality($facts);
        } catch (GeminiException $e) {
            return $this->errorResponse($e, 'review_quality');
        }

        return response()->json([
            'summary' => $result['summary'],
            'strengths' => $result['strengths'],
            'issues' => $result['issues'],
            'recommended_actions' => $result['recommended_actions'],
        ]);
    }

    /**
     * V1.2 — SEO Assistant (Sections 43-50). Suggestion-only: no
     * seo_title/seo_description column exists on Project (confirmed
     * against the migrations — see the V1.2 report's schema audit), so
     * nothing here is ever saved. Text-only, same live-form-first fact
     * resolution as reviewQuality.
     */
    public function generateSeo(Request $request, ?Project $project): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'problem' => ['nullable', 'string'],
            'process' => ['nullable', 'string'],
            'result' => ['nullable', 'string'],
        ]);

        $session = $this->session($project);
        $facts = array_merge($session->facts(), $this->factsFromRequest($validated, $project));
        $this->mergeCaseStudyFields($facts, $validated, $project);

        if (blank($facts['project_name'] ?? null)) {
            return response()->json([
                'message' => 'Add at least a Title before requesting SEO suggestions.',
            ], 422);
        }

        try {
            $result = $this->assistant->generateSeo($facts);
        } catch (GeminiException $e) {
            return $this->errorResponse($e, 'generate_seo');
        }

        return response()->json([
            'title' => $result['title'],
            'meta_description' => $result['meta_description'],
            'social_description' => $result['social_description'],
            'notes' => $result['notes'],
        ]);
    }

    /**
     * The one action in this controller that persists anything — a single
     * explicit click applying an AI cover recommendation for an *existing*
     * stored image (Section 65). Only reachable for a real, saved Project;
     * Create-page "use this freshly selected file as cover" is handled
     * entirely client-side (copy the File into the cover_image input) and
     * never touches this endpoint — see resources/js/admin/project-assistant.js.
     */
    public function applyCover(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate(['path' => ['required', 'string']]);

        $owned = collect([$project->image_path, $project->cover_image_path, ...$project->galleryImages()])->filter();

        if (! $owned->contains($validated['path'])) {
            return response()->json(['message' => 'That image no longer belongs to this project.'], 422);
        }

        $project->update(['cover_image_path' => $validated['path']]);

        return response()->json(['ok' => true, 'cover_image_path' => $validated['path']]);
    }

    // --- Helpers -------------------------------------------------------

    private function session(?Project $project): ProjectAssistantSession
    {
        return new ProjectAssistantSession($project ? "project:{$project->id}" : 'create');
    }

    private function imageValidationRules(): array
    {
        return [
            'image' => ['nullable', 'image', 'max:'.self::MAX_IMAGE_KB],
            'cover_image' => ['nullable', 'image', 'max:'.self::MAX_IMAGE_KB],
            'gallery' => ['nullable', 'array', 'max:'.self::MAX_IMAGES],
            'gallery.*' => ['image', 'max:'.self::MAX_IMAGE_KB],
            'existing_images' => ['nullable', 'array', 'max:'.self::MAX_IMAGES],
            'existing_images.*' => ['string'],
        ];
    }

    /**
     * Builds the bounded, transparent image set actually sent to Gemini
     * (Sections 20-23): newly selected files first (they're what the user
     * is actively looking at), then existing stored images the user
     * checked — intersected against the project's own paths exactly like
     * ProjectController::update already does for gallery removal, so an
     * arbitrary path can never be requested. Capped at MAX_IMAGES total.
     *
     * @return ImageInput[]
     *
     * @throws GeminiException
     */
    private function resolveImages(Request $request, ?Project $project): array
    {
        $images = [];

        foreach (['image' => 'Main visual', 'cover_image' => 'Archive cover'] as $field => $label) {
            if ($request->hasFile($field)) {
                $images[] = $this->imageFromUpload($request->file($field), $label);
            }
        }

        foreach ($request->file('gallery', []) as $i => $file) {
            $images[] = $this->imageFromUpload($file, 'Gallery upload '.($i + 1));
        }

        if ($project && $request->filled('existing_images')) {
            $owned = collect([$project->image_path, $project->cover_image_path, ...$project->galleryImages()])
                ->filter()
                ->values();

            $selected = collect($request->input('existing_images'))->intersect($owned)->values();

            foreach ($selected as $i => $path) {
                $images[] = $this->imageFromStorage($path, 'Existing image '.($i + 1));
            }
        }

        if (count($images) > self::MAX_IMAGES) {
            $images = array_slice($images, 0, self::MAX_IMAGES);
        }

        return $images;
    }

    private function imageFromUpload(UploadedFile $file, string $label): ImageInput
    {
        return new ImageInput(
            bytes: $file->get(),
            mimeType: $file->getMimeType() ?: 'image/jpeg',
            label: $label,
        );
    }

    private function imageFromStorage(string $path, string $label): ImageInput
    {
        $normalized = ltrim(str_replace('storage/', '', $path), '/');
        $disk = Storage::disk('public');

        if (! $disk->exists($normalized)) {
            throw new GeminiException("One of the selected images could not be found ({$label}).");
        }

        $mime = $disk->mimeType($normalized);

        if (! str_starts_with((string) $mime, 'image/')) {
            throw new GeminiException("One of the selected images could not be processed ({$label}).");
        }

        if ($disk->size($normalized) > self::MAX_IMAGE_KB * 1024) {
            throw new GeminiException("One of the selected images is too large to analyze ({$label}).");
        }

        return new ImageInput(bytes: $disk->get($normalized), mimeType: $mime, label: $label, existingPath: $path);
    }

    private function factsFromRequest(array $validated, ?Project $project): array
    {
        $facts = [
            'project_name' => $validated['title'] ?? $project?->title,
            'category' => $validated['category'] ?? $project?->category?->name,
            'role' => $validated['role'] ?? $project?->role,
            'client' => $validated['client'] ?? $project?->client,
            'year' => $validated['year'] ?? $project?->year,
            'tools_confirmed' => $validated['tools'] ?? ($project?->tools ? array_map('trim', explode(',', $project->tools)) : []),
        ];

        if (filled($validated['notes'] ?? null)) {
            $facts['additional_notes'] = $validated['notes'];
        }

        if ($project) {
            foreach (['description', 'problem', 'process', 'result'] as $field) {
                if (filled($project->$field)) {
                    $facts["existing_{$field}"] = $project->$field;
                }
            }
        }

        return array_filter($facts, fn ($v) => $v !== null && $v !== '' && $v !== []);
    }

    /**
     * V1.2 (reviewQuality/generateSeo) — resolves each case-study field to
     * a single value: the live form value if the request actually
     * submitted one, otherwise the saved Project's own value. Also
     * removes factsFromRequest()'s own "existing_{field}" entries for
     * these same fields so the prompt never shows both "Existing
     * description: X" and "Description: Y" as if they were two different
     * facts — one clear value per field, chosen with live-value priority.
     */
    private function mergeCaseStudyFields(array &$facts, array $validated, ?Project $project): void
    {
        foreach (['description', 'problem', 'process', 'result'] as $field) {
            $value = $validated[$field] ?? $project?->$field;

            if (filled($value)) {
                $facts[$field] = $value;
            }

            unset($facts["existing_{$field}"]);
        }
    }

    /**
     * Fact corrections are folded in as plain confirmed_facts entries
     * (never mechanically "found and replaced" against prior text — that
     * would be a fragile string-matching operation on natural language).
     * Each turn re-sends the full facts summary to Gemini, and the system
     * instruction's REVISION rule tells it a correction takes precedence
     * — combined with the interaction's own conversational memory of the
     * correction being made, this is a robust way to apply Section 60
     * without inventing brittle text-diffing.
     */
    private function applyFactCorrections(ProjectAssistantSession $session, array $corrections): void
    {
        if ($corrections === []) {
            return;
        }

        $lines = array_map(fn ($c) => "Correction — {$c['about']}: {$c['correction']}", $corrections);
        $session->mergeFacts(['confirmed_facts' => $lines]);
    }

    private function changedFields(array $before, array $after): array
    {
        $changed = [];

        foreach (['en', 'id'] as $lang) {
            foreach (['description', 'problem', 'process', 'result'] as $field) {
                if (($before[$lang][$field] ?? null) !== ($after[$lang][$field] ?? null)) {
                    $changed[] = "{$lang}.{$field}";
                }
            }
        }

        return $changed;
    }

    private function errorResponse(GeminiException $e, ?string $operation = null): JsonResponse
    {
        if ($operation) {
            Log::warning('gemini.assistant_error', [
                'operation' => $operation,
                'reason' => $e->technicalReason,
                'status' => $e->statusCode,
            ]);
        }

        $status = $e->statusCode;
        $httpStatus = ($status !== null && $status >= 400 && $status < 500) ? $status : 502;

        return response()->json(['message' => $e->getMessage()], $httpStatus);
    }
}
