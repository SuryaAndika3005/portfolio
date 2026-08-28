@props(['label' => null, 'name' => null, 'hint' => null, 'for' => null])

<div>
    @if ($label)
        <label @if ($for) for="{{ $for }}" @endif class="admin-field-label">{{ $label }}</label>
    @endif
    @if ($hint)
        <p class="admin-field-hint">{{ $hint }}</p>
    @endif

    {{ $slot }}

    @if ($name)
        @error($name)
            <p class="admin-field-error">{{ $message }}</p>
        @enderror
    @endif
</div>
