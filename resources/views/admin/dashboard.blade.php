@extends('admin._layout', ['title' => 'Overview'])

@section('content')
    @php
        $hour = now()->hour;
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
    @endphp

    <div class="flex items-center justify-between gap-4 mb-8 flex-wrap">
        <div>
            <p class="text-eyebrow font-bold uppercase tracking-widest text-muted mb-1">{{ $greeting }}</p>
            <h1 class="text-subheading font-extrabold text-ink">Portfolio overview</h1>
        </div>
        <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn--compact">+ New Project</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="admin-stat">
            <p class="admin-stat-label">Projects</p>
            <p class="admin-stat-value">{{ $totalProjects }} total</p>
        </div>
        <div class="admin-stat">
            <p class="admin-stat-label">Categories</p>
            <p class="admin-stat-value">{{ $totalCategories }}</p>
        </div>
        <div class="admin-stat">
            <p class="admin-stat-label">Gallery Assets</p>
            <p class="admin-stat-value">{{ $totalGalleryImages }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <div class="lg:col-span-2 admin-form-section">
            <p class="admin-form-section-title">Projects by Category</p>
            <div class="space-y-3">
                @forelse ($projectsByCategory as $category)
                    <div class="flex items-center justify-between text-small">
                        <span class="text-ink font-semibold">{{ $category->name }}</span>
                        <span class="text-muted">{{ $category->projects_count }}</span>
                    </div>
                @empty
                    <p class="text-small text-muted">No categories yet.</p>
                @endforelse
            </div>
        </div>

        <div class="lg:col-span-3 admin-form-section">
            <div class="flex items-center justify-between mb-1">
                <p class="admin-form-section-title !mb-0">Recent Projects</p>
                <a href="{{ route('admin.projects.index') }}" class="btn-text btn--compact">View all</a>
            </div>
            <div class="divide-y divide-border-light">
                @forelse ($recentProjects as $project)
                    <div class="flex items-center gap-3 py-3">
                        <img src="{{ asset('storage/' . $project->coverImagePath()) }}" alt="" class="admin-row-thumb">
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('admin.projects.edit', $project) }}" class="text-small font-semibold text-ink hover:text-primary transition-colors duration-[var(--motion-fast)] truncate block">
                                {{ $project->title }}
                            </a>
                            <p class="text-meta text-muted">{{ $project->category->name ?? '—' }} &middot; {{ $project->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="admin-empty">
                        <p>No projects yet.</p>
                        <p>Create your first project to see it here.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
