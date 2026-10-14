@php
    $rooms = [
        [
            'name' => 'Atlas Room',
            'price' => '950 MAD / night',
            'guests' => 2,
            'bed' => 'King Bed',
            'description' => 'Elegant room inspired by the colors and landscapes of Morocco.',
            'image' => asset('images/rooms/atlas.jpg'),
        ],
        [
            'name' => 'Palm Room',
            'price' => '1150 MAD / night',
            'guests' => 2,
            'bed' => 'Queen Bed',
            'description' => 'A warm and peaceful room combining Moroccan details with modern comfort.',
            'image' => asset('images/rooms/palm.jpg'),
        ],
        [
            'name' => 'Anawat Suite',
            'price' => '1650 MAD / night',
            'guests' => 4,
            'bed' => 'King Bed + Sofa Bed',
            'description' => 'A spacious private suite designed for an unforgettable Moroccan escape.',
            'image' => asset('images/rooms/suite.jpg'),
        ],
    ];

    $experiences = [
        [
            'icon' => 'R',
            'title' => 'RELAX',
            'heading' => 'Hammam & rest',
            'description' => 'Hammam, wellness and peaceful moments.',
        ],
        [
            'icon' => 'D',
            'title' => 'DISCOVER',
            'heading' => 'Culture & landscapes',
            'description' => 'Explore Moroccan culture and surrounding landscapes.',
        ],
        [
            'icon' => 'T',
            'title' => 'TASTE',
            'heading' => 'Seasonal flavors',
            'description' => 'Traditional Moroccan cuisine and local flavors.',
        ],
        [
            'icon' => 'E',
            'title' => 'ESCAPE',
            'heading' => 'Private moments',
            'description' => 'Romantic and private moments.',
        ],
    ];
@endphp

@extends('layouts.app')

@section('content')
    <main id="home" class="overflow-hidden bg-[#f9f5ef] text-[#1d2d2a]">
        <section class="hero relative min-h-screen overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: linear-gradient(90deg, rgba(10,18,16,0.72), rgba(10,18,16,0.28)), url('{{ asset('images/hero.jpg') }}');"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(200,165,90,0.2),transparent_34%)]"></div>

            <div class="relative z-10">
                <x-navbar />

                <div class="section-shell flex min-h-screen items-center pb-20 pt-28 md:pt-32">
                    <div class="max-w-3xl text-white">
                        <p class="mb-6 text-xs font-semibold uppercase tracking-[0.35em] text-[#e2c88c]">Dar Anawat</p>
                        <h1 class="font-display text-5xl leading-[0.95] text-white md:text-7xl lg:text-[5.2rem]">
                            YOUR MOROCCAN ESCAPE<br>
                            BEGINS HERE
                        </h1>
                        <p class="mt-6 max-w-xl text-lg text-white/80 md:text-xl">
                            A secluded hideaway where Moroccan elegance meets warm, effortless living.
                        </p>

                        <div class="mt-10 flex flex-col gap-4 sm:flex-row">
                            <a href="#about" class="gold-button">Explore the Dar</a>
                            <a href="#rooms" class="soft-button border-white/30 bg-white/5 text-white hover:bg-white/10">Discover our rooms</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="about" class="section-shell py-24 md:py-28">
            <div class="grid items-center gap-12 lg:grid-cols-[1.1fr_0.9fr]">
                <div>
                    <p class="section-kicker">A stay shaped by beauty</p>
                    <h2 class="section-title">A warm retreat inspired by the soul of Morocco.</h2>
                    <p class="mt-6 text-lg leading-8 text-[#4d5b56]">
                        At Dar Anawat, every detail is designed to feel intimate, generous, and quietly luxurious. From the soft textures of our interiors to the rhythm of the courtyard, the guest house invites slow mornings, memorable evenings, and a deeper connection to the land.
                    </p>
                    <p class="mt-5 text-lg leading-8 text-[#4d5b56]">
                        Nestled in an atmosphere of calm and elegance, our boutique guest house blends contemporary comfort with authentic Moroccan character. It is a place to rest, gather, explore, and return to the beauty of everyday rituals.
                    </p>
                </div>

                <div class="relative overflow-hidden rounded-[2rem] border border-[#e3d5b0] bg-[#f1e7d5] p-4 shadow-[0_25px_80px_rgba(18,46,42,0.12)]">
                    <img src="{{ asset('images/hero.jpg') }}" alt="Dar Anawat guest house exterior" class="h-[520px] w-full rounded-[1.5rem] object-cover">
                    <div class="absolute bottom-8 left-8 right-8 rounded-[1.5rem] border border-white/40 bg-[#102f2b]/80 p-5 text-white backdrop-blur-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#e7c980]">Guest House Experience</p>
                        <p class="mt-3 text-2xl font-light">Private serenity in a vibrant cultural setting.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="rooms" class="bg-[#f3ede0] py-24 md:py-28">
            <div class="section-shell">
                <div class="mx-auto max-w-3xl text-center">
                    <p class="section-kicker">Stay in style</p>
                    <h2 class="section-title">Find your perfect stay</h2>
                </div>

                <div class="mt-12 grid gap-8 lg:grid-cols-3">
                    @foreach ($rooms as $room)
                        <x-room-card :room="$room" />
                    @endforeach
                </div>
            </div>
        </section>

        <section id="experiences" class="section-shell py-24 md:py-28">
            <div class="mx-auto max-w-3xl text-center">
                <p class="section-kicker">Moments to remember</p>
                <h2 class="section-title">An experience designed around your rhythm</h2>
            </div>

            <div class="mt-12 grid gap-8 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($experiences as $experience)
                    <x-experience-card :experience="$experience" />
                @endforeach
            </div>
        </section>

        <section id="discover" class="relative overflow-hidden bg-[#122b29] py-24 text-white md:py-28">
            <div class="absolute inset-0 bg-cover bg-center opacity-25" style="background-image: url('{{ asset('images/hero.jpg') }}');"></div>
            <div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(18,43,41,0.96),rgba(18,43,41,0.72))]"></div>

            <div class="relative z-10 section-shell">
                <div class="grid items-end gap-6 md:grid-cols-[0.9fr_1.1fr]">
                    <div>
                        <p class="section-kicker text-[#e2c88c]">Discover Morocco</p>
                        <h2 class="section-title text-white">Beauty, culture and local soul.</h2>
                    </div>
                    <p class="max-w-xl text-lg leading-8 text-[#e8e0d2]">
                        From the ochre tones of the desert to the lively textures of Marrakech, Dar Anawat offers an invitation to live Morocco with ease and wonder.
                    </p>
                </div>

                <div class="mt-12 grid gap-6 md:grid-cols-3">
                    <article class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#e2c88c]">Nature</p>
                        <h3 class="mt-5 font-display text-4xl">Atlas & desert</h3>
                        <p class="mt-4 text-base leading-7 text-[#e1d9ce]">Explore panoramic landscapes, peaceful valleys, and golden horizons beyond the city.</p>
                    </article>

                    <article class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#e2c88c]">Culture</p>
                        <h3 class="mt-5 font-display text-4xl">Mosaic stories</h3>
                        <p class="mt-4 text-base leading-7 text-[#e1d9ce]">Discover artisan heritage, heritage riads, and the timeless beauty of Moroccan craft.</p>
                    </article>

                    <article class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#e2c88c]">Local experiences</p>
                        <h3 class="mt-5 font-display text-4xl">Slow living</h3>
                        <p class="mt-4 text-base leading-7 text-[#e1d9ce]">Private dinners, market walks, tea rituals, and warm welcomes inspired by everyday Moroccan life.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="cta" class="section-shell py-24 text-center md:py-28">
            <div class="rounded-[2.5rem] border border-[#e2d3a6] bg-[linear-gradient(135deg,#f7f0e2,#fffdf9)] p-8 shadow-[0_24px_60px_rgba(24,48,44,0.08)] md:p-16">
                <p class="section-kicker">Ready when you are</p>
                <h2 class="mx-auto max-w-4xl section-title">READY TO EXPERIENCE DAR ANAWAT?</h2>
                <p class="mx-auto mt-6 max-w-2xl text-xl leading-9 text-[#485c57]">
                    Your stay is more than a room. It is a story waiting to be lived.
                </p>
                <div class="mt-10 flex justify-center">
                    <button type="button" class="gold-button">Plan Your Stay</button>
                </div>
            </div>
        </section>
    </main>
@endsection
