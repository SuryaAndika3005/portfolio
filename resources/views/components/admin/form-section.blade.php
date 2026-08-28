@props(['title'])

<div class="admin-form-section">
    <p class="admin-form-section-title">{{ $title }}</p>
    {{ $slot }}
</div>
