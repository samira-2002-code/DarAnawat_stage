@extends('layouts.app')

@section('title', __('site.rooms.' . $room['slug'] . '.name') . ' | Dar Anawat')

@section('content')
    <main class="overflow-hidden bg-[#f9f5ef] text-[#1d2d2a]">
        <section class="hero relative overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('{{ $room['image'] }}');"></div>
            <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(10,18,16,0.88),rgba(10,18,16,0.48))]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(200,165,90,0.2),transparent_34%)]"></div>

            <div class="relative z-10">
                <x-navbar />

                <div class="section-shell flex min-h-[440px] items-end pb-14 pt-36 md:min-h-[480px] md:pb-20">
                    <div class="max-w-3xl text-white">
                        <nav aria-label="{{ __('site.details.breadcrumb.rooms') }}" class="mb-7 text-sm text-white/70">
                            <ol class="flex flex-wrap items-center gap-2">
                                <li><a href="{{ route('home') }}" class="transition hover:text-[#ebd3a4]">{{ __('site.details.breadcrumb.home') }}</a></li>
                                <li aria-hidden="true">/</li>
                                <li><a href="{{ route('chambres') }}" class="transition hover:text-[#ebd3a4]">{{ __('site.details.breadcrumb.rooms') }}</a></li>
                                <li aria-hidden="true">/</li>
                                <li aria-current="page" class="text-[#e2c88c]">{{ __('site.rooms.' . $room['slug'] . '.name') }}</li>
                            </ol>
                        </nav>
                        <p class="mb-5 text-xs font-semibold uppercase tracking-[0.35em] text-[#e2c88c]">{{ __('site.details.hero_kicker') }}</p>
                        <h1 class="font-display text-5xl leading-[0.95] text-white md:text-7xl">{{ __('site.rooms.' . $room['slug'] . '.name') }}</h1>
                        <p class="mt-6 max-w-2xl text-lg leading-8 text-white/80 md:text-xl">
                            {{ __('site.details.hero_description') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-shell py-12 md:py-16">
            <div class="grid gap-3 overflow-hidden rounded-[2rem] border border-[#e8dcc0] bg-[#fffdf9] p-3 shadow-[var(--shadow)] md:h-[480px] md:grid-cols-[1.5fr_1fr_1fr] md:grid-rows-2">
                @foreach ($room['gallery'] as $index => $image)
                    <div class="{{ $index === 0 ? 'md:row-span-2' : '' }} group relative min-h-52 overflow-hidden rounded-[1.35rem] {{ $index === 0 ? 'md:min-h-0' : 'md:min-h-0' }}">
                        <img
                            src="{{ $image }}"
                            alt="{{ __('site.rooms.' . $room['slug'] . '.name') }} — {{ __('site.details.gallery_alt') }} {{ $index + 1 }}"
                            class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-[1.03]"
                        >
                    </div>
                @endforeach
            </div>
        </section>

        <section class="section-shell pb-16 md:pb-24">
            <div class="grid gap-10 lg:grid-cols-[1.35fr_0.65fr] lg:gap-14">
                <div>
                    <p class="section-kicker">{{ __('site.details.about_kicker') }}</p>
                    <h2 class="section-title text-4xl leading-tight md:text-6xl">{{ __('site.rooms.' . $room['slug'] . '.name') }}</h2>
                    <p class="mt-6 text-base leading-8 text-[#4d5b56] md:text-lg">{{ __('site.rooms.' . $room['slug'] . '.description') }}</p>

                    <dl class="mt-8 grid gap-4 border-y border-[#e3d5b0] py-6 sm:grid-cols-3">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-[0.16em] text-[#a67b3f]">{{ __('site.common.capacity') }}</dt>
                            <dd class="mt-2 text-[#3b4b45]">👤 {{ __('site.rooms.' . $room['slug'] . '.capacity') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-[0.16em] text-[#a67b3f]">{{ __('site.common.bed_type') }}</dt>
                            <dd class="mt-2 text-[#3b4b45]">🛏 {{ __('site.rooms.' . $room['slug'] . '.bed') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-[0.16em] text-[#a67b3f]">{{ __('site.common.surface') }}</dt>
                            <dd class="mt-2 text-[#3b4b45]">{{ $room['surface'] }}</dd>
                        </div>
                    </dl>

                    <div class="mt-10">
                        <p class="section-kicker">{{ __('site.details.amenities_kicker') }}</p>
                        <h2 class="font-display text-4xl text-[#122b29]">{{ __('site.details.amenities_title') }}</h2>
                        <ul class="mt-6 grid gap-3 sm:grid-cols-2">
                            @foreach (__('site.details.amenities') as $amenity)
                                <li class="flex items-center gap-3 rounded-2xl border border-[#e8dcc0] bg-[#fffdf9] px-4 py-3 text-sm text-[#3b4b45]">
                                    <span aria-hidden="true" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#f7f0e0] text-[#a67b3f]">✦</span>
                                    {{ $amenity }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <aside class="h-fit rounded-[2rem] border border-[#e2d3a6] bg-[linear-gradient(135deg,#f7f0e2,#fffdf9)] p-7 shadow-[var(--shadow)] lg:sticky lg:top-8">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#a67b3f]">{{ __('site.details.stay_label') }}</p>
                    <p class="mt-5 font-display text-5xl font-semibold text-[#122b29]">
                        <bdi dir="ltr">{{ \App\Support\Currency::formatMad($room['price']) }}</bdi>
                    </p>
                    <p class="mt-1 text-sm text-[#6c756e]">{{ __('site.details.per_night') }}</p>
                    <div class="my-6 border-t border-[#e3d5b0]"></div>
                    <p class="mb-5 text-sm leading-6 text-[#4d5b56]">
                        {{ __('site.details.booking_description') }}
                    </p>
                    <a href="{{ route('reservations.create', ['room' => $room['slug']]) }}" class="gold-button w-full">
                        {{ __('site.details.reserve') }}
                    </a>
                    <p class="mt-4 text-center text-xs leading-5 text-[#6c756e]">{{ __('site.details.booking_note') }}</p>
                </aside>
            </div>
        </section>

        <section class="bg-[#f3ede0] py-20 md:py-24">
            <div class="section-shell">
                <div class="mx-auto max-w-3xl text-center">
                    <p class="section-kicker">{{ __('site.details.similar_kicker') }}</p>
                    <h2 class="section-title px-3 text-4xl leading-tight md:text-6xl">{{ __('site.details.similar_title') }}</h2>
                </div>

                <div class="mt-12 grid items-stretch gap-6 md:grid-cols-2 xl:grid-cols-3 xl:gap-8">
                    @foreach ($rooms as $similarRoom)
                        <x-room-card :room="$similarRoom" />
                    @endforeach
                </div>
            </div>
        </section>
    </main>
@endsection
