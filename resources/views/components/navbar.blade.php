<header class="absolute inset-x-0 top-0 z-50">
    @php
        $homeUrl = route('home');
        $isHome = request()->routeIs('home');
    @endphp

    <nav class="section-shell flex items-center justify-between py-6 text-white/90">
        <a href="{{ $isHome ? '#home' : $homeUrl . '#home' }}" class="flex items-center gap-3 text-lg font-semibold tracking-[0.25em] text-white uppercase">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/40 bg-white/5 text-xs font-medium tracking-[0.2em] text-[#ebd3a4]">DA</span>
            <span class="hidden sm:inline">Dar Anawat</span>
        </a>

        <div class="hidden items-center gap-5 text-sm font-medium uppercase tracking-[0.2em] text-white/80 md:flex">
            <a href="{{ $isHome ? '#home' : $homeUrl . '#home' }}" class="transition hover:text-[#ebd3a4]">{{ __('navigation.home') }}</a>
            <a href="{{ $isHome ? '#about' : $homeUrl . '#about' }}" class="transition hover:text-[#ebd3a4]">{{ __('navigation.about') }}</a>
            <a href="{{ route('chambres') }}">{{ __('navigation.rooms') }}</a>
            <a href="{{ $isHome ? '#experiences' : $homeUrl . '#experiences' }}" class="transition hover:text-[#ebd3a4]">{{ __('navigation.experiences') }}</a>
            <a href="{{ $isHome ? '#discover' : $homeUrl . '#discover' }}" class="transition hover:text-[#ebd3a4]">{{ __('navigation.morocco') }}</a>
        </div>

        <div class="flex items-center gap-3">
            <div class="flex items-center gap-1.5 text-xs font-medium tracking-[0.12em] text-white/80 sm:gap-2" role="group" aria-label="{{ __('navigation.language_selector') }}">
                @foreach (['fr' => 'FR', 'en' => 'EN', 'ar' => 'AR'] as $locale => $label)
                    <a href="{{ route('language.switch', ['locale' => $locale]) }}" lang="{{ $locale }}" hreflang="{{ $locale }}" @if (app()->isLocale($locale)) aria-current="page" @endif class="transition hover:text-[#ebd3a4] {{ app()->isLocale($locale) ? 'text-[#ebd3a4]' : '' }}">{{ $label }}</a>
                    @if (! $loop->last)<span aria-hidden="true">|</span>@endif
                @endforeach
            </div>
            <a href="{{ $isHome ? '#cta' : $homeUrl . '#cta' }}" class="gold-button hidden sm:inline-flex">{{ __('navigation.book') }}</a>
        </div>
    </nav>
</header>
