<footer class="border-t border-[#d9ceb1] bg-[#112b29] text-[#f7f3ed]">
    @php
        $homeUrl = route('home');
        $isHome = request()->routeIs('home');
    @endphp

    <div class="section-shell grid gap-10 py-14 md:grid-cols-2 lg:grid-cols-5">
        <div class="lg:col-span-2">
            <p class="mb-4 text-xs font-semibold uppercase tracking-[0.25em] text-[#c9a76c]">Dar Anawat</p>
            <h3 class="font-display text-4xl text-white">{{ __('site.footer.title') }}</h3>
            <p class="mt-4 max-w-md text-base leading-7 text-[#d9d2c4]">
                {{ __('site.footer.description') }}
            </p>
        </div>

        <div>
            <p class="mb-4 text-xs font-semibold uppercase tracking-[0.22em] text-[#c9a76c]">{{ __('site.footer.navigation') }}</p>
            <ul class="space-y-3 text-sm text-[#e7dfd2]">
                <li><a href="{{ $isHome ? '#home' : $homeUrl . '#home' }}" class="transition hover:text-white">{{ __('navigation.home') }}</a></li>
                <li><a href="{{ $isHome ? '#about' : $homeUrl . '#about' }}" class="transition hover:text-white">{{ __('navigation.about') }}</a></li>
                <li><a href="{{ route('chambres') }}" class="transition hover:text-white">{{ __('navigation.rooms') }}</a></li>
                <li><a href="{{ $isHome ? '#experiences' : $homeUrl . '#experiences' }}" class="transition hover:text-white">{{ __('navigation.experiences') }}</a></li>
            </ul>
        </div>

        <div>
            <p class="mb-4 text-xs font-semibold uppercase tracking-[0.22em] text-[#c9a76c]">{{ __('site.footer.contact') }}</p>
            <ul class="space-y-3 text-sm text-[#e7dfd2]">
                <li>+212 5 24 00 00 00</li>
                <li>hello@daranawat.ma</li>
                <li>{{ __('site.footer.location') }}</li>
            </ul>
        </div>

        <div>
            <p class="mb-4 text-xs font-semibold uppercase tracking-[0.22em] text-[#c9a76c]">{{ __('site.footer.follow') }}</p>
            <ul class="space-y-3 text-sm text-[#e7dfd2]">
                <li>Instagram</li>
                <li>Facebook</li>
                <li>Pinterest</li>
            </ul>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="section-shell flex flex-col items-center justify-between gap-3 py-5 text-sm text-[#d9d2c4] md:flex-row">
            <span>{{ __('site.footer.copyright') }}</span>
            <span>{{ __('site.footer.tagline') }}</span>
        </div>
    </div>
</footer>
