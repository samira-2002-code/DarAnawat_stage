<div class="mx-auto max-w-3xl rounded-[2rem] border border-[#e7ddca] bg-[#fffdf9] p-5 shadow-[var(--shadow)] sm:p-8">
    @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-[#e7b6ae] bg-[#fff1ef] p-5 text-sm text-[#7b2e24]" role="alert">
            <ul class="list-inside list-disc space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    <form action="{{ $action }}" method="POST" class="space-y-5">
        @csrf
        @if ($method !== 'POST') @method($method) @endif
        <div>
            <label class="mb-2 block text-sm font-semibold" for="room_number">{{ __('site.owner.rooms.number') }}</label>
            <input id="room_number" class="form-field" name="room_number" maxlength="40" required value="{{ old('room_number', $room->room_number) }}">
            @error('room_number')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold" for="room_type">{{ __('site.owner.room') }}</label>
            <select id="room_type" class="form-field" name="room_type" required>
                @foreach ($roomTypes as $type => $capacity)
                    <option value="{{ $type }}" @selected(old('room_type', $room->room_type) === $type)>{{ __('site.rooms.'.$type.'.name') }}</option>
                @endforeach
            </select>
            @error('room_type')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold" for="price_per_night">{{ __('site.owner.rooms.price') }} (MAD)</label>
            <input id="price_per_night" class="form-field" name="price_per_night" type="number" min="1" max="100000000" step="1" required value="{{ old('price_per_night', $room->price_per_night) }}">
            @error('price_per_night')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold" for="status">{{ __('site.owner.rooms.status') }}</label>
            <select id="status" class="form-field" name="status" required>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $room->status) === $status)>{{ __('site.owner.rooms.statuses.'.$status) }}</option>
                @endforeach
            </select>
            @error('status')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>
        <div class="flex flex-wrap gap-3">
            <button type="submit" class="gold-button">{{ $submitLabel }}</button>
            <a class="rounded-full border border-[#8c6632] px-5 py-3 text-sm font-semibold text-[#704d20]" href="{{ route('owner.rooms.index') }}">{{ __('site.owner.rooms.back_to_list') }}</a>
        </div>
    </form>
</div>
