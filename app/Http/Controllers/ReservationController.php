<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Models\Payment;
use App\Models\Reservation;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use LogicException;

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
            'rooms' => array_keys(Reservation::ROOM_CAPACITIES),
            'selectedRoom' => $selectedRoom,
        ]);
    }

    public function store(StoreReservationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $nightlyRate = config('rooms.rates_mad.'.$validated['room_type']);

        if (! is_int($nightlyRate) || $nightlyRate < 1) {
            throw new LogicException('A valid room rate must be configured for every reservable room.');
        }

        $nights = CarbonImmutable::parse($validated['check_in'])
            ->diffInDays(CarbonImmutable::parse($validated['check_out']));
        $amountMad = $nightlyRate * $nights;

        $reservation = DB::transaction(function () use ($request, $validated, $amountMad): Reservation {
            $reservation = Reservation::create([
                ...$validated,
                'user_id' => $request->user()?->getAuthIdentifier(),
                'status' => 'pending',
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
