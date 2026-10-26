@extends('layouts.app')

@section('title', __('site.reservation.page_title') . ' | Dar Anawat')
@section('meta_description', __('site.reservation.intro'))

@section('content')
    <main class="min-h-screen bg-[#f9f5ef] text-[#1d2d2a]">
        <section class="hero relative overflow-hidden pb-16 pt-32 text-white md:pb-20 md:pt-40">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(201,167,108,0.2),transparent_45%)]"></div>
            <x-navbar />
            <div class="section-shell relative">
                <p class="section-kicker !text-[#ebd3a4]">{{ __('site.reservation.kicker') }}</p>
                <h1 class="font-display max-w-3xl text-5xl leading-[0.95] tracking-[-0.04em] md:text-7xl">
                    {{ __('site.reservation.title') }}
                </h1>
                <p class="mt-6 max-w-2xl text-sm leading-7 text-white/75 md:text-base">
                    {{ __('site.reservation.intro') }}
                </p>
            </div>
        </section>

        <section class="section-shell -mt-8 relative z-10 pb-20 md:-mt-10 md:pb-28">
            <div class="mx-auto max-w-4xl rounded-[2rem] border border-[#e7ddca] bg-[#fffdf9] p-5 shadow-[var(--shadow)] sm:p-8 md:p-12">
                @if (session('reservation_success'))
                    <div class="mb-8 rounded-2xl border border-[#b8d5c0] bg-[#edf7ef] p-5 text-sm leading-6 text-[#204d30]" role="status">
                        {{ session('reservation_success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-8 rounded-2xl border border-[#e7b6ae] bg-[#fff1ef] p-5 text-sm text-[#7b2e24]" role="alert" tabindex="-1">
                        <p class="font-semibold">{{ __('site.reservation.errors.summary') }}</p>
                        <ul class="mt-2 list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('reservations.store') }}" method="POST" class="space-y-9" data-reservation-form>
                    @csrf

                    <fieldset class="space-y-5">
                        <legend class="font-display text-3xl text-[#122b29]">{{ __('site.reservation.guest_section') }}</legend>
                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="guest_name" class="mb-2 block text-sm font-medium">{{ __('site.reservation.name') }}</label>
                                <input id="guest_name" name="guest_name" type="text" autocomplete="name" required maxlength="120" value="{{ old('guest_name') }}" aria-describedby="guest_name_error" @class(['form-field', 'border-red-500' => $errors->has('guest_name')])>
                                @error('guest_name')<p id="guest_name_error" class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="guest_email" class="mb-2 block text-sm font-medium">{{ __('site.reservation.email') }}</label>
                                <input id="guest_email" name="guest_email" type="email" autocomplete="email" required maxlength="254" value="{{ old('guest_email') }}" aria-describedby="guest_email_error" @class(['form-field', 'border-red-500' => $errors->has('guest_email')])>
                                @error('guest_email')<p id="guest_email_error" class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
                            </div>
                            <div class="md:col-span-2">
                                <label for="guest_phone" class="mb-2 block text-sm font-medium">{{ __('site.reservation.phone') }}</label>
                                <input id="guest_phone" name="guest_phone" type="tel" autocomplete="tel" maxlength="40" value="{{ old('guest_phone') }}" aria-describedby="guest_phone_error" @class(['form-field', 'border-red-500' => $errors->has('guest_phone')])>
                                @error('guest_phone')<p id="guest_phone_error" class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="space-y-5">
                        <legend class="font-display text-3xl text-[#122b29]">{{ __('site.reservation.stay_section') }}</legend>
                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="room_type" class="mb-2 block text-sm font-medium">{{ __('site.reservation.room') }}</label>
                                <select id="room_type" name="room_type" required data-room-select aria-describedby="room_type_error" @class(['form-field', 'border-red-500' => $errors->has('room_type')])>
                                    <option value="">{{ __('site.reservation.choose_room') }}</option>
                                    @foreach ($rooms as $room)
                                        <option value="{{ $room['slug'] }}" data-capacity="{{ $room['capacity'] }}" @selected(old('room_type', $selectedRoom) === $room['slug'])>
                                            {{ __('site.rooms.' . $room['slug'] . '.name') }} — {{ __('site.common.price_format', ['price' => \App\Support\Currency::formatDh($room['price'])]) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('room_type')<p id="room_type_error" class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="guests" class="mb-2 block text-sm font-medium">{{ __('site.reservation.guests') }}</label>
                                <input id="guests" name="guests" type="number" min="1" max="4" step="1" inputmode="numeric" required value="{{ old('guests', 1) }}" data-guests-input aria-describedby="guests_hint guests_error" @class(['form-field', 'border-red-500' => $errors->has('guests')])>
                                <p id="guests_hint" class="mt-2 text-xs text-[#6c756e]">{{ __('site.reservation.capacity_hint') }}</p>
                                @error('guests')<p id="guests_error" class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="check_in" class="mb-2 block text-sm font-medium">{{ __('site.reservation.check_in') }}</label>
                                <input id="check_in" name="check_in" type="date" min="{{ now()->toDateString() }}" required value="{{ old('check_in') }}" data-check-in aria-describedby="check_in_error" @class(['form-field', 'border-red-500' => $errors->has('check_in')])>
                                @error('check_in')<p id="check_in_error" class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="check_out" class="mb-2 block text-sm font-medium">{{ __('site.reservation.check_out') }}</label>
                                <input id="check_out" name="check_out" type="date" min="{{ now()->addDay()->toDateString() }}" required value="{{ old('check_out') }}" data-check-out aria-describedby="check_out_error" @class(['form-field', 'border-red-500' => $errors->has('check_out')])>
                                @error('check_out')<p id="check_out_error" class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
                            </div>
                            <div class="md:col-span-2">
                                <label for="special_requests" class="mb-2 block text-sm font-medium">{{ __('site.reservation.special_requests') }}</label>
                                <textarea id="special_requests" name="special_requests" rows="4" maxlength="2000" aria-describedby="special_requests_error" @class(['form-field resize-y', 'border-red-500' => $errors->has('special_requests')])>{{ old('special_requests') }}</textarea>
                                @error('special_requests')<p id="special_requests_error" class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </fieldset>

                    <p class="rounded-2xl bg-[#f4eee3] p-4 text-sm leading-6 text-[#4d5b56]">
                        {{ __('site.reservation.pending_note') }}
                    </p>

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                        <button type="submit" class="gold-button min-w-56" data-submit-button>
                            <span data-submit-label>{{ __('site.reservation.submit') }}</span>
                        </button>
                        <span class="text-xs text-[#6c756e]" data-submit-status aria-live="polite"></span>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <script>
        const reservationForm = document.querySelector('[data-reservation-form]');
        const roomSelect = document.querySelector('[data-room-select]');
        const guestsInput = document.querySelector('[data-guests-input]');
        const checkInInput = document.querySelector('[data-check-in]');
        const checkOutInput = document.querySelector('[data-check-out]');
        const submitButton = document.querySelector('[data-submit-button]');

        const updateGuestCapacity = () => {
            const capacity = roomSelect.selectedOptions[0].dataset.capacity;
            guestsInput.max = capacity || '4';
        };

        roomSelect.addEventListener('change', updateGuestCapacity);

        checkInInput.addEventListener('change', () => {
            if (checkInInput.value) {
                const [year, month, day] = checkInInput.value.split('-').map(Number);
                const nextDay = new Date(year, month - 1, day + 1);
                const nextDayValue = [
                    nextDay.getFullYear(),
                    String(nextDay.getMonth() + 1).padStart(2, '0'),
                    String(nextDay.getDate()).padStart(2, '0'),
                ].join('-');
                checkOutInput.min = nextDayValue;
                if (checkOutInput.value && checkOutInput.value <= checkInInput.value) {
                    checkOutInput.value = '';
                }
            }
        });

        updateGuestCapacity();
        checkInInput.dispatchEvent(new Event('change'));

        reservationForm.addEventListener('submit', () => {
            submitButton.disabled = true;
            submitButton.classList.add('cursor-wait', 'opacity-70');
            submitButton.querySelector('[data-submit-label]').textContent = @js(__('site.reservation.submitting'));
            document.querySelector('[data-submit-status]').textContent = @js(__('site.reservation.submitting'));
        });
    </script>
@endsection
