@props(['project'])

{{-- Deterministic Project Completeness (V1.2, Sections 27-32) -- no AI,
     computed fresh on every page load from real Project columns via
     App\Services\ProjectCompleteness. Answers "are the fields filled in?"
     only; it says nothing about writing quality (that's the separate AI
     Quality Review inside the Assistant panel). Compact by design: a
     count, a checklist, nothing gamified. --}}
@php
    $completeness = \App\Services\ProjectCompleteness::evaluate($project);
@endphp
<div class="admin-completeness">
    <p class="admin-completeness-score">
        <span>Project Completeness</span>
        <span class="admin-completeness-count">{{ $completeness['score'] }}/{{ $completeness['total'] }} checks complete</span>
    </p>
    <ul class="admin-completeness-list">
        @foreach ($completeness['checks'] as $check)
            <li class="admin-completeness-item" data-complete="{{ $check['complete'] ? 'true' : 'false' }}">
                <span aria-hidden="true" class="admin-completeness-icon">{{ $check['complete'] ? '✓' : '○' }}</span>
                <span>{{ $check['label'] }}</span>
                <span class="sr-only">{{ $check['complete'] ? 'Complete' : 'Missing' }}</span>
            </li>
        @endforeach
    </ul>
</div>
