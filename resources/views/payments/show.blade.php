@extends('layouts.app')

@section('title', __('site.payment.page_title') . ' | Dar Anawat')
@section('meta_description', __('site.payment.instructions'))

@section('content')
    <main class="min-h-screen bg-[#f9f5ef] text-[#1d2d2a]">
        <section class="hero relative overflow-hidden pb-16 pt-32 text-white md:pb-20 md:pt-40">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(201,167,108,0.2),transparent_45%)]"></div>
            <x-navbar />
            <div class="section-shell relative">
                <p class="section-kicker !text-[#ebd3a4]">{{ __('site.payment.kicker') }}</p>
                <h1 class="font-display max-w-3xl text-5xl leading-[0.95] tracking-[-0.04em] md:text-7xl">
                    {{ __('site.payment.page_title') }}
                </h1>
                <p class="mt-6 max-w-2xl text-sm leading-7 text-white/75 md:text-base">
                    {{ __('site.payment.instructions') }}
                </p>
            </div>
        </section>

        <section class="section-shell -mt-8 relative z-10 pb-20 md:-mt-10 md:pb-28">
            <div class="mx-auto max-w-4xl rounded-[2rem] border border-[#e7ddca] bg-[#fffdf9] p-5 shadow-[var(--shadow)] sm:p-8 md:p-12">
                @if (session('status'))
                    <div class="mb-8 rounded-2xl border border-[#b8d5c0] bg-[#edf7ef] p-5 text-sm leading-6 text-[#204d30]" role="status">
                        {{ session('status') }}
                    </div>
                @endif

                <section class="rounded-2xl border border-[#e7ddca] bg-[#f9f5ef] p-5 sm:p-6" aria-labelledby="transfer-details-title">
                    <h2 id="transfer-details-title" class="font-display text-2xl text-[#122b29]">{{ __('site.payment.details') }}</h2>
                    <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="font-semibold text-[#122b29]">{{ __('site.payment.reservation') }}</dt>
                            <dd class="mt-1">#{{ $reservation->getKey() }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-[#122b29]">{{ __('site.payment.amount') }}</dt>
                            <dd class="mt-1 font-semibold"><bdi dir="ltr">{{ \App\Support\Currency::formatMad($reservation->payment->amount_mad, 'fr') }}</bdi></dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-[#122b29]">{{ __('site.payment.reservation_status') }}</dt>
                            <dd class="mt-1">{{ __('site.payment.reservation_statuses.' . $reservation->status) }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-[#122b29]">{{ __('site.payment.status') }}</dt>
                            <dd class="mt-1">{{ __('site.payment.statuses.' . $reservation->payment->status) }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-[#122b29]">{{ __('site.payment.bank') }}</dt>
                            <dd class="mt-1">{{ config('payment.bank_name') }}</dd>
                        </div>
                        @if (config('payment.beneficiary'))
                            <div>
                                <dt class="font-semibold text-[#122b29]">{{ __('site.payment.beneficiary') }}</dt>
                                <dd class="mt-1">{{ config('payment.beneficiary') }}</dd>
                            </div>
                        @endif
                        <div class="sm:col-span-2">
                            <dt class="font-semibold text-[#122b29]">{{ __('site.payment.rib') }}</dt>
                            <dd class="mt-1 break-all font-mono text-base tracking-wide" data-rib-value>{{ config('payment.rib') }}</dd>
                        </div>
                    </dl>
                    <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <button
                            type="button"
                            class="gold-button"
                            data-copy-rib
                            data-rib="{{ config('payment.rib') }}"
                        >
                            {{ __('site.payment.copy_rib') }}
                        </button>
                        <span class="text-sm text-[#4d5b56]" data-copy-rib-status aria-live="polite"></span>
                    </div>
                    <p class="mt-5 rounded-xl bg-white p-4 text-sm leading-6 text-[#4d5b56]">
                        {{ $reservation->payment->status === \App\Models\Payment::STATUS_VERIFIED
                            ? __('site.payment.verified_message')
                            : __('site.payment.pending_verification') }}
                    </p>
                </section>

                @if ($reservation->payment->status !== \App\Models\Payment::STATUS_VERIFIED)
                    <section class="mt-8" aria-labelledby="transfer-report-title">
                        <h2 id="transfer-report-title" class="font-display text-2xl text-[#122b29]">{{ __('site.payment.report_title') }}</h2>
                        <p class="mt-2 text-sm leading-6 text-[#4d5b56]">{{ __('site.payment.report_instructions') }}</p>

                        @if ($errors->any())
                            <div class="mt-5 rounded-2xl border border-[#e7b6ae] bg-[#fff1ef] p-5 text-sm text-[#7b2e24]" role="alert">
                                <ul class="list-inside list-disc space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('payments.report', $reservation) }}" method="POST" enctype="multipart/form-data" class="mt-5 space-y-4">
                            @csrf
                            <div>
                                <label for="receipt" class="mb-2 block text-sm font-medium">{{ __('site.payment.receipt') }}</label>
                                <input id="receipt" name="receipt" type="file" accept=".pdf,.jpg,.jpeg,.png" class="form-field">
                                <p class="mt-2 text-xs text-[#6c756e]">{{ __('site.payment.receipt_help') }}</p>
                            </div>
                            <button type="submit" class="gold-button">{{ __('site.payment.report_button') }}</button>
                        </form>
                    </section>
                @endif

                @if ($reservation->payment->receipts->isNotEmpty())
                    <section class="mt-8" aria-labelledby="receipts-title">
                        <h2 id="receipts-title" class="font-display text-2xl text-[#122b29]">{{ __('site.payment.receipts') }}</h2>
                        <ul class="mt-4 space-y-3">
                            @foreach ($reservation->payment->receipts as $receipt)
                                <li class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-[#e7ddca] p-4 text-sm">
                                    <span>{{ __('site.payment.receipt_statuses.' . $receipt->status) }}</span>
                                    <a class="font-semibold text-[#8c6632] underline" href="{{ route('payments.receipts.download', $receipt) }}">
                                        {{ __('site.payment.download_receipt') }}
                                    </a>
                                    @if ($receipt->review_reason)
                                        <p class="w-full text-[#6c756e]">{{ $receipt->review_reason }}</p>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif
            </div>
        </section>
    </main>

    <script>
        const copyRibButton = document.querySelector('[data-copy-rib]');

        const copyText = async (text) => {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(text);
                return;
            }

            const temporaryInput = document.createElement('textarea');
            temporaryInput.value = text;
            temporaryInput.setAttribute('readonly', '');
            temporaryInput.style.position = 'fixed';
            temporaryInput.style.opacity = '0';
            document.body.append(temporaryInput);
            temporaryInput.select();
            const copied = document.execCommand('copy');
            temporaryInput.remove();

            if (!copied) {
                throw new Error('Clipboard copy failed.');
            }
        };

        if (copyRibButton) {
            copyRibButton.addEventListener('click', async () => {
                const status = document.querySelector('[data-copy-rib-status]');

                try {
                    await copyText(copyRibButton.dataset.rib);
                    status.textContent = @js(__('site.payment.copy_success'));
                } catch {
                    status.textContent = @js(__('site.payment.copy_error'));
                }
            });
        }
    </script>
@endsection
