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
                            @if ($receipt->status === 'submitted' && in_array($reservation->payment->status, ['awaiting_transfer', 'reported'], true))
                                <form action="{{ route('owner.receipts.reject', $receipt) }}" method="POST" class="flex flex-wrap items-center gap-2">
                                    @csrf
                                    <label class="sr-only" for="reason-{{ $receipt->getKey() }}">{{ __('site.owner.reason') }}</label>
                                    <input id="reason-{{ $receipt->getKey() }}" name="reason" required minlength="3" maxlength="1000" class="form-field min-w-48" placeholder="{{ __('site.owner.reason') }}">
                                    <button type="submit" class="rounded-full border border-[#a64b3c] px-4 py-2 text-xs font-semibold text-[#8a352b]">{{ __('site.owner.reject_receipt') }}</button>
                                </form>
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

            @if ($reservation->status === 'pending' && in_array($reservation->payment->status, ['awaiting_transfer', 'reported'], true))
                <div class="mt-5 grid gap-4 border-t border-[#e7ddca] pt-5 lg:grid-cols-2">
                    <form action="{{ route('owner.payments.verify', $reservation->payment) }}" method="POST" class="space-y-3 rounded-xl bg-[#edf7ef] p-4">
                        @csrf
                        <p class="text-sm font-semibold text-[#204d30]">{{ __('site.owner.verify_warning') }}</p>
                        <label class="flex items-start gap-2 text-sm">
                            <input name="funds_received" type="checkbox" value="1" required class="mt-1">
                            <span>{{ __('site.owner.funds_received') }}</span>
                        </label>
                        <button type="submit" class="gold-button">{{ __('site.owner.verify_payment') }}</button>
                    </form>

                    <form action="{{ route('owner.payments.not-received', $reservation->payment) }}" method="POST" class="space-y-3 rounded-xl bg-[#fff1ef] p-4">
                        @csrf
                        <label class="block text-sm font-semibold" for="not-received-{{ $reservation->payment->getKey() }}">{{ __('site.owner.reason') }}</label>
                        <textarea id="not-received-{{ $reservation->payment->getKey() }}" name="reason" required minlength="3" maxlength="1000" rows="2" class="form-field"></textarea>
                        <button type="submit" class="rounded-full border border-[#a64b3c] px-5 py-3 text-sm font-semibold text-[#8a352b]">{{ __('site.owner.mark_not_received') }}</button>
                    </form>
                </div>
            @endif

            @if ($reservation->status === 'pending' && $reservation->payment->status === 'verified')
                <form action="{{ route('owner.reservations.confirm', $reservation) }}" method="POST" class="mt-5 space-y-3 border-t border-[#e7ddca] pt-5">
                    @csrf
                    <p class="text-sm font-semibold">{{ __('site.owner.confirm_warning') }}</p>
                    <label class="flex items-start gap-2 text-sm">
                        <input name="availability_confirmed" type="checkbox" value="1" required class="mt-1">
                        <span>{{ __('site.owner.availability_confirmed') }}</span>
                    </label>
                    <label class="block text-sm font-semibold" for="confirm-reason-{{ $reservation->getKey() }}">{{ __('site.owner.confirm_note') }}</label>
                    <textarea id="confirm-reason-{{ $reservation->getKey() }}" name="reason" maxlength="1000" rows="2" class="form-field"></textarea>
                    <button type="submit" class="gold-button">{{ __('site.owner.confirm_reservation') }}</button>
                </form>
            @endif
        </div>
    @endif
</article>
