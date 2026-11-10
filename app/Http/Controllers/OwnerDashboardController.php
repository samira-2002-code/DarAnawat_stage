<?php

namespace App\Http\Controllers;

use App\Models\PaymentReceipt;
use App\Models\Reservation;
use App\Models\Room;
use App\Support\RoomAvailability;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OwnerDashboardController extends Controller
{
    public function index(RoomAvailability $availability): View
    {
        $pendingReservations = Reservation::query()
            ->with(['user', 'room', 'payment.receipts', 'payment.decisions.administrator'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(15, ['*'], 'pendingPage');

        $confirmedReservations = Reservation::query()
            ->with(['user', 'room', 'payment'])
            ->where('status', 'confirmed')
            ->latest()
            ->paginate(15, ['*'], 'confirmedPage');

        $roomOptions = [];

        foreach ($pendingReservations as $reservation) {
            $hasInventory = Room::query()->where('room_type', $reservation->room_type)->exists();
            $roomOptions[$reservation->getKey()] = [
                'hasInventory' => $hasInventory,
                'rooms' => $hasInventory
                    ? $availability->availableRooms(
                        $reservation->room_type,
                        $reservation->check_in->toDateString(),
                        $reservation->check_out->toDateString(),
                        $reservation->getKey(),
                    )
                    : collect(),
            ];
        }

        return view('owner.dashboard', [
            'pendingReservations' => $pendingReservations,
            'confirmedReservations' => $confirmedReservations,
            'roomOptions' => $roomOptions,
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
