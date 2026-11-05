@extends('layouts.app')

@section('title', __('site.owner.reservations.index_title') . ' | Dar Anawat')

@section('content')
    <main class="min-h-screen bg-[#f9f5ef] text-[#1d2d2a]">
        <section class="hero relative overflow-hidden pb-14 pt-32 text-white md:pt-40">
            <x-navbar />
            <div class="section-shell relative">
                <p class="section-kicker !text-[#ebd3a4]">{{ __('site.owner.kicker') }}</p>
                <h1 class="font-display text-5xl md:text-7xl">{{ __('site.owner.reservations.index_title') }}</h1>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a class="gold-button" href="{{ route('owner.reservations.create') }}">{{ __('site.owner.reservations.create_button') }}</a>
                    <a class="rounded-full border border-white/50 px-5 py-3 text-sm font-semibold" href="{{ route('owner.dashboard') }}">{{ __('site.owner.reservations.back_to_dashboard') }}</a>
                </div>
            </div>
        </section>

        <section class="section-shell -mt-8 relative z-10 space-y-6 pb-20 md:pb-28">
            @if (session('status'))
                <div class="rounded-2xl border border-[#b8d5c0] bg-[#edf7ef] p-5 text-sm text-[#204d30]" role="status">{{ session('status') }}</div>
            @endif

            <div class="rounded-[2rem] border border-[#e7ddca] bg-[#fffdf9] p-5 shadow-[var(--shadow)] sm:p-8">
                @forelse ($reservations as $reservation)
                    <article class="mb-4 grid gap-4 rounded-2xl border border-[#e7ddca] p-5 last:mb-0 md:grid-cols-[1fr_auto] md:items-center">
                        <div>
                            <h2 class="font-display text-2xl text-[#122b29]">
                                {{ __('site.owner.reservation') }} #{{ $reservation->getKey() }}
                                <span class="text-base font-sans">{{ __('site.payment.reservation_statuses.'.$reservation->status) }}</span>
                            </h2>
                            <p class="mt-2">{{ $reservation->guest_name }} · {{ $reservation->guest_email }}</p>
                            <p class="mt-1 text-sm text-[#6c756e]">
                                {{ __('site.rooms.'.$reservation->room_type.'.name') }}
                                · {{ $reservation->check_in->format('d/m/Y') }} – {{ $reservation->check_out->format('d/m/Y') }}
                            </p>
                            @if ($reservation->payment)
                                <p class="mt-1 text-sm text-[#6c756e]">
                                    {{ __('site.owner.amount') }}:
                                    <bdi dir="ltr">{{ \App\Support\Currency::formatMad($reservation->payment->amount_mad, 'fr') }}</bdi>
                                    · {{ __('site.payment.statuses.'.$reservation->payment->status) }}
                                </p>
                            @endif
                        </div>
                        <a class="rounded-full border border-[#8c6632] px-5 py-3 text-center text-sm font-semibold text-[#704d20]" href="{{ route('owner.reservations.show', $reservation) }}">
                            {{ __('site.owner.reservations.view') }}
                        </a>
                    </article>
                @empty
                    <p class="rounded-2xl bg-[#f4eee3] p-5 text-sm text-[#4d5b56]">{{ __('site.owner.reservations.empty') }}</p>
                @endforelse

                <div class="mt-6">{{ $reservations->links() }}</div>
            </div>
        </section>
    </main>
@endsection
