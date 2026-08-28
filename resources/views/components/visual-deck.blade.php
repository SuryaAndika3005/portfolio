@props(['slides', 'title' => ''])

@php
    // Every image on the project ($allSlides, passed straight through from
    // show.blade.php — no second gallery dataset), rendered as absolutely
    // stacked cards. This only outputs the cards themselves: positioning
    // (active/full-color/centered vs. grayscale/scaled/peeking) is done by
    // visual-deck.js, and the counter/prev/next/close/zoom chrome around
    // this lives in the fullscreen modal markup that hosts this component
    // (see #imageModal in show.blade.php), not here.
    $slides = collect($slides)->filter()->values();
    $count = $slides->count();
@endphp

@foreach ($slides as $i => $src)
    <div data-deck-card
         data-index="{{ $i }}"
         aria-hidden="{{ $i === 0 ? 'false' : 'true' }}"
         class="modal-deck-card absolute inset-0 m-auto w-[88vw] h-[68vh] sm:h-[74vh] md:w-[74vw] md:h-[80vh] lg:w-[64vw] flex items-center justify-center overflow-hidden">
        <img data-zoom-img
             src="{{ $src }}"
             decoding="async"
             loading="{{ $i === 0 ? 'eager' : 'lazy' }}"
             class="max-w-full max-h-full w-auto h-auto object-contain select-none rounded-lg shadow-2xl cursor-zoom-in"
             draggable="false"
             alt="{{ __(':title showcase :current of :total', ['title' => $title, 'current' => $i + 1, 'total' => $count]) }}">
    </div>
@endforeach
