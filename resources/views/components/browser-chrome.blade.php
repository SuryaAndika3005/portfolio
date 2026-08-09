@props(['label' => '', 'accent' => 'it'])

<div {{ $attributes->merge(['class' => 'rounded-[1.5rem] sm:rounded-[2rem] overflow-hidden border border-slate-200 bg-white shadow-xl']) }}>
    @switch($accent)
        @case('uiux')
            <div class="flex items-center gap-2 px-4 sm:px-5 py-3 bg-violet-50 border-b border-violet-100">
                <span class="w-2.5 h-2.5 rounded-full bg-violet-300"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-violet-400"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-violet-500"></span>
                <span class="ml-3 flex-1 bg-white/70 border border-violet-100 rounded-full px-3 py-1 text-[11px] font-semibold text-violet-700 truncate text-center">
                    {{ $label }}
                </span>
            </div>
            @break
        @default
            <div class="flex items-center gap-2 px-4 sm:px-5 py-3 bg-emerald-50 border-b border-emerald-100">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-300"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <span class="ml-3 flex-1 bg-white/70 border border-emerald-100 rounded-full px-3 py-1 text-[11px] font-semibold text-emerald-700 truncate text-center">
                    {{ $label }}
                </span>
            </div>
    @endswitch

    {{ $slot }}
</div>
