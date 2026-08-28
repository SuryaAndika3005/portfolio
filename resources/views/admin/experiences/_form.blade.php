@csrf

@php
    $categoryOptions = \App\Http\Requests\Admin\ExperienceRequest::CATEGORIES;
@endphp

<div class="max-w-2xl space-y-6">

    <x-admin.form-section title="Experience">
        <div class="space-y-5">
            <x-admin.field label="Role" for="role" name="role">
                <input id="role" type="text" name="role" value="{{ old('role', $experience->role ?? '') }}" required
                       class="admin-input {{ $errors->has('role') ? 'has-error' : '' }}">
            </x-admin.field>

            <x-admin.field label="Organization / Company" for="company" name="company">
                <input id="company" type="text" name="company" value="{{ old('company', $experience->company ?? '') }}" required
                       class="admin-input {{ $errors->has('company') ? 'has-error' : '' }}">
            </x-admin.field>

            <x-admin.field label="Type" for="category" name="category"
                hint="Professional Work gets its own list on the homepage; Organization and Events are both grouped under Leadership &amp; Organizations.">
                <select id="category" name="category" required class="admin-input {{ $errors->has('category') ? 'has-error' : '' }}">
                    @foreach ($categoryOptions as $option)
                        <option value="{{ $option }}" @selected(old('category', $experience->category ?? '') === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </x-admin.field>
        </div>
    </x-admin.form-section>

    <x-admin.form-section title="Period">
        <x-admin.field label="Duration" for="duration" name="duration"
            hint="Free text, exactly as shown on the site. Use &quot;Present&quot; for an ongoing role, e.g. &quot;Jun 2025 - Present&quot;.">
            <input id="duration" type="text" name="duration" value="{{ old('duration', $experience->duration ?? '') }}" required
                   placeholder="e.g. Jun 2025 - Present" class="admin-input {{ $errors->has('duration') ? 'has-error' : '' }}">
        </x-admin.field>
    </x-admin.form-section>

    <x-admin.form-section title="Details">
        <x-admin.field label="Description" for="description" name="description"
            hint="Optional. Only Professional Work entries with a description get an expandable detail on the homepage.">
            <textarea id="description" name="description" rows="4" class="admin-input">{{ old('description', $experience->description ?? '') }}</textarea>
        </x-admin.field>
    </x-admin.form-section>

</div>

<div class="mt-8 flex items-center gap-3">
    <button type="submit" class="btn btn-primary">
        {{ isset($experience) ? 'Save Changes' : 'Create Experience' }}
    </button>
    <a href="{{ route('admin.experiences.index') }}" class="btn-text">Cancel</a>
</div>
