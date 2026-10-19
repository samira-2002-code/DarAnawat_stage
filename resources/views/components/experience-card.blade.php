@props(['experience'])

<article class="group rounded-[2rem] border border-[#e7dcc8] bg-white p-6 shadow-[0_22px_50px_rgba(18,46,42,0.06)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_30px_60px_rgba(18,46,42,0.12)]">
    <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-full bg-[#efe4d0] text-base font-semibold text-[#1a241f]">
        {{ $experience['icon'] }}
    </div>
    <p class="mb-3 text-xs font-semibold uppercase tracking-[0.22em] text-[#a67b3f]">{{ $experience['title'] }}</p>
    <h3 class="font-display text-3xl font-semibold text-[#1a241f]">{{ $experience['heading'] }}</h3>
    <p class="mt-4 text-base leading-7 text-[#4d5b56]">{{ $experience['description'] }}</p>
</article>
