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
            <a href="{{ $isHome ? '#home' : $homeUrl . '#home' }}" class="transition hover:text-[#ebd3a4]">Home</a>
            <a href="{{ $isHome ? '#about' : $homeUrl . '#about' }}" class="transition hover:text-[#ebd3a4]">About</a>
            <a href="{{ $isHome ? '#rooms' : $homeUrl . '#rooms' }}" class="transition hover:text-[#ebd3a4]">Rooms</a>
            <a href="{{ route('chambres') }}" class="transition hover:text-[#ebd3a4]">Chambres</a>
            <a href="{{ $isHome ? '#experiences' : $homeUrl . '#experiences' }}" class="transition hover:text-[#ebd3a4]">Experiences</a>
            <a href="{{ $isHome ? '#discover' : $homeUrl . '#discover' }}" class="transition hover:text-[#ebd3a4]">Morocco</a>
        </div>

        <a href="{{ $isHome ? '#cta' : $homeUrl . '#cta' }}" class="gold-button hidden sm:inline-flex">Book Your Escape</a>
    </nav>
</header>
