@props(['room'])

<article class="room-card group overflow-hidden rounded-[2rem] border border-[#e8dcc0] bg-[#fffdf9] shadow-[var(--shadow)]">
    <div class="overflow-hidden">
        <img
            src="{{ $room['image'] }}"
            alt="{{ __('site.home.rooms.' . $room['translation_key'] . '_name') }}"
            class="h-72 w-full object-cover transition duration-700 group-hover:scale-105"
        >
    </div>

    <div class="space-y-5 p-6 md:p-7">
        <div class="flex items-start justify-between gap-4">
            <h3 class="font-display text-3xl font-semibold text-[#1a241f]">{{ __('site.home.rooms.' . $room['translation_key'] . '_name') }}</h3>
            <span class="text-sm font-semibold uppercase tracking-[0.18em] text-[#a67b3f]">{{ $room['price'] }}</span>
        </div>

        <div class="flex flex-wrap gap-3 text-xs font-medium uppercase tracking-[0.16em] text-[#3b4b45]">
            <span class="rounded-full border border-[#d8c9a7] bg-[#f7f0e0] px-3 py-2">{{ $room['guests'] }} {{ __('site.common.guests') }}</span>
            <span class="rounded-full border border-[#d8c9a7] bg-[#f7f0e0] px-3 py-2">{{ __('site.home.rooms.' . $room['translation_key'] . '_bed') }}</span>
        </div>

        <p class="text-base leading-7 text-[#4d5b56]">{{ __('site.home.rooms.' . $room['translation_key'] . '_description') }}</p>

        <button class="soft-button w-full justify-center" type="button">{{ __('site.common.view_room') }}</button>
    </div>
</article>
