@extends('layouts.app')

@section('title', __('site.payment.account_title') . ' | Dar Anawat')

@section('content')
    <main class="min-h-screen bg-[#f9f5ef] text-[#1d2d2a]">
        <section class="hero relative overflow-hidden pb-16 pt-32 text-white md:pb-20 md:pt-40">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(201,167,108,0.2),transparent_45%)]"></div>
            <x-navbar />
            <div class="section-shell relative">
                <p class="section-kicker !text-[#ebd3a4]">{{ __('site.payment.kicker') }}</p>
                <h1 class="font-display max-w-3xl text-5xl leading-[0.95] tracking-[-0.04em] md:text-7xl">{{ __('site.payment.account_title') }}</h1>
            </div>
        </section>

        <section class="section-shell -mt-8 relative z-10 pb-20 md:-mt-10 md:pb-28">
            <div class="mx-auto max-w-5xl rounded-[2rem] border border-[#e7ddca] bg-[#fffdf9] p-5 shadow-[var(--shadow)] sm:p-8 md:p-12">
                @if ($reservations->isEmpty())
                    <p class="rounded-2xl bg-[#f4eee3] p-5 text-sm leading-6 text-[#4d5b56]">{{ __('site.payment.no_reservations') }}</p>
                @else
                    <div class="space-y-4">
                        @foreach ($reservations as $reservation)
                            <article class="flex flex-col justify-between gap-4 rounded-2xl border border-[#e7ddca] p-5 sm:flex-row sm:items-center">
                                <div>
                                    <h2 class="font-display text-2xl text-[#122b29]">{{ __('site.payment.reservation') }} #{{ $reservation->getKey() }}</h2>
                                    <p class="mt-2 text-sm text-[#4d5b56]">
                                        {{ __('site.rooms.' . $reservation->room_type . '.name') }}
                                        · {{ $reservation->check_in->format('d/m/Y') }} – {{ $reservation->check_out->format('d/m/Y') }}
                                    </p>
                                    @if ($reservation->payment)
                                        <p class="mt-2 text-sm">
                                            <bdi dir="ltr">{{ \App\Support\Currency::formatMad($reservation->payment->amount_mad, 'fr') }}</bdi>
                                            · {{ __('site.payment.statuses.' . $reservation->payment->status) }}
                                        </p>
                                    @endif
                                </div>
                                @if ($reservation->payment)
                                    <a class="gold-button" href="{{ route('payments.show', $reservation) }}">{{ __('site.payment.open_details') }}</a>
                                @endif
                            </article>
                        @endforeach
                    </div>

                    <div class="mt-8">{{ $reservations->links() }}</div>
                @endif
            </div>
        </section>
    </main>
@endsection
