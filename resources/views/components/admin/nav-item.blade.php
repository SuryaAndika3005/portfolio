@props(['href', 'active' => false])

<a href="{{ $href }}" class="admin-nav-item {{ $active ? 'is-active' : '' }}" @if ($active) aria-current="page" @endif>
    {{ $icon }}
    <span>{{ $slot }}</span>
</a>
