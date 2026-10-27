@extends('layouts.app')

@section('title', __('site.owner.dashboard_title') . ' | Dar Anawat')

@section('content')
    <main class="min-h-screen bg-[#f9f5ef] text-[#1d2d2a]">
        <section class="hero relative overflow-hidden pb-16 pt-32 text-white md:pb-20 md:pt-40">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(201,167,108,0.2),transparent_45%)]"></div>
            <x-navbar />
            <div class="section-shell relative">
                <p class="section-kicker !text-[#ebd3a4]">{{ __('site.owner.kicker') }}</p>
                <h1 class="font-display max-w-3xl text-5xl leading-[0.95] tracking-[-0.04em] md:text-7xl">{{ __('site.owner.dashboard_title') }}</h1>
                <p class="mt-6 max-w-2xl text-sm leading-7 text-white/75 md:text-base">{{ __('site.owner.intro') }}</p>
            </div>
        </section>

        <section class="section-shell -mt-8 relative z-10 space-y-10 pb-20 md:-mt-10 md:pb-28">
            @if (session('status'))
                <div class="rounded-2xl border border-[#b8d5c0] bg-[#edf7ef] p-5 text-sm leading-6 text-[#204d30]" role="status">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl border border-[#e7b6ae] bg-[#fff1ef] p-5 text-sm text-[#7b2e24]" role="alert">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="rounded-[2rem] border border-[#e7ddca] bg-[#fffdf9] p-5 shadow-[var(--shadow)] sm:p-8">
                <div class="mb-6 flex items-center justify-between gap-4">
                    <h2 class="font-display text-3xl text-[#122b29]">{{ __('site.owner.pending_title') }}</h2>
                    <span class="rounded-full bg-[#f4eee3] px-3 py-1 text-sm">{{ $pendingReservations->total() }}</span>
                </div>

                @forelse ($pendingReservations as $reservation)
                    @include('owner.partials.reservation-card', ['reservation' => $reservation])
                @empty
                    <p class="rounded-2xl bg-[#f4eee3] p-5 text-sm leading-6 text-[#4d5b56]">{{ __('site.owner.no_pending') }}</p>
                @endforelse

                <div class="mt-6">{{ $pendingReservations->links() }}</div>
            </section>

            <section class="rounded-[2rem] border border-[#e7ddca] bg-[#fffdf9] p-5 shadow-[var(--shadow)] sm:p-8">
                <div class="mb-6 flex items-center justify-between gap-4">
                    <h2 class="font-display text-3xl text-[#122b29]">{{ __('site.owner.confirmed_title') }}</h2>
                    <span class="rounded-full bg-[#edf7ef] px-3 py-1 text-sm">{{ $confirmedReservations->total() }}</span>
                </div>

                @forelse ($confirmedReservations as $reservation)
                    @include('owner.partials.reservation-card', ['reservation' => $reservation])
                @empty
                    <p class="rounded-2xl bg-[#f4eee3] p-5 text-sm leading-6 text-[#4d5b56]">{{ __('site.owner.no_confirmed') }}</p>
                @endforelse

                <div class="mt-6">{{ $confirmedReservations->links() }}</div>
            </section>
        </section>
    </main>
@endsection
