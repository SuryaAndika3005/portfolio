@extends('admin._layout', ['title' => 'New Experience'])

@section('content')
    <div class="flex items-center gap-2 mb-6 text-small text-muted">
        <a href="{{ route('admin.experiences.index') }}" class="hover:text-primary">Experience</a>
        <span>/</span>
        <span class="text-ink font-semibold">New</span>
    </div>

    <form method="POST" action="{{ route('admin.experiences.store') }}">
        @include('admin.experiences._form')
    </form>
@endsection
