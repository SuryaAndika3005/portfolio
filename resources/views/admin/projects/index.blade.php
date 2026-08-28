@extends('admin._layout', ['title' => 'Projects'])

@section('content')
    <div class="flex items-center justify-between gap-4 mb-6 flex-wrap">
        <h1 class="text-subheading font-extrabold text-ink">Projects</h1>
        <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn--compact">+ New Project</a>
    </div>

    <form method="GET" action="{{ route('admin.projects.index') }}" class="flex flex-wrap items-center gap-3 mb-5">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by title…"
               class="admin-input" style="max-width: 240px;">
        <select id="admin-filter-category" name="category" class="admin-input" style="max-width: 200px;">
            <option value="">All categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-secondary btn--compact">Search</button>
        @if (request('q') || request('category'))
            <a href="{{ route('admin.projects.index') }}" class="btn-text btn--compact">Clear</a>
        @endif
    </form>

    {{-- Desktop/tablet: compact table. --}}
    <div class="admin-table-wrap hidden lg:block">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 76px;">Cover</th>
                    <th>Project</th>
                    <th>Category</th>
                    <th>Updated</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($projects as $project)
                    <tr>
                        <td>
                            <img src="{{ asset('storage/' . $project->coverImagePath()) }}" alt="" class="admin-row-thumb">
                        </td>
                        <td>
                            <p class="font-semibold text-ink">{{ $project->title }}</p>
                        </td>
                        <td class="text-muted">{{ $project->category->name ?? '—' }}</td>
                        <td class="text-muted">{{ $project->updated_at->diffForHumans() }}</td>
                        <td class="text-right whitespace-nowrap">
                            <a href="{{ route('admin.projects.edit', $project) }}" class="text-small font-semibold text-primary hover:underline">Edit</a>
                            <span class="text-border-light mx-1.5">&middot;</span>
                            <a href="{{ route('portfolio.show', $project->id) }}" target="_blank" rel="noopener" class="text-small font-semibold text-muted hover:text-ink">View ↗</a>
                            <span class="text-border-light mx-1.5">&middot;</span>
                            <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" class="inline"
                                  data-confirm-delete data-confirm-title="&quot;{{ $project->title }}&quot;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-small font-semibold" style="color:#DC2626;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="admin-empty">
                                <p>No projects yet.</p>
                                <p>Create your first project to get started.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile: stacked rows instead of a horizontally-scrolling table. --}}
    <div class="lg:hidden space-y-3">
        @forelse ($projects as $project)
            <div class="admin-table-wrap p-4 flex items-center gap-3">
                <img src="{{ asset('storage/' . $project->coverImagePath()) }}" alt="" class="admin-row-thumb" style="width: 64px; height: 48px;">
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-ink truncate">{{ $project->title }}</p>
                    <p class="text-meta text-muted">{{ $project->category->name ?? '—' }} &middot; {{ $project->updated_at->diffForHumans() }}</p>
                    <div class="mt-2 flex items-center gap-3">
                        <a href="{{ route('admin.projects.edit', $project) }}" class="text-small font-semibold text-primary">Edit</a>
                        <a href="{{ route('portfolio.show', $project->id) }}" target="_blank" rel="noopener" class="text-small font-semibold text-muted">View ↗</a>
                        <form method="POST" action="{{ route('admin.projects.destroy', $project) }}"
                              data-confirm-delete data-confirm-title="&quot;{{ $project->title }}&quot;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-small font-semibold" style="color:#DC2626;">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="admin-table-wrap">
                <div class="admin-empty">
                    <p>No projects yet.</p>
                    <p>Create your first project to get started.</p>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $projects->links() }}
    </div>
@endsection
