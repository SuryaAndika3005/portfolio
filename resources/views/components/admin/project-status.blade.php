@props(['project'])

{{-- Project Status (V1.2 UX Refinement, Sections 5-6, 62-63) — replaces
     "here are many AI buttons" with "here is where this Project stands".
     Always visible, zero AI calls: a completeness count plus one locally
     computed recommended next action (App\Services\ProjectCompleteness).
     The detailed ✓/○ checklist lives in Project Review below, not here —
     this stays a compact orientation card, never a dashboard. --}}
@php
    $completeness = \App\Services\ProjectCompleteness::evaluate($project);
    $next = \App\Services\ProjectCompleteness::recommendedNextAction($project, $completeness);
@endphp
<div class="admin-status">
    <p class="admin-status-count">{{ $completeness['score'] }}/{{ $completeness['total'] }} checks complete</p>
    <div class="admin-status-next">
        <span class="admin-status-next-label">Recommended next</span>
        <span class="admin-status-next-value">{{ $next['label'] }}</span>
    </div>
    <a href="#{{ $next['anchor'] }}" class="btn btn-secondary btn--compact w-full justify-center" data-scroll-anchor="{{ $next['anchor'] }}">
        Open Review
    </a>
</div>
