<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Models\Reservation;
use App\Support\RoomCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $reservation = Reservation::create([
            ...$request->validated(),
            'user_id' => $request->user()?->getAuthIdentifier(),
            'status' => 'pending',
        ]);

        return redirect()
            ->route('reservations.create')
            ->with('reservation_success', __('site.reservation.success', ['reference' => $reservation->getKey()]));
    }
}
