<div class="mx-auto max-w-4xl rounded-[2rem] border border-[#e7ddca] bg-[#fffdf9] p-5 shadow-[var(--shadow)] sm:p-8">
    @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-[#e7b6ae] bg-[#fff1ef] p-5 text-sm text-[#7b2e24]" role="alert">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($reservation->exists)
        <p class="mb-6 rounded-2xl bg-[#f4eee3] p-4 text-sm leading-6 text-[#4d5b56]">
            {{ __('site.owner.reservations.payment_note') }}
        </p>
    @endif

    <form action="{{ $action }}" method="POST" class="space-y-6">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif

        <div>
            <label class="mb-2 block text-sm font-semibold" for="user_id">{{ __('site.owner.reservations.customer') }}</label>
            <select class="form-field" id="user_id" name="user_id">
                <option value="">{{ __('site.owner.reservations.guest_without_account') }}</option>
                @foreach ($users as $user)
                    <option value="{{ $user->getKey() }}" @selected((string) old('user_id', $reservation->user_id) === (string) $user->getKey())>
                        {{ $user->name }} · {{ $user->email }}
                    </option>
                @endforeach
            </select>
            @error('user_id')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold" for="guest_name">{{ __('site.owner.reservations.guest_name') }}</label>
                <input class="form-field" id="guest_name" name="guest_name" type="text" maxlength="120" required value="{{ old('guest_name', $reservation->guest_name) }}">
                @error('guest_name')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold" for="guest_email">{{ __('site.owner.reservations.guest_email') }}</label>
                <input class="form-field" id="guest_email" name="guest_email" type="email" maxlength="254" required value="{{ old('guest_email', $reservation->guest_email) }}">
                @error('guest_email')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold" for="guest_phone">{{ __('site.owner.reservations.guest_phone') }}</label>
                <input class="form-field" id="guest_phone" name="guest_phone" type="tel" maxlength="40" value="{{ old('guest_phone', $reservation->guest_phone) }}">
                @error('guest_phone')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold" for="room_type">{{ __('site.owner.room') }}</label>
                <select class="form-field" id="room_type" name="room_type" required>
                    @foreach (\App\Models\Reservation::ROOM_CAPACITIES as $type => $capacity)
                        <option value="{{ $type }}" @selected(old('room_type', $reservation->room_type) === $type)>
                            {{ __('site.rooms.'.$type.'.name') }} · {{ $capacity }}
                        </option>
                    @endforeach
                </select>
                @error('room_type')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold" for="guests">{{ __('site.reservation.guests') }}</label>
                <input class="form-field" id="guests" name="guests" type="number" min="1" max="4" step="1" required value="{{ old('guests', $reservation->guests ?? 1) }}">
                @error('guests')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold" for="check_in">{{ __('site.reservation.check_in') }}</label>
                <input class="form-field" id="check_in" name="check_in" type="date" min="{{ now()->toDateString() }}" required value="{{ old('check_in', $reservation->check_in?->toDateString()) }}">
                @error('check_in')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold" for="check_out">{{ __('site.reservation.check_out') }}</label>
                <input class="form-field" id="check_out" name="check_out" type="date" min="{{ now()->addDay()->toDateString() }}" required value="{{ old('check_out', $reservation->check_out?->toDateString()) }}">
                @error('check_out')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="mb-2 block text-sm font-semibold" for="special_requests">{{ __('site.owner.reservations.special_requests') }}</label>
            <textarea class="form-field" id="special_requests" name="special_requests" rows="4" maxlength="2000">{{ old('special_requests', $reservation->special_requests) }}</textarea>
            @error('special_requests')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="gold-button">{{ $submitLabel }}</button>
            @if ($reservation->exists)
                <a class="rounded-full border border-[#8c6632] px-5 py-3 text-sm font-semibold text-[#704d20]" href="{{ route('owner.reservations.show', $reservation) }}">{{ __('site.owner.reservations.back_to_details') }}</a>
            @else
                <a class="rounded-full border border-[#8c6632] px-5 py-3 text-sm font-semibold text-[#704d20]" href="{{ route('owner.reservations.index') }}">{{ __('site.owner.reservations.back_to_list') }}</a>
            @endif
        </div>
    </form>
</div>
