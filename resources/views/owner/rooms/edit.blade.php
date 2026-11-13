@extends('layouts.app')

@section('title', __('site.owner.rooms.edit') . ' | Dar Anawat')

@section('content')
    <main class="min-h-screen bg-[#f9f5ef] py-32 text-[#1d2d2a]">
        <x-navbar />
        <section class="section-shell">
            <p class="section-kicker">{{ __('site.owner.kicker') }}</p>
            <h1 class="mb-8 font-display text-5xl text-[#122b29]">{{ __('site.owner.rooms.edit') }} · {{ $room->room_number }}</h1>
            @include('owner.rooms.form', [
                'action' => route('owner.rooms.update', $room),
                'method' => 'PUT',
                'submitLabel' => __('site.owner.rooms.save'),
            ])
        </section>
    </main>
@endsection
