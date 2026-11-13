@extends('layouts.app')

@section('title', __('site.owner.reservations.edit_title') . ' | Dar Anawat')

@section('content')
    <main class="min-h-screen bg-[#f9f5ef] text-[#1d2d2a]">
        <section class="hero relative overflow-hidden pb-14 pt-32 text-white md:pt-40">
            <x-navbar />
            <div class="section-shell relative">
                <p class="section-kicker !text-[#ebd3a4]">{{ __('site.owner.reservation') }} #{{ $reservation->getKey() }}</p>
                <h1 class="font-display text-5xl md:text-7xl">{{ __('site.owner.reservations.edit_title') }}</h1>
            </div>
        </section>
        <section class="section-shell -mt-8 relative z-10 pb-20 md:pb-28">
            @include('owner.reservations.form', [
                'action' => route('owner.reservations.update', $reservation),
                'method' => 'PUT',
                'submitLabel' => __('site.owner.reservations.save'),
            ])
        </section>
    </main>
@endsection
