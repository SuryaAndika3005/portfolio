@extends('admin._layout', ['title' => 'Add Project'])

@section('content')
    <h1 class="text-2xl font-black text-slate-900 mb-8">Add Project</h1>

    <form method="POST" action="{{ route('admin.projects.store') }}" enctype="multipart/form-data"
          class="bg-white rounded-2xl border border-slate-200 p-8">
        @include('admin.projects._form')
    </form>
@endsection
