@extends('admin._layout', ['title' => 'Projects'])

@section('content')
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-black text-slate-900">Projects</h1>
        <a href="{{ route('admin.projects.create') }}" class="bg-slate-900 text-white text-sm font-bold px-4 py-2.5 rounded-xl hover:bg-slate-800">
            + Add Project
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-5 py-3 font-semibold">Project</th>
                    <th class="px-5 py-3 font-semibold">Category</th>
                    <th class="px-5 py-3 font-semibold">Highlighted</th>
                    <th class="px-5 py-3 font-semibold">Added</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($projects as $project)
                    <tr>
                        <td class="px-5 py-3 font-semibold text-slate-800">{{ $project->title }}</td>
                        <td class="px-5 py-3 text-slate-500">{{ $project->category->name ?? '—' }}</td>
                        <td class="px-5 py-3">
                            @if ($project->is_highlighted)
                                <span class="inline-flex items-center bg-blue-50 text-blue-600 text-xs font-bold px-2.5 py-1 rounded-full">
                                    Highlighted @if($project->featured_order !== null) (#{{ $project->featured_order }}) @endif
                                </span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-slate-500">{{ $project->created_at->diffForHumans() }}</td>
                        <td class="px-5 py-3 text-right space-x-3">
                            <a href="{{ route('admin.projects.edit', $project) }}" class="text-blue-600 font-semibold hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" class="inline"
                                  onsubmit="return confirm('Delete this project? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 font-semibold hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-slate-400">No projects yet — add your first one.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $projects->links() }}
    </div>
@endsection
