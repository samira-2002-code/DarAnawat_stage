<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Models\Payment;
use App\Models\Reservation;
use App\Support\ReservationPricing;
use App\Support\RoomCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function create(Request $request): View
    {
        $selectedRoom = $request->query('room');

        abort_if(
            $selectedRoom !== null
                && (! is_string($selectedRoom) || ! array_key_exists($selectedRoom, Reservation::ROOM_CAPACITIES)),
            404,
        );

        return view('reservations.create', [
            'rooms' => RoomCatalog::all(),
            'selectedRoom' => $selectedRoom,
        ]);
    }

    public function store(StoreReservationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $amountMad = ReservationPricing::amountFor(
            $validated['room_type'],
            $validated['check_in'],
            $validated['check_out'],
        );

        $reservation = DB::transaction(function () use ($request, $validated, $amountMad): Reservation {
            $reservation = Reservation::create([
                ...$validated,
                'user_id' => $request->user()?->getAuthIdentifier(),
                'status' => Reservation::STATUS_PENDING,
            ]);

            $reservation->payment()->create([
                'amount_mad' => $amountMad,
                'status' => Payment::STATUS_AWAITING_TRANSFER,
            ]);

            return $reservation;
        });

        if ($request->user()) {
            return redirect()->route('payments.show', $reservation);
        }

        return redirect()
            ->route('reservations.create')
            ->with('reservation_success', __('site.reservation.success', ['reference' => $reservation->getKey()]))
            ->with('reservation_amount_mad', $amountMad);
    }
}
