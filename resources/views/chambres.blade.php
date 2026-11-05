@extends('layouts.app')

@section('title', __('site.meta.rooms_title'))

@section('content')
    <main class="overflow-hidden bg-[#f9f5ef] text-[#1d2d2a]">
        <section class="hero relative min-h-[520px] overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center opacity-35" style="background-image: url('{{ asset('images/hero.jpg') }}');"></div>
            <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(10,18,16,0.82),rgba(10,18,16,0.38))]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(200,165,90,0.2),transparent_34%)]"></div>

            <div class="relative z-10">
                <x-navbar />

                <div class="section-shell flex min-h-[520px] items-end pb-20 pt-36">
                    <div class="max-w-3xl text-white">
                        <p class="mb-5 text-xs font-semibold uppercase tracking-[0.35em] text-[#e2c88c]">{{ __('site.chambres.hero_kicker') }}</p>
                        <h1 class="font-display text-5xl leading-[0.95] text-white md:text-7xl">{{ __('site.chambres.title') }}</h1>
                        <p class="mt-6 max-w-2xl text-lg leading-8 text-white/80 md:text-xl">
                            {{ __('site.chambres.description') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-[#f3ede0] py-20 md:py-28">
            <div class="section-shell">
                <div class="mx-auto max-w-3xl text-center">
                    <p class="section-kicker">{{ __('site.chambres.section_kicker') }}</p>
                    <h2 class="section-title px-3 text-4xl leading-tight md:text-6xl">{{ __('site.chambres.section_title') }}</h2>
                    <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-[#4d5b56]">{{ __('site.chambres.section_description') }}</p>
                </div>

                <div class="mt-12 grid items-stretch gap-6 md:grid-cols-2 xl:grid-cols-4 xl:gap-8">
                    @foreach ($rooms as $room)
                        <x-room-card :room="$room" />
                    @endforeach
                </div>
            </div>
        </section>
    </main>
@endsection
