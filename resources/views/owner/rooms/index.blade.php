@extends('layouts.app')

@section('title', __('site.owner.rooms.title') . ' | Dar Anawat')

@section('content')
    <main class="min-h-screen bg-[#f9f5ef] text-[#1d2d2a]">
        <section class="hero relative overflow-hidden pb-14 pt-32 text-white md:pt-40">
            <x-navbar />
            <div class="section-shell relative">
                <p class="section-kicker !text-[#ebd3a4]">{{ __('site.owner.kicker') }}</p>
                <h1 class="font-display text-5xl md:text-7xl">{{ __('site.owner.rooms.title') }}</h1>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a class="gold-button" href="{{ route('owner.rooms.create') }}">{{ __('site.owner.rooms.create') }}</a>
                    <a class="rounded-full border border-white/50 px-5 py-3 text-sm font-semibold" href="{{ route('owner.dashboard') }}">{{ __('site.owner.reservations.back_to_dashboard') }}</a>
                </div>
            </div>
        </section>

        <section class="section-shell -mt-8 relative z-10 space-y-6 pb-20 md:pb-28">
            @if (session('status'))
                <div class="rounded-2xl border border-[#b8d5c0] bg-[#edf7ef] p-5 text-sm text-[#204d30]" role="status">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="rounded-2xl border border-[#e7b6ae] bg-[#fff1ef] p-5 text-sm text-[#7b2e24]" role="alert">
                    <ul class="list-inside list-disc space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="GET" action="{{ route('owner.rooms.index') }}" class="grid gap-4 rounded-[2rem] border border-[#e7ddca] bg-[#fffdf9] p-5 shadow-[var(--shadow)] md:grid-cols-4">
                <label class="sr-only" for="room-search">{{ __('site.owner.rooms.search') }}</label>
                <input id="room-search" class="form-field" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="{{ __('site.owner.rooms.search') }}">
                <label class="sr-only" for="room-filter-type">{{ __('site.owner.room') }}</label>
                <select id="room-filter-type" class="form-field" name="room_type">
                    <option value="">{{ __('site.owner.rooms.all_categories') }}</option>
                    @foreach ($roomTypes as $type => $capacity)
                        <option value="{{ $type }}" @selected(($filters['room_type'] ?? '') === $type)>{{ __('site.rooms.'.$type.'.name') }}</option>
                    @endforeach
                </select>
                <label class="sr-only" for="room-filter-status">{{ __('site.owner.rooms.status') }}</label>
                <select id="room-filter-status" class="form-field" name="status">
                    <option value="">{{ __('site.owner.rooms.all_statuses') }}</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ __('site.owner.rooms.statuses.'.$status) }}</option>
                    @endforeach
                </select>
                <button class="gold-button" type="submit">{{ __('site.owner.rooms.filter') }}</button>
            </form>

            <div class="overflow-x-auto rounded-[2rem] border border-[#e7ddca] bg-[#fffdf9] p-5 shadow-[var(--shadow)] sm:p-8">
                <table class="w-full min-w-[720px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-[#e7ddca] text-[#6c756e]">
                            <th class="p-3 font-semibold">{{ __('site.owner.rooms.number') }}</th>
                            <th class="p-3 font-semibold">{{ __('site.owner.room') }}</th>
                            <th class="p-3 font-semibold">{{ __('site.owner.rooms.price') }}</th>
                            <th class="p-3 font-semibold">{{ __('site.owner.rooms.status') }}</th>
                            <th class="p-3 font-semibold">{{ __('site.owner.rooms.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rooms as $room)
                            <tr class="border-b border-[#e7ddca] last:border-0">
                                <td class="p-3 font-semibold">{{ $room->room_number }}</td>
                                <td class="p-3">{{ __('site.rooms.'.$room->room_type.'.name') }}</td>
                                <td class="p-3"><bdi dir="ltr">{{ \App\Support\Currency::formatMad($room->price_per_night, 'fr') }}</bdi> / {{ __('site.common.per_night') }}</td>
                                <td class="p-3">{{ __('site.owner.rooms.statuses.'.$room->status) }}</td>
                                <td class="p-3">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <a class="font-semibold text-[#704d20] underline" href="{{ route('owner.rooms.edit', $room) }}">{{ __('site.owner.rooms.edit') }}</a>
                                        @if ($room->reservations_count === 0)
                                            <form action="{{ route('owner.rooms.destroy', $room) }}" method="POST" onsubmit="return confirm(@js(__('site.owner.rooms.delete_confirmation')))">
                                                @csrf
                                                @method('DELETE')
                                                <button class="font-semibold text-[#8a352b] underline" type="submit">{{ __('site.owner.rooms.delete') }}</button>
                                            </form>
                                        @else
                                            <span class="text-[#6c756e]">{{ __('site.owner.rooms.delete_blocked') }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="p-5 text-center text-[#6c756e]" colspan="5">{{ __('site.owner.rooms.empty') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-6">{{ $rooms->links() }}</div>
            </div>
        </section>
    </main>
@endsection
