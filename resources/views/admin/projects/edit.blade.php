@extends('admin._layout', ['title' => 'Edit Project'])

@section('content')
    <div class="flex items-center justify-between gap-2 mb-6 flex-wrap">
        <div class="flex items-center gap-2 text-small text-muted min-w-0">
            <a href="{{ route('admin.projects.index') }}" class="hover:text-primary">Projects</a>
            <span>/</span>
            <span class="text-ink font-semibold truncate">{{ $project->title }}</span>
        </div>
        <x-admin.project-assistant :project="$project" />
    </div>

    <form id="admin-project-form" method="POST" action="{{ route('admin.projects.update', $project) }}" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.projects._form')
    </form>
@endsection
