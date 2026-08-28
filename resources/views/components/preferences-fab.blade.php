{{-- Floating Preferences control -- the single public access point for
     Language + Theme (see the Batch brief: "REMOVE FROM TOPBAR" /
     "FLOATING PREFERENCES FAB"). Rendered once here in layout.blade.php,
     not inside <x-nav>, so it never gets duplicated across the nav's 3
     topbar variants the way the old inline switcher was. Reuses the exact
     same localization/theme mechanisms as before -- language links still
     hit LocaleController via lang.switch, and every [data-theme-option]
     button is still wired up generically by theme.js's own
     querySelectorAll, so no business logic is duplicated here. Not
     rendered in Admin -- Admin has its own separate layout
     (admin/_layout.blade.php) that never includes this component. --}}
<div id="prefsFab" class="prefs-fab">
    <button type="button" id="prefsFabBtn" class="prefs-fab-btn"
        aria-expanded="false" aria-controls="prefsFabPopover"
        aria-label="{{ __('Preferences') }}">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <line x1="4" y1="7" x2="20" y2="7"></line>
            <circle cx="9" cy="7" r="2" fill="currentColor" stroke="none"></circle>
            <line x1="4" y1="12" x2="20" y2="12"></line>
            <circle cx="15" cy="12" r="2" fill="currentColor" stroke="none"></circle>
            <line x1="4" y1="17" x2="20" y2="17"></line>
            <circle cx="11" cy="17" r="2" fill="currentColor" stroke="none"></circle>
        </svg>
    </button>

    {{-- First-visit discovery hint -- NOT a modal/splash, just a small
         one-time bubble pointing at the FAB. Rendered hidden by default and
         only revealed by preferences-fab.js after checking a dedicated
         localStorage flag (never inferred from the theme/locale VALUE
         itself -- see the brief's explicit warning against that). Nested
         inside #prefsFab so it inherits the exact same .prefs-fab--suppressed
         visibility:hidden rule the FAB itself already uses for the gallery
         modal / mobile nav, with no extra JS wiring needed. --}}
    <div id="prefsFabHint" class="prefs-fab-hint" hidden>
        <button type="button" id="prefsFabHintDismiss" class="prefs-fab-hint-dismiss" aria-label="{{ __('Dismiss') }}">&times;</button>
        <p class="prefs-fab-hint-title">{{ __('Customize your experience') }}</p>
        <p class="prefs-fab-hint-subtitle">{{ __('Language') }} &middot; {{ __('Appearance') }}</p>
    </div>

    <div id="prefsFabPopover" class="prefs-fab-popover" hidden>
        <div>
            <p class="prefs-fab-heading">{{ __('Language') }}</p>
            <div class="prefs-fab-options" role="group" aria-label="{{ __('Language') }}">
                <a href="{{ route('lang.switch', ['locale' => 'en', 'next' => request()->getRequestUri()]) }}"
                    class="prefs-fab-option" aria-current="{{ app()->getLocale() === 'en' ? 'true' : 'false' }}"
                    aria-label="{{ __('Switch language to English') }}">EN</a>
                <a href="{{ route('lang.switch', ['locale' => 'id', 'next' => request()->getRequestUri()]) }}"
                    class="prefs-fab-option" aria-current="{{ app()->getLocale() === 'id' ? 'true' : 'false' }}"
                    aria-label="{{ __('Switch language to Bahasa Indonesia') }}">ID</a>
            </div>
        </div>

        <div>
            <p class="prefs-fab-heading">{{ __('Appearance') }}</p>
            <div class="prefs-fab-options prefs-fab-options--stacked" role="group" aria-label="{{ __('Theme') }}">
                <button type="button" data-theme-option="system" class="prefs-fab-option" aria-pressed="false"
                    aria-label="{{ __('Use system theme') }}">
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="13" rx="2"/><path stroke-linecap="round" d="M8 21h8M12 17v4"/></svg>
                    <span>{{ __('System') }}</span>
                </button>
                <button type="button" data-theme-option="light" class="prefs-fab-option" aria-pressed="false"
                    aria-label="{{ __('Use light theme') }}">
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path stroke-linecap="round" d="M12 2v2m0 16v2M4.2 4.2l1.4 1.4m12.8 12.8l1.4 1.4M2 12h2m16 0h2M4.2 19.8l1.4-1.4M18.4 5.6l1.4-1.4"/></svg>
                    <span>{{ __('Light') }}</span>
                </button>
                <button type="button" data-theme-option="dark" class="prefs-fab-option" aria-pressed="false"
                    aria-label="{{ __('Use dark theme') }}">
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.5A8.5 8.5 0 1111.5 3a7 7 0 009.5 9.5z"/></svg>
                    <span>{{ __('Dark') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>
