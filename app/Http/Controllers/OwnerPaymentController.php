<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfirmReservationRequest;
use App\Http\Requests\ReviewPaymentRequest;
use App\Http\Requests\VerifyPaymentRequest;
use App\Models\Payment;
use App\Models\PaymentDecision;
use App\Models\PaymentReceipt;
use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class OwnerPaymentController extends Controller
{
    public function verify(VerifyPaymentRequest $request, Payment $payment): RedirectResponse
    {
        DB::transaction(function () use ($request, $payment): void {
            $payment = Payment::query()->lockForUpdate()->findOrFail($payment->getKey());

            abort_unless(
                in_array($payment->status, [Payment::STATUS_AWAITING_TRANSFER, Payment::STATUS_REPORTED], true),
                409,
                __('site.owner.errors.payment_not_reviewable'),
            );

            $payment->update(['status' => Payment::STATUS_VERIFIED]);
            $payment->receipts()->where('status', 'submitted')->update([
                'status' => 'accepted',
                'reviewed_by' => $request->user()->getAuthIdentifier(),
                'reviewed_at' => now(),
            ]);
            $payment->decisions()->create([
                'administrator_id' => $request->user()->getAuthIdentifier(),
                'action' => PaymentDecision::ACTION_VERIFIED,
            ]);
        });

        return redirect()->route('owner.dashboard')->with('status', __('site.owner.payment_verified'));
    }

    public function rejectReceipt(
        ReviewPaymentRequest $request,
        PaymentReceipt $receipt,
    ): RedirectResponse {
        DB::transaction(function () use ($request, $receipt): void {
            $payment = Payment::query()->lockForUpdate()->findOrFail($receipt->payment_id);
            $receipt = PaymentReceipt::query()->lockForUpdate()->findOrFail($receipt->getKey());

            abort_unless(
                $receipt->payment_id === $payment->getKey()
                    && $receipt->status === 'submitted'
                    && in_array($payment->status, [Payment::STATUS_AWAITING_TRANSFER, Payment::STATUS_REPORTED], true),
                409,
                __('site.owner.errors.payment_not_reviewable'),
            );

            $receipt->update([
                'status' => 'rejected',
                'review_reason' => $request->validated('reason'),
                'reviewed_by' => $request->user()->getAuthIdentifier(),
                'reviewed_at' => now(),
            ]);
            $payment->update(['status' => Payment::STATUS_REJECTED]);
            $payment->decisions()->create([
                'administrator_id' => $request->user()->getAuthIdentifier(),
                'action' => PaymentDecision::ACTION_REJECTED,
                'reason' => $request->validated('reason'),
            ]);
        });

        return redirect()->route('owner.dashboard')->with('status', __('site.owner.receipt_rejected'));
    }

    public function markNotReceived(
        ReviewPaymentRequest $request,
        Payment $payment,
    ): RedirectResponse {
        DB::transaction(function () use ($request, $payment): void {
            $payment = Payment::query()->lockForUpdate()->findOrFail($payment->getKey());

            abort_unless(
                in_array($payment->status, [Payment::STATUS_AWAITING_TRANSFER, Payment::STATUS_REPORTED], true),
                409,
                __('site.owner.errors.payment_not_reviewable'),
            );

            $payment->update(['status' => Payment::STATUS_NOT_RECEIVED]);
            $payment->receipts()->where('status', 'submitted')->update([
                'status' => 'rejected',
                'review_reason' => $request->validated('reason'),
                'reviewed_by' => $request->user()->getAuthIdentifier(),
                'reviewed_at' => now(),
            ]);
            $payment->decisions()->create([
                'administrator_id' => $request->user()->getAuthIdentifier(),
                'action' => PaymentDecision::ACTION_NOT_RECEIVED,
                'reason' => $request->validated('reason'),
            ]);
        });

        return redirect()->route('owner.dashboard')->with('status', __('site.owner.payment_not_received'));
    }

    public function confirmReservation(
        ConfirmReservationRequest $request,
        Reservation $reservation,
    ): RedirectResponse {
        DB::transaction(function () use ($request, $reservation): void {
            $reservation = Reservation::query()->lockForUpdate()->findOrFail($reservation->getKey());
            $payment = Payment::query()
                ->where('reservation_id', $reservation->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $reservation->status === 'pending' && $payment->status === Payment::STATUS_VERIFIED,
                409,
                __('site.owner.errors.reservation_not_confirmable'),
            );

            $reservation->update(['status' => 'confirmed']);
            $payment->decisions()->create([
                'administrator_id' => $request->user()->getAuthIdentifier(),
                'action' => PaymentDecision::ACTION_RESERVATION_CONFIRMED,
                'reason' => $request->validated('reason'),
            ]);
        });

        return redirect()->route('owner.dashboard')->with('status', __('site.owner.reservation_confirmed'));
    }
}
