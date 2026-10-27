<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransferReportRequest;
use App\Models\Payment;
use App\Models\PaymentReceipt;
use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $reservations = $request->user()
            ->reservations()
            ->with('payment')
            ->latest()
            ->paginate(15);

        return view('payments.index', ['reservations' => $reservations]);
    }

    public function show(Request $request, Reservation $reservation): View
    {
        $reservation = $request->user()
            ->reservations()
            ->with(['payment.receipts'])
            ->findOrFail($reservation->getKey());

        abort_unless($reservation->payment, 404);

        return view('payments.show', ['reservation' => $reservation]);
    }

    public function reportTransfer(
        StoreTransferReportRequest $request,
        Reservation $reservation,
    ): RedirectResponse {
        $reservation = $request->user()
            ->reservations()
            ->with('payment')
            ->findOrFail($reservation->getKey());
        $payment = $reservation->payment;

        abort_unless($payment, 404);

        abort_if(
            in_array($payment->status, [Payment::STATUS_VERIFIED], true),
            409,
            __('site.payment.errors.already_verified'),
        );

        $receipt = $request->validated('receipt');
        $storedPath = $receipt?->store('receipts', 'payment_receipts');

        try {
            DB::transaction(function () use ($request, $payment, $receipt, $storedPath): void {
                $lockedPayment = Payment::query()->lockForUpdate()->findOrFail($payment->getKey());

                abort_if(
                    $lockedPayment->status === Payment::STATUS_VERIFIED,
                    409,
                    __('site.payment.errors.already_verified'),
                );

                if ($receipt && $storedPath) {
                    $lockedPayment->receipts()->create([
                        'uploaded_by' => $request->user()->getAuthIdentifier(),
                        'path' => $storedPath,
                        'mime_type' => $receipt->getMimeType(),
                        'size_bytes' => $receipt->getSize(),
                    ]);
                }

                $lockedPayment->update([
                    'status' => Payment::STATUS_REPORTED,
                    'transfer_reported_at' => now(),
                ]);
            });
        } catch (Throwable $exception) {
            if ($storedPath !== null) {
                Storage::disk('payment_receipts')->delete($storedPath);
            }

            throw $exception;
        }

        return redirect()
            ->route('payments.show', $reservation)
            ->with('status', __('site.payment.reported'));
    }

    public function downloadReceipt(Request $request, PaymentReceipt $receipt): StreamedResponse
    {
        abort_unless(
            $receipt->payment()
                ->whereHas('reservation', fn ($query) => $query->where('user_id', $request->user()->getAuthIdentifier()))
                ->exists(),
            404,
        );

        return Storage::disk('payment_receipts')->download(
            $receipt->path,
            'payment-receipt-'.$receipt->getKey(),
            [
                'Cache-Control' => 'private, no-store, max-age=0',
                'Content-Type' => $receipt->mime_type,
            ],
        );
    }
}
