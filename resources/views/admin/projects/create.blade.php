@extends('admin._layout', ['title' => 'New Project'])

@section('content')
    <div class="flex items-center justify-between gap-2 mb-6 flex-wrap">
        <div class="flex items-center gap-2 text-small text-muted">
            <a href="{{ route('admin.projects.index') }}" class="hover:text-primary">Projects</a>
            <span>/</span>
            <span class="text-ink font-semibold">New</span>
        </div>
        <x-admin.project-assistant :project="null" />
    </div>

    <form id="admin-project-form" method="POST" action="{{ route('admin.projects.store') }}" enctype="multipart/form-data">
        @include('admin.projects._form')
    </form>
@endsection
