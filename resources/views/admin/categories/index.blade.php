@extends('admin._layout', ['title' => 'Categories'])

@section('content')
    <div class="flex items-center justify-between gap-4 mb-2 flex-wrap">
        <h1 class="text-subheading font-extrabold text-ink">Categories</h1>
    </div>
    <p class="text-small text-muted mb-6 max-w-2xl">
        Creating new categories is disabled for now — the homepage's Selected Works section only recognizes the three categories it already has panels for. Names can be renamed freely; slugs stay fixed because the public site's category-aware layout depends on them.
    </p>

    <div class="admin-table-wrap hidden lg:block">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Slug</th>
                    <th>Projects</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td class="font-semibold text-ink">{{ $category->name }}</td>
                        <td class="text-muted font-mono text-meta">{{ $category->slug }}</td>
                        <td class="text-muted">{{ $category->projects_count }}</td>
                        <td class="text-right whitespace-nowrap">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-small font-semibold text-primary hover:underline">Edit</a>
                            @if ($category->projects_count === 0)
                                <span class="text-border-light mx-1.5">&middot;</span>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline"
                                      data-confirm-delete data-confirm-title="&quot;{{ $category->name }}&quot;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-small font-semibold" style="color:#DC2626;">Delete</button>
                                </form>
                            @else
                                <span class="text-border-light mx-1.5">&middot;</span>
                                <span class="text-small text-muted">Protected by {{ $category->projects_count }} {{ Str::plural('project', $category->projects_count) }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="admin-empty">
                                <p>No categories available.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="lg:hidden space-y-3">
        @forelse ($categories as $category)
            <div class="admin-table-wrap p-4">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-semibold text-ink truncate">{{ $category->name }}</p>
                        <p class="text-meta text-muted font-mono">{{ $category->slug }} &middot; {{ $category->projects_count }} projects</p>
                    </div>
                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-small font-semibold text-primary shrink-0">Edit</a>
                </div>
                @if ($category->projects_count === 0)
                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="mt-2"
                          data-confirm-delete data-confirm-title="&quot;{{ $category->name }}&quot;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-small font-semibold" style="color:#DC2626;">Delete</button>
                    </form>
                @else
                    <p class="mt-2 text-small text-muted">Protected by {{ $category->projects_count }} {{ Str::plural('project', $category->projects_count) }}</p>
                @endif
            </div>
        @empty
            <div class="admin-table-wrap">
                <div class="admin-empty"><p>No categories available.</p></div>
            </div>
        @endforelse
    </div>
@endsection
