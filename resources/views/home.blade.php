@php
    $rooms = [
        [
            'translation_key' => 'atlas',
            'name' => 'Atlas Room',
            'price' => config('rooms.rates_mad.single'),
            'guests' => 2,
            'bed' => 'King Bed',
            'description' => 'Elegant room inspired by the colors and landscapes of Morocco.',
            'image' => asset('images/rooms/atlas.jpg'),
        ],
        [
            'translation_key' => 'palm',
            'name' => 'Palm Room',
            'price' => config('rooms.rates_mad.double'),
            'guests' => 2,
            'bed' => 'Queen Bed',
            'description' => 'A warm and peaceful room combining Moroccan details with modern comfort.',
            'image' => asset('images/rooms/palm.jpg'),
        ],
        [
            'translation_key' => 'suite',
            'name' => 'Anawat Suite',
            'price' => config('rooms.rates_mad.suite'),
            'guests' => 4,
            'bed' => 'King Bed + Sofa Bed',
            'description' => 'A spacious private suite designed for an unforgettable Moroccan escape.',
            'image' => asset('images/rooms/suite.jpg'),
        ],
    ];
    $rooms = \App\Support\RoomCatalog::all();

    $experiences = [
        [
            'icon' => '↗',
            'title' => __('site.home.experiences.relax.title'),
            'heading' => __('site.home.experiences.relax.heading'),
            'description' => __('site.home.experiences.relax.description'),
        ],
        [
            'icon' => '✦',
            'title' => __('site.home.experiences.discover.title'),
            'heading' => __('site.home.experiences.discover.heading'),
            'description' => __('site.home.experiences.discover.description'),
        ],
        [
            'icon' => '♡',
            'title' => __('site.home.experiences.taste.title'),
            'heading' => __('site.home.experiences.taste.heading'),
            'description' => __('site.home.experiences.taste.description'),
        ],
        [
            'icon' => '⌖',
            'title' => __('site.home.experiences.escape.title'),
            'heading' => __('site.home.experiences.escape.heading'),
            'description' => __('site.home.experiences.escape.description'),
        ],
    ];
@endphp

@extends('layouts.app')

@section('title', __('site.meta.home_title'))

@section('content')
    <main id="home" class="overflow-hidden bg-[#f9f5ef] text-[#1d2d2a]">
        <section class="hero relative min-h-screen overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: linear-gradient(90deg, rgba(10,18,16,0.72), rgba(10,18,16,0.28)), url('{{ asset('images/hero.jpg') }}');"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(200,165,90,0.2),transparent_34%)]"></div>

            <div class="relative z-10">
                <x-navbar />

                <div class="section-shell flex min-h-screen items-center pb-20 pt-28 md:pt-32">
                    <div class="max-w-3xl text-white">
                        <p class="mb-6 text-xs font-semibold uppercase tracking-[0.35em] text-[#e2c88c]">{{ __('site.home.hero.kicker') }}</p>
                        <h1 class="font-display text-5xl leading-[0.95] text-white md:text-7xl lg:text-[5.2rem]">
                            {{ __('site.home.hero.title_first') }}
                        </h1>
                        <p class="mt-4 max-w-2xl font-display text-3xl leading-tight text-[#ebd3a4] md:text-5xl">
                            {{ __('site.home.hero.title_second') }}
                        </p>
                        <p class="mt-6 max-w-xl text-lg text-white/80 md:text-xl">
                            {{ __('site.home.hero.description') }}
                        </p>

                        <div class="mt-10 flex flex-col gap-4 sm:flex-row">
                            <a href="#about" class="gold-button">{{ __('site.home.hero.explore') }}</a>
                            <a href="{{ route('reservations.create') }}" class="soft-button border-white/30 bg-white/5 text-white hover:bg-white/10">{{ __('site.home.hero.book') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="about" class="section-shell py-24 md:py-28">
            <div class="grid items-center gap-12 lg:grid-cols-[1.1fr_0.9fr]">
                <div>
                    <p class="section-kicker">{{ __('site.home.about.kicker') }}</p>
                    <h2 class="section-title">{{ __('site.home.about.title') }}</h2>
                    <p class="mt-6 text-lg leading-8 text-[#4d5b56]">
                        {{ __('site.home.about.first') }}
                    </p>
                    <p class="mt-5 text-lg leading-8 text-[#4d5b56]">
                        {{ __('site.home.about.second') }}
                    </p>
                </div>

                <div class="relative overflow-hidden rounded-[2rem] border border-[#e3d5b0] bg-[#f1e7d5] p-4 shadow-[0_25px_80px_rgba(18,46,42,0.12)]">
                    <img src="{{ asset('images/hero.jpg') }}" alt="{{ __('site.home.about.image_alt') }}" class="h-[520px] w-full rounded-[1.5rem] object-cover">
                    <div class="absolute bottom-8 left-8 right-8 rounded-[1.5rem] border border-white/40 bg-[#102f2b]/80 p-5 text-white backdrop-blur-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#e7c980]">{{ __('site.home.about.image_label') }}</p>
                        <p class="mt-3 text-2xl font-light">{{ __('site.home.about.image_caption') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="experiences" class="section-shell py-24 md:py-28">
            <div class="mx-auto max-w-3xl text-center">
                <p class="section-kicker">{{ __('site.home.experiences.kicker') }}</p>
                <h2 class="section-title">{{ __('site.home.experiences.title') }}</h2>
                <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-[#4d5b56]">
                    {{ __('site.home.experiences.intro') }}
                </p>
            </div>

            <div class="mt-12 grid gap-8 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($experiences as $experience)
                    <x-experience-card :experience="$experience" />
                @endforeach
            </div>
        </section>

        <section id="rooms" class="bg-[#f3ede0] py-24 md:py-28">
            <div class="section-shell">
                <div class="mx-auto max-w-3xl text-center">
                    <p class="section-kicker">{{ __('site.home.rooms.kicker') }}</p>
                    <h2 class="section-title">{{ __('site.home.rooms.title') }}</h2>
                    <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-[#4d5b56]">{{ __('site.home.rooms.intro') }}</p>
                </div>

                <div class="mt-12 grid gap-8 md:grid-cols-2 xl:grid-cols-4">
                    @foreach ($rooms as $room)
                        <x-room-card :room="$room" />
                    @endforeach
                </div>
            </div>
        </section>

        <section id="breakfast" class="section-shell py-20 md:py-24">
            <div class="grid overflow-hidden rounded-[2rem] border border-[#e2d3a6] bg-[linear-gradient(135deg,#f7f0e2,#fffdf9)] shadow-[var(--shadow)] lg:grid-cols-[0.9fr_1.1fr]">
                <div class="relative min-h-72 overflow-hidden bg-[#122b29]">
                    <img src="{{ asset('images/rooms/atlas.jpg') }}" alt="{{ __('site.home.breakfast.image_alt') }}" class="absolute inset-0 h-full w-full object-cover opacity-75">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#102f2b]/85 via-transparent to-transparent"></div>
                    <p class="absolute bottom-7 left-7 right-7 font-display text-3xl text-white md:text-4xl">{{ __('site.home.breakfast.image_caption') }}</p>
                </div>
                <div class="p-7 md:p-12 lg:p-14">
                    <p class="section-kicker">{{ __('site.home.breakfast.kicker') }}</p>
                    <h2 class="font-display text-4xl leading-tight text-[#122b29] md:text-5xl">{{ __('site.home.breakfast.title') }}</h2>
                    <p class="mt-5 text-base leading-8 text-[#4d5b56] md:text-lg">{{ __('site.home.breakfast.description') }}</p>
                    <p class="mt-6 inline-flex items-center gap-3 rounded-full bg-[#122b29] px-5 py-3 text-sm font-semibold text-white">
                        <span aria-hidden="true" class="text-[#e2c88c]">✦</span>
                        {{ __('site.details.breakfast.included') }}
                    </p>
                </div>
            </div>
        </section>

        <section id="atmosphere" class="relative overflow-hidden bg-[#122b29] py-24 text-white md:py-28">
            <div class="absolute inset-0 bg-cover bg-center opacity-20" style="background-image: url('{{ asset('images/rooms/suite.jpg') }}');"></div>
            <div class="absolute inset-0 bg-[linear-gradient(110deg,rgba(18,43,41,0.98),rgba(18,43,41,0.78))]"></div>
            <div class="relative z-10 section-shell grid items-center gap-12 lg:grid-cols-[0.8fr_1.2fr]">
                <div class="max-w-xl">
                    <p class="section-kicker !text-[#e2c88c]">{{ __('site.home.atmosphere.kicker') }}</p>
                    <h2 class="font-display text-5xl leading-tight md:text-6xl">{{ __('site.home.atmosphere.title') }}</h2>
                </div>
                <div>
                    <p class="text-lg leading-8 text-[#e8e0d2] md:text-xl">{{ __('site.home.atmosphere.description') }}</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        @foreach (__('site.home.atmosphere.qualities') as $quality)
                            <span class="rounded-full border border-[#e2c88c]/40 bg-white/5 px-4 py-2 text-sm text-[#f3ead9]">{{ $quality }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section id="discover" class="relative overflow-hidden bg-[#f3ede0] py-24 text-[#1d2d2a] md:py-28">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(201,167,108,0.13),transparent_45%)]"></div>

            <div class="relative z-10 section-shell">
                <div class="grid items-end gap-6 md:grid-cols-[0.9fr_1.1fr]">
                    <div>
                        <p class="section-kicker">{{ __('site.home.discover.kicker') }}</p>
                        <h2 class="section-title">{{ __('site.home.discover.title') }}</h2>
                    </div>
                    <p class="max-w-xl text-lg leading-8 text-[#4d5b56]">
                        {{ __('site.home.discover.description') }}
                    </p>
                </div>

                <div class="mt-12 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach (__('site.home.discover.places') as $place)
                        <article class="group relative min-h-64 overflow-hidden rounded-[1.75rem] border border-[#d8c9a7] bg-gradient-to-br from-[#264743] via-[#1b3936] to-[#102825] p-6 text-white shadow-[0_18px_45px_rgba(18,46,42,0.1)] transition duration-300 hover:-translate-y-1 hover:border-[#c9a76c]">
                            <span aria-hidden="true" class="absolute -right-5 -top-10 font-display text-[11rem] leading-none text-white/[0.04]">{{ $loop->iteration }}</span>
                            <div class="relative z-10 flex h-full min-h-52 flex-col">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#e2c88c]">{{ $place['label'] }}</p>
                                <h3 class="mt-auto font-display text-3xl leading-tight md:text-4xl">{{ $place['title'] }}</h3>
                                <p class="mt-3 text-sm leading-6 text-[#e1d9ce]">{{ $place['description'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="section-shell py-20 md:py-24">
            <div class="mx-auto max-w-3xl text-center">
                <p class="section-kicker">{{ __('site.home.why.kicker') }}</p>
                <h2 class="section-title">{{ __('site.home.why.title') }}</h2>
            </div>
            <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                @foreach (__('site.home.why.items') as $item)
                    <article class="rounded-[1.5rem] border border-[#e8dcc0] bg-[#fffdf9] p-5 text-center shadow-[0_14px_35px_rgba(18,46,42,0.05)]">
                        <span aria-hidden="true" class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-[#f1e7d5] font-display text-2xl text-[#a67b3f]">{{ $item['icon'] }}</span>
                        <h3 class="mt-4 font-display text-2xl leading-tight text-[#122b29]">{{ $item['title'] }}</h3>
                    </article>
                @endforeach
            </div>
        </section>

        <section id="cta" class="section-shell py-24 text-center md:py-28">
            <div class="rounded-[2.5rem] border border-[#e2d3a6] bg-[linear-gradient(135deg,#f7f0e2,#fffdf9)] p-8 shadow-[0_24px_60px_rgba(24,48,44,0.08)] md:p-16">
                <p class="section-kicker">{{ __('site.home.cta.kicker') }}</p>
                <h2 class="mx-auto max-w-4xl section-title">{{ __('site.home.cta.title') }}</h2>
                <p class="mx-auto mt-6 max-w-2xl text-xl leading-9 text-[#485c57]">
                    {{ __('site.home.cta.description') }}
                </p>
                <div class="mt-10 flex flex-col justify-center gap-4 sm:flex-row">
                    <a href="{{ route('reservations.create') }}" class="gold-button">{{ __('site.home.cta.button') }}</a>
                    <a href="{{ route('chambres') }}" class="soft-button !border-[#b98d4d] !bg-transparent !text-[#122b29] hover:!bg-[#f7f0e0]">{{ __('site.home.cta.rooms') }}</a>
                </div>
            </div>
        </section>
    </main>
@endsection
