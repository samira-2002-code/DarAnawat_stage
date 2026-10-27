<?php

namespace App\Http\Controllers;

use App\Models\PaymentReceipt;
use App\Models\Reservation;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OwnerDashboardController extends Controller
{
    public function index(): View
    {
        $pendingReservations = Reservation::query()
            ->with(['user', 'payment.receipts', 'payment.decisions.administrator'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(15, ['*'], 'pendingPage');

        $confirmedReservations = Reservation::query()
            ->with(['user', 'payment'])
            ->where('status', 'confirmed')
            ->latest()
            ->paginate(15, ['*'], 'confirmedPage');

        return view('owner.dashboard', [
            'pendingReservations' => $pendingReservations,
            'confirmedReservations' => $confirmedReservations,
        ]);
    }

    public function downloadReceipt(PaymentReceipt $receipt): StreamedResponse
    {
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
