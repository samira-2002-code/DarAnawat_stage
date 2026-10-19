@props(['room'])

@if (isset($room['slug']))
    <article id="chambre-{{ $room['slug'] }}" class="room-card group flex h-full flex-col overflow-hidden rounded-[2rem] border border-[#e8dcc0] bg-[#fffdf9] shadow-[var(--shadow)]">
        <a href="{{ route('chambre.details', ['type' => $room['slug']]) }}" class="h-[260px] w-full shrink-0 overflow-hidden" aria-label="{{ __('site.common.view_details') }} : {{ __('site.rooms.' . $room['slug'] . '.name') }}">
            <img
                src="{{ $room['image'] }}"
                alt="{{ __('site.rooms.' . $room['slug'] . '.name') }}"
                class="h-full w-full object-cover transition duration-700 group-hover:scale-[1.03]"
            >
        </a>

        <div class="flex flex-1 flex-col gap-4 p-6 md:p-7">
            <div class="flex items-start justify-between gap-3">
                <h3 class="font-display text-3xl font-semibold leading-tight text-[#1a241f]">{{ __('site.rooms.' . $room['slug'] . '.name') }}</h3>
                <p class="shrink-0 text-right text-sm font-semibold tracking-[0.08em] text-[#a67b3f]">
                    <bdi dir="ltr">{{ \App\Support\Currency::formatMad($room['price']) }}</bdi>
                    <span class="mt-1 block text-xs font-medium tracking-normal text-[#6c756e]">{{ __('site.common.per_night') }}</span>
                </p>
            </div>

            <p class="line-clamp-2 min-h-[3rem] text-sm leading-6 text-[#4d5b56]">{{ __('site.rooms.' . $room['slug'] . '.short_description') }}</p>

            <div class="flex flex-wrap gap-2 border-t border-[#eee5d5] pt-4 text-xs font-medium text-[#3b4b45]">
                <span class="rounded-full border border-[#d8c9a7] bg-[#f7f0e0] px-3 py-2">👤 {{ __('site.rooms.' . $room['slug'] . '.capacity') }}</span>
                <span class="rounded-full border border-[#d8c9a7] bg-[#f7f0e0] px-3 py-2">🛏 {{ __('site.rooms.' . $room['slug'] . '.bed') }}</span>
                <span class="rounded-full border border-[#d8c9a7] bg-[#f7f0e0] px-3 py-2">{{ $room['surface'] }}</span>
            </div>

            <a href="{{ route('chambre.details', ['type' => $room['slug']]) }}" class="gold-button mt-auto w-full">
                {{ __('site.common.view_details') }}
            </a>
        </div>
    </article>
@else
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
            <span class="text-sm font-semibold uppercase tracking-[0.18em] text-[#a67b3f]"><bdi dir="ltr">{{ \App\Support\Currency::formatMad($room['price']) }}</bdi> / {{ __('site.common.per_night') }}</span>
        </div>

        <div class="flex flex-wrap gap-3 text-xs font-medium uppercase tracking-[0.16em] text-[#3b4b45]">
            <span class="rounded-full border border-[#d8c9a7] bg-[#f7f0e0] px-3 py-2">{{ $room['guests'] }} {{ __('site.common.guests') }}</span>
            <span class="rounded-full border border-[#d8c9a7] bg-[#f7f0e0] px-3 py-2">{{ __('site.home.rooms.' . $room['translation_key'] . '_bed') }}</span>
        </div>

        <p class="text-base leading-7 text-[#4d5b56]">{{ __('site.home.rooms.' . $room['translation_key'] . '_description') }}</p>

        <button class="soft-button w-full justify-center" type="button">{{ __('site.common.view_room') }}</button>
    </div>
</article>
@endif
