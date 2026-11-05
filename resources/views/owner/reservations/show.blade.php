@extends('layouts.app')

@section('title', __('site.owner.reservations.details_title') . ' | Dar Anawat')

@section('content')
    <main class="min-h-screen bg-[#f9f5ef] text-[#1d2d2a]">
        <section class="hero relative overflow-hidden pb-14 pt-32 text-white md:pt-40">
            <x-navbar />
            <div class="section-shell relative">
                <p class="section-kicker !text-[#ebd3a4]">{{ __('site.owner.kicker') }}</p>
                <h1 class="font-display text-5xl md:text-7xl">{{ __('site.owner.reservation') }} #{{ $reservation->getKey() }}</h1>
                <p class="mt-4">{{ __('site.payment.reservation_statuses.'.$reservation->status) }}</p>
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

            <article class="rounded-[2rem] border border-[#e7ddca] bg-[#fffdf9] p-5 shadow-[var(--shadow)] sm:p-8">
                <dl class="grid gap-5 sm:grid-cols-2">
                    <div><dt class="text-sm font-semibold">{{ __('site.owner.reservations.customer') }}</dt><dd class="mt-1">{{ $reservation->user?->name ?? __('site.owner.reservations.guest_without_account') }}</dd></div>
                    <div><dt class="text-sm font-semibold">{{ __('site.owner.reservations.guest_name') }}</dt><dd class="mt-1">{{ $reservation->guest_name }}</dd></div>
                    <div><dt class="text-sm font-semibold">{{ __('site.owner.reservations.guest_email') }}</dt><dd class="mt-1">{{ $reservation->guest_email }}</dd></div>
                    <div><dt class="text-sm font-semibold">{{ __('site.owner.reservations.guest_phone') }}</dt><dd class="mt-1">{{ $reservation->guest_phone ?: '—' }}</dd></div>
                    <div><dt class="text-sm font-semibold">{{ __('site.owner.room') }}</dt><dd class="mt-1">{{ __('site.rooms.'.$reservation->room_type.'.name') }} · {{ $reservation->guests }}</dd></div>
                    <div><dt class="text-sm font-semibold">{{ __('site.owner.dates') }}</dt><dd class="mt-1">{{ $reservation->check_in->format('d/m/Y') }} – {{ $reservation->check_out->format('d/m/Y') }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-sm font-semibold">{{ __('site.owner.reservations.special_requests') }}</dt><dd class="mt-1 whitespace-pre-line">{{ $reservation->special_requests ?: '—' }}</dd></div>
                </dl>

                <div class="mt-6 border-t border-[#e7ddca] pt-5">
                    <h2 class="font-display text-2xl">{{ __('site.owner.amount') }}</h2>
                    @if ($reservation->payment)
                        <p class="mt-2"><bdi dir="ltr">{{ \App\Support\Currency::formatMad($reservation->payment->amount_mad, 'fr') }}</bdi> · {{ __('site.payment.statuses.'.$reservation->payment->status) }}</p>
                        @foreach ($reservation->payment->decisions as $decision)
                            <p class="mt-2 text-sm text-[#6c756e]">
                                {{ __('site.owner.actions.'.$decision->action) }}
                                · {{ $decision->administrator?->name ?? '—' }}
                                · {{ $decision->created_at->format('d/m/Y H:i') }}
                                @if ($decision->reason) — {{ $decision->reason }} @endif
                            </p>
                        @endforeach
                    @else
                        <p class="mt-2">—</p>
                    @endif
                </div>

                <div class="mt-6 flex flex-wrap gap-3 border-t border-[#e7ddca] pt-5">
                    <a class="gold-button" href="{{ route('owner.reservations.edit', $reservation) }}">{{ __('site.owner.reservations.edit_button') }}</a>
                    <a class="rounded-full border border-[#8c6632] px-5 py-3 text-sm font-semibold text-[#704d20]" href="{{ route('owner.reservations.index') }}">{{ __('site.owner.reservations.back_to_list') }}</a>
                    @if ($reservation->status === \App\Models\Reservation::STATUS_PENDING && $reservation->payment?->status === \App\Models\Payment::STATUS_VERIFIED)
                        <a class="rounded-full border border-[#8c6632] px-5 py-3 text-sm font-semibold text-[#704d20]" href="{{ route('owner.dashboard') }}">{{ __('site.owner.reservations.open_confirmation') }}</a>
                    @endif
                </div>

                @if (
                    $reservation->status === \App\Models\Reservation::STATUS_PENDING
                    && $reservation->payment?->status === \App\Models\Payment::STATUS_AWAITING_TRANSFER
                    && $reservation->payment->receipts->isEmpty()
                    && $reservation->payment->decisions->isEmpty()
                )
                    <div class="mt-6 grid gap-5 border-t border-[#e7ddca] pt-5 md:grid-cols-2">
                        <form action="{{ route('owner.reservations.cancel', $reservation) }}" method="POST" class="space-y-3 rounded-2xl bg-[#fff1ef] p-5">
                            @csrf
                            @method('PATCH')
                            <label class="block text-sm font-semibold" for="cancel-reason">{{ __('site.owner.reservations.cancel_reason') }}</label>
                            <textarea class="form-field" id="cancel-reason" name="reason" minlength="3" maxlength="1000" required rows="3">{{ old('reason') }}</textarea>
                            @error('reason')<p class="text-sm text-red-700">{{ $message }}</p>@enderror
                            <button class="rounded-full border border-[#a64b3c] px-5 py-3 text-sm font-semibold text-[#8a352b]" type="submit">{{ __('site.owner.reservations.cancel_button') }}</button>
                        </form>

                        <form action="{{ route('owner.reservations.destroy', $reservation) }}" method="POST" class="space-y-3 rounded-2xl border border-[#e7b6ae] p-5" onsubmit="return confirm(@js(__('site.owner.reservations.delete_confirmation')))">
                            @csrf
                            @method('DELETE')
                            <p class="text-sm leading-6 text-[#7b2e24]">{{ __('site.owner.reservations.delete_note') }}</p>
                            <button class="rounded-full border border-[#a64b3c] px-5 py-3 text-sm font-semibold text-[#8a352b]" type="submit">{{ __('site.owner.reservations.delete_button') }}</button>
                        </form>
                    </div>
                @endif
            </article>
        </section>
    </main>
@endsection
