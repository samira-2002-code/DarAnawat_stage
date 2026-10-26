<article class="mb-5 rounded-2xl border border-[#e7ddca] p-5 last:mb-0">
    <div class="grid gap-4 text-sm sm:grid-cols-2 lg:grid-cols-4">
        <div>
            <h3 class="font-display text-xl text-[#122b29]">{{ __('site.owner.reservation') }} #{{ $reservation->getKey() }}</h3>
            <p class="mt-2">{{ $reservation->user?->name ?? $reservation->guest_name }}</p>
            <p class="text-[#6c756e]">{{ $reservation->user?->email ?? $reservation->guest_email }}</p>
        </div>
        <div>
            <p class="font-semibold">{{ __('site.owner.room') }}</p>
            <p class="mt-1">{{ __('site.rooms.' . $reservation->room_type . '.name') }} · {{ $reservation->guests }}</p>
        </div>
        <div>
            <p class="font-semibold">{{ __('site.owner.dates') }}</p>
            <p class="mt-1">{{ $reservation->check_in->format('d/m/Y') }} – {{ $reservation->check_out->format('d/m/Y') }}</p>
        </div>
        <div>
            <p class="font-semibold">{{ __('site.owner.amount') }}</p>
            @if ($reservation->payment)
                <p class="mt-1"><bdi dir="ltr">{{ \App\Support\Currency::formatMad($reservation->payment->amount_mad, 'fr') }}</bdi></p>
                <p class="mt-2">{{ __('site.payment.statuses.' . $reservation->payment->status) }}</p>
            @else
                <p class="mt-1">—</p>
            @endif
        </div>
    </div>

    @if ($reservation->payment)
        <div class="mt-5 border-t border-[#e7ddca] pt-5">
            @if ($reservation->payment->receipts->isNotEmpty())
                <h4 class="font-semibold">{{ __('site.owner.receipts') }}</h4>
                <ul class="mt-3 space-y-3">
                    @foreach ($reservation->payment->receipts as $receipt)
                        <li class="flex flex-wrap items-center gap-3 rounded-xl bg-[#f9f5ef] p-3 text-sm">
                            <span>{{ __('site.payment.receipt_statuses.' . $receipt->status) }}</span>
                            <a class="font-semibold text-[#8c6632] underline" href="{{ route('owner.receipts.download', $receipt) }}">
                                {{ __('site.owner.open_receipt') }}
                            </a>
                            @if ($receipt->review_reason)
                                <span class="text-[#6c756e]">{{ $receipt->review_reason }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-[#6c756e]">{{ __('site.owner.no_receipt') }}</p>
            @endif

            @if ($reservation->payment->decisions->isNotEmpty())
                <details class="mt-5">
                    <summary class="cursor-pointer font-semibold text-[#8c6632]">{{ __('site.owner.history') }}</summary>
                    <ul class="mt-3 space-y-2 text-sm">
                        @foreach ($reservation->payment->decisions as $decision)
                            <li class="rounded-xl bg-[#f9f5ef] p-3">
                                <span class="font-semibold">{{ __('site.owner.actions.' . $decision->action) }}</span>
                                <span> · {{ $decision->administrator?->name ?? '—' }} · {{ $decision->created_at->format('d/m/Y H:i') }}</span>
                                @if ($decision->reason)<p class="mt-1 text-[#6c756e]">{{ $decision->reason }}</p>@endif
                            </li>
                        @endforeach
                    </ul>
                </details>
            @endif

        </div>
    @endif
</article>
