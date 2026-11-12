@props(['room', 'showSize' => true])

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
                <bdi dir="ltr">{{ \App\Support\Currency::formatDh($room['price']) }}</bdi>
                <span class="mt-1 block text-xs font-medium tracking-normal text-[#6c756e]">{{ __('site.common.per_night') }}</span>
            </p>
        </div>

        <p class="line-clamp-2 min-h-[3rem] text-sm leading-6 text-[#4d5b56]">{{ __('site.rooms.' . $room['slug'] . '.short_description') }}</p>

        <div class="flex flex-wrap gap-2 border-t border-[#eee5d5] pt-4 text-xs font-medium text-[#3b4b45]">
            <span class="rounded-full border border-[#d8c9a7] bg-[#f7f0e0] px-3 py-2">👤 {{ __('site.rooms.' . $room['slug'] . '.capacity') }}</span>
            <span class="rounded-full border border-[#d8c9a7] bg-[#f7f0e0] px-3 py-2">🛏 {{ __('site.rooms.' . $room['slug'] . '.bed') }}</span>
            @if ($showSize && $room['surface'])
                <span class="rounded-full border border-[#d8c9a7] bg-[#f7f0e0] px-3 py-2">{{ $room['surface'] }}</span>
            @endif
            <span class="rounded-full border border-[#d8c9a7] bg-[#f7f0e0] px-3 py-2">{{ __('site.details.breakfast.included_short') }}</span>
        </div>

        <div class="mt-auto grid gap-3 pt-2 sm:grid-cols-2">
            <a href="{{ route('chambre.details', ['type' => $room['slug']]) }}" class="gold-button w-full !px-3 !text-[0.68rem] !tracking-[0.1em]">
                {{ __('site.common.view_details') }}
            </a>
            <a href="{{ route('reservations.create', ['room' => $room['slug']]) }}" class="soft-button w-full !border-[#b98d4d] !bg-transparent !px-3 !text-[0.68rem] !text-[#122b29] !tracking-[0.1em] hover:!bg-[#f7f0e0]">
                {{ __('site.common.book_now') }}
            </a>
        </div>
    </div>
</article>
