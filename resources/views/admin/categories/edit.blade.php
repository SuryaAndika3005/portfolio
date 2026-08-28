@extends('admin._layout', ['title' => 'Edit Category'])

@section('content')
    <div class="flex items-center gap-2 mb-6 text-small text-muted">
        <a href="{{ route('admin.categories.index') }}" class="hover:text-primary">Categories</a>
        <span>/</span>
        <span class="text-ink font-semibold">{{ $category->name }}</span>
    </div>

    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="max-w-lg">
        @csrf
        @method('PUT')

        <x-admin.form-section title="Category">
            <div class="space-y-5">
                <x-admin.field label="Name" for="name" name="name">
                    <input id="name" type="text" name="name" value="{{ old('name', $category->name) }}" required
                           class="admin-input {{ $errors->has('name') ? 'has-error' : '' }}">
                </x-admin.field>

                <x-admin.field label="Slug" hint="Used by the public portfolio layout — read-only.">
                    <input type="text" value="{{ $category->slug }}" disabled
                           class="admin-input" style="background-color: var(--color-canvas); color: var(--color-muted); cursor: not-allowed;">
                </x-admin.field>
            </div>
        </x-admin.form-section>

        <div class="mt-8 flex items-center gap-3">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('admin.categories.index') }}" class="btn-text">Cancel</a>
        </div>
    </form>
@endsection
