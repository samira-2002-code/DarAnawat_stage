@props(['room'])

<article id="chambre-{{ $room['slug'] }}" class="room-card group flex h-full flex-col overflow-hidden rounded-[2rem] border border-[#e8dcc0] bg-[#fffdf9] shadow-[var(--shadow)]">
    <a href="{{ route('chambre.details', ['type' => $room['slug']]) }}" class="h-[260px] w-full shrink-0 overflow-hidden" aria-label="Voir les détails : {{ $room['name'] }}">
        <img
            src="{{ $room['image'] }}"
            alt="{{ $room['name'] }}"
            class="h-full w-full object-cover transition duration-700 group-hover:scale-[1.03]"
        >
    </a>

    <div class="flex flex-1 flex-col gap-4 p-6 md:p-7">
        <div class="flex items-start justify-between gap-3">
            <h3 class="font-display text-3xl font-semibold leading-tight text-[#1a241f]">{{ $room['name'] }}</h3>
            <p class="shrink-0 text-right text-sm font-semibold tracking-[0.08em] text-[#a67b3f]">
                {{ number_format($room['price'], 0, ',', ' ') }} MAD
                <span class="mt-1 block text-xs font-medium tracking-normal text-[#6c756e]">par nuit</span>
            </p>
        </div>

        <p class="line-clamp-2 min-h-[3rem] text-sm leading-6 text-[#4d5b56]">{{ $room['short_description'] ?? $room['description'] }}</p>

        <div class="flex flex-wrap gap-2 border-t border-[#eee5d5] pt-4 text-xs font-medium text-[#3b4b45]">
            <span class="rounded-full border border-[#d8c9a7] bg-[#f7f0e0] px-3 py-2">👤 {{ $room['capacity'] }}</span>
            <span class="rounded-full border border-[#d8c9a7] bg-[#f7f0e0] px-3 py-2">🛏 {{ $room['bed'] }}</span>
            <span class="rounded-full border border-[#d8c9a7] bg-[#f7f0e0] px-3 py-2">{{ $room['surface'] }}</span>
        </div>

        <a href="{{ route('chambre.details', ['type' => $room['slug']]) }}" class="gold-button mt-auto w-full">
            Voir détails
        </a>
    </div>
</article>
