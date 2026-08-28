@extends('admin._layout', ['title' => 'Experience'])

@section('content')
    <div class="flex items-center justify-between gap-4 mb-6 flex-wrap">
        <h1 class="text-subheading font-extrabold text-ink">Experience</h1>
        <a href="{{ route('admin.experiences.create') }}" class="btn btn-primary btn--compact">+ New Experience</a>
    </div>

    <div class="admin-table-wrap hidden lg:block">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 160px;">Period</th>
                    <th>Role</th>
                    <th>Organization / Company</th>
                    <th style="width: 140px;">Type</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($experiences as $experience)
                    <tr>
                        <td class="text-muted">{{ $experience->duration }}</td>
                        <td class="font-semibold text-ink">{{ $experience->role }}</td>
                        <td class="text-muted">{{ $experience->company }}</td>
                        <td><span class="admin-badge">{{ $experience->category }}</span></td>
                        <td class="text-right whitespace-nowrap">
                            <a href="{{ route('admin.experiences.edit', $experience) }}" class="text-small font-semibold text-primary hover:underline">Edit</a>
                            <span class="text-border-light mx-1.5">&middot;</span>
                            <form method="POST" action="{{ route('admin.experiences.destroy', $experience) }}" class="inline"
                                  data-confirm-delete data-confirm-title="&quot;{{ $experience->role }} @ {{ $experience->company }}&quot;">
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
                                <p>No experiences yet.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="lg:hidden space-y-3">
        @forelse ($experiences as $experience)
            <div class="admin-table-wrap p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-semibold text-ink truncate">{{ $experience->role }}</p>
                        <p class="text-meta text-muted">{{ $experience->company }} &middot; {{ $experience->duration }}</p>
                        <span class="admin-badge mt-1">{{ $experience->category }}</span>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-3">
                    <a href="{{ route('admin.experiences.edit', $experience) }}" class="text-small font-semibold text-primary">Edit</a>
                    <form method="POST" action="{{ route('admin.experiences.destroy', $experience) }}"
                          data-confirm-delete data-confirm-title="&quot;{{ $experience->role }} @ {{ $experience->company }}&quot;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-small font-semibold" style="color:#DC2626;">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="admin-table-wrap">
                <div class="admin-empty"><p>No experiences yet.</p></div>
            </div>
        @endforelse
    </div>
@endsection
