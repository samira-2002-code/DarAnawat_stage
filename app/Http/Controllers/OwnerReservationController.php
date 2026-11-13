<?php

namespace App\Http\Controllers;

use App\Http\Requests\CancelReservationRequest;
use App\Http\Requests\OwnerReservationRequest;
use App\Models\Payment;
use App\Models\PaymentDecision;
use App\Models\Reservation;
use App\Models\User;
use App\Support\ReservationPricing;
use App\Support\RoomAvailability;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OwnerReservationController extends Controller
{
    public function index(): View
    {
        $reservations = Reservation::query()
            ->with(['user', 'payment'])
            ->latest()
            ->paginate(15);

        return view('owner.reservations.index', ['reservations' => $reservations]);
    }

    public function create(): View
    {
        return view('owner.reservations.create', [
            'reservation' => new Reservation,
            'users' => $this->customers(),
        ]);
    }

    public function store(OwnerReservationRequest $request, RoomAvailability $availability): RedirectResponse
    {
        $data = $request->validated();
        $amountMad = ReservationPricing::amountFor(
            $data['room_type'],
            $data['check_in'],
            $data['check_out'],
        );

        $reservation = DB::transaction(function () use ($data, $amountMad, $availability): Reservation {
            $availability->lockAllCategories();
            $availability->assertCategoryAvailable(
                $data['room_type'],
                $data['check_in'],
                $data['check_out'],
            );

            $reservation = Reservation::create([
                ...$data,
                'status' => Reservation::STATUS_PENDING,
            ]);

            $reservation->payment()->create([
                'amount_mad' => $amountMad,
                'status' => Payment::STATUS_AWAITING_TRANSFER,
            ]);

            return $reservation;
        });

        return redirect()
            ->route('owner.reservations.show', $reservation)
            ->with('status', __('site.owner.reservations.created'));
    }

    public function show(Reservation $reservation, RoomAvailability $availability): View
    {
        $reservation->load(['user', 'room', 'payment.receipts', 'payment.decisions.administrator']);

        $availableRooms = $reservation->status === Reservation::STATUS_CONFIRMED
            ? $availability->availableRooms(
                $reservation->room_type,
                $reservation->check_in->toDateString(),
                $reservation->check_out->toDateString(),
                $reservation->getKey(),
            )
            : collect();

        return view('owner.reservations.show', [
            'reservation' => $reservation,
            'availableRooms' => $availableRooms,
        ]);
    }

    public function assignRoom(
        Request $request,
        Reservation $reservation,
        RoomAvailability $availability,
    ): RedirectResponse {
        $data = $request->validate([
            'room_id' => ['required', 'integer', Rule::exists('rooms', 'id')],
        ]);

        DB::transaction(function () use ($reservation, $availability, $data): void {
            $availability->lockAllCategories();
            $reservation = Reservation::query()->lockForUpdate()->findOrFail($reservation->getKey());

            abort_unless(
                $reservation->status === Reservation::STATUS_CONFIRMED,
                409,
                __('site.owner.errors.room_assignment_not_allowed'),
            );

            $room = $availability->assertRoomAvailable(
                (int) $data['room_id'],
                $reservation->room_type,
                $reservation->check_in->toDateString(),
                $reservation->check_out->toDateString(),
                $reservation->getKey(),
            );

            $reservation->room()->associate($room);
            $reservation->save();
        });

        return redirect()
            ->route('owner.reservations.show', $reservation)
            ->with('status', __('site.owner.rooms.assigned'));
    }

    public function edit(Reservation $reservation): View
    {
        $reservation->load('payment');

        return view('owner.reservations.edit', [
            'reservation' => $reservation,
            'users' => $this->customers(),
        ]);
    }

    public function update(
        OwnerReservationRequest $request,
        Reservation $reservation,
        RoomAvailability $availability,
    ): RedirectResponse {
        $data = $request->validated();

        DB::transaction(function () use ($data, $reservation, $availability): void {
            $availability->lockAllCategories();
            $reservation = Reservation::query()->lockForUpdate()->findOrFail($reservation->getKey());
            $payment = Payment::query()
                ->where('reservation_id', $reservation->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $pricingChanged = $reservation->room_type !== $data['room_type']
                || $reservation->check_in->toDateString() !== $data['check_in']
                || $reservation->check_out->toDateString() !== $data['check_out'];

            if ($pricingChanged) {
                abort_unless(
                    $reservation->status === Reservation::STATUS_PENDING
                        && $payment->status === Payment::STATUS_AWAITING_TRANSFER
                        && ! $payment->receipts()->exists()
                        && ! $payment->decisions()->exists(),
                    409,
                    __('site.owner.errors.reservation_not_editable'),
                );

                $availability->assertCategoryAvailable(
                    $data['room_type'],
                    $data['check_in'],
                    $data['check_out'],
                );

                $payment->update([
                    'amount_mad' => ReservationPricing::amountFor(
                        $data['room_type'],
                        $data['check_in'],
                        $data['check_out'],
                    ),
                ]);
            }

            $reservation->update($data);
        });

        return redirect()
            ->route('owner.reservations.show', $reservation)
            ->with('status', __('site.owner.reservations.updated'));
    }

    public function cancel(CancelReservationRequest $request, Reservation $reservation): RedirectResponse
    {
        DB::transaction(function () use ($request, $reservation): void {
            $reservation = Reservation::query()->lockForUpdate()->findOrFail($reservation->getKey());
            $payment = Payment::query()
                ->where('reservation_id', $reservation->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $reservation->status === Reservation::STATUS_PENDING
                    && $payment->status === Payment::STATUS_AWAITING_TRANSFER
                    && ! $payment->receipts()->exists()
                    && ! $payment->decisions()->exists(),
                409,
                __('site.owner.errors.reservation_not_cancellable'),
            );

            $reservation->update(['status' => Reservation::STATUS_CANCELLED]);
            $payment->decisions()->create([
                'administrator_id' => $request->user()->getAuthIdentifier(),
                'action' => PaymentDecision::ACTION_RESERVATION_CANCELLED,
                'reason' => $request->validated('reason'),
            ]);
        });

        return redirect()
            ->route('owner.reservations.show', $reservation)
            ->with('status', __('site.owner.reservations.cancelled'));
    }

    public function destroy(Reservation $reservation): RedirectResponse
    {
        DB::transaction(function () use ($reservation): void {
            $reservation = Reservation::query()->lockForUpdate()->findOrFail($reservation->getKey());
            $payment = Payment::query()
                ->where('reservation_id', $reservation->getKey())
                ->lockForUpdate()
                ->first();

            abort_unless(
                $reservation->status === Reservation::STATUS_PENDING
                    && $payment
                    && $payment->status === Payment::STATUS_AWAITING_TRANSFER
                    && ! $payment->receipts()->exists()
                    && ! $payment->decisions()->exists(),
                409,
                __('site.owner.errors.reservation_not_deletable'),
            );

            $reservation->delete();
        });

        return redirect()
            ->route('owner.reservations.index')
            ->with('status', __('site.owner.reservations.deleted'));
    }

    private function customers(): Collection
    {
        return User::query()
            ->where('role', 'customer')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }
}
