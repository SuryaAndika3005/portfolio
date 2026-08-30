@props(['data'])
{{-- Generic JSON-LD emitter: any page pushes its own @type payload(s) via
     @push('json-ld') and renders them through this one component, so the
     escaping/encoding rule (unescaped slashes so image/URL values stay
     readable, unescaped unicode, no HTML-escaping of a script body) lives
     in exactly one place rather than being repeated at every call site. --}}
<script type="application/ld+json">{!! json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
