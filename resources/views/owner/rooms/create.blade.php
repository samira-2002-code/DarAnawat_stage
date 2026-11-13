@extends('layouts.app')

@section('title', __('site.owner.rooms.create') . ' | Dar Anawat')

@section('content')
    <main class="min-h-screen bg-[#f9f5ef] py-32 text-[#1d2d2a]">
        <x-navbar />
        <section class="section-shell">
            <p class="section-kicker">{{ __('site.owner.kicker') }}</p>
            <h1 class="mb-8 font-display text-5xl text-[#122b29]">{{ __('site.owner.rooms.create') }}</h1>
            @include('owner.rooms.form', [
                'action' => route('owner.rooms.store'),
                'method' => 'POST',
                'submitLabel' => __('site.owner.rooms.create'),
            ])
        </section>
    </main>
@endsection
