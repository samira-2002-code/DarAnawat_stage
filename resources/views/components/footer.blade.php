<footer id="contact" class="border-t border-[#d9ceb1] bg-[#112b29] text-[#f7f3ed]">
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
                <li><a href="#contact" class="transition hover:text-white">{{ __('site.footer.contact') }}</a></li>
            </ul>
        </div>

        <div>
            <p class="mb-4 text-xs font-semibold uppercase tracking-[0.22em] text-[#c9a76c]">{{ __('site.footer.contact') }}</p>
            <ul class="space-y-3 text-sm text-[#e7dfd2]">
                <li>
                    <a href="tel:+212763099428" class="transition hover:text-white" dir="ltr">+212 763-099428</a>
                </li>
                <li>
                    <a href="mailto:www.dar.ennawat@gmail.com" class="break-words transition hover:text-white">www.dar.ennawat@gmail.com</a>
                </li>
                <li>{{ __('site.footer.location') }}</li>
            </ul>
        </div>

        <div>
            <p class="mb-4 text-xs font-semibold uppercase tracking-[0.22em] text-[#c9a76c]">{{ __('site.footer.follow') }}</p>
            <div class="flex flex-col items-start gap-4">
                <a href="https://www.instagram.com/riaddarennawat/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-3 text-sm text-[#e7dfd2] transition hover:text-white" aria-label="{{ __('site.footer.instagram') }}">
                    <svg aria-hidden="true" viewBox="0 0 24 24" class="h-5 w-5 fill-none stroke-current" stroke-width="1.7">
                        <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                        <circle cx="12" cy="12" r="4"></circle>
                        <circle cx="18" cy="6" r="1" class="fill-current stroke-none"></circle>
                    </svg>
                    <span>{{ __('site.footer.instagram') }}</span>
                </a>
                <a href="https://wa.me/212763099428" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-full border border-[#c9a76c]/50 px-4 py-2 text-sm text-[#ebd3a4] transition hover:bg-white/10 hover:text-white">
                    {{ __('site.footer.whatsapp') }}
                </a>
            </div>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="section-shell flex flex-col items-center justify-between gap-3 py-5 text-sm text-[#d9d2c4] md:flex-row">
            <span>{{ __('site.footer.copyright') }}</span>
            <span>{{ __('site.footer.tagline') }}</span>
        </div>
    </div>
</footer>
