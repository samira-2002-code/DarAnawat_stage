<?php

namespace App\Support;

use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use LogicException;

class RoomAvailability
{
    public function lockAllCategories(): void
    {
        $roomTypes = array_keys(Reservation::ROOM_CAPACITIES);
        sort($roomTypes, SORT_STRING);

        foreach ($roomTypes as $roomType) {
            $lock = DB::table('room_availability_locks')->where('room_type', $roomType);

            if (DB::connection()->getDriverName() === 'sqlite' && $lock->increment('lock_version') !== 1) {
                throw new LogicException("Missing availability lock for room category [{$roomType}].");
            }
        }

        $lockedRoomTypes = DB::table('room_availability_locks')
            ->whereIn('room_type', $roomTypes)
            ->orderBy('room_type')
            ->lockForUpdate()
            ->pluck('room_type')
            ->all();

        if ($lockedRoomTypes !== $roomTypes) {
            throw new LogicException('Availability locks do not match the configured room categories.');
        }
    }

    public function isAvailable(
        string $roomType,
        string $checkIn,
        string $checkOut,
        ?int $exceptReservationId = null,
    ): bool {
        return ! Reservation::query()
            ->where('room_type', $roomType)
            ->where('status', Reservation::STATUS_CONFIRMED)
            ->whereDate('check_in', '<', $checkOut)
            ->whereDate('check_out', '>', $checkIn)
            ->when(
                $exceptReservationId !== null,
                fn ($query) => $query->where('id', '<>', $exceptReservationId),
            )
            ->exists();
    }

    public function availableRooms(
        string $roomType,
        string $checkIn,
        string $checkOut,
        ?int $exceptReservationId = null,
    ): Collection {
        return Room::query()
            ->where('room_type', $roomType)
            ->where('status', Room::STATUS_AVAILABLE)
            ->whereDoesntHave('reservations', function ($query) use ($checkIn, $checkOut, $exceptReservationId): void {
                $query->where('status', Reservation::STATUS_CONFIRMED)
                    ->whereDate('check_in', '<', $checkOut)
                    ->whereDate('check_out', '>', $checkIn)
                    ->when(
                        $exceptReservationId !== null,
                        fn ($reservationQuery) => $reservationQuery->where('id', '<>', $exceptReservationId),
                    );
            })
            ->whereNotExists(function ($query) use (
                $roomType,
                $checkIn,
                $checkOut,
                $exceptReservationId,
            ): void {
                $query->selectRaw('1')
                    ->from('reservations')
                    ->whereNull('reservations.room_id')
                    ->where('reservations.room_type', $roomType)
                    ->where('reservations.status', Reservation::STATUS_CONFIRMED)
                    ->whereDate('reservations.check_in', '<', $checkOut)
                    ->whereDate('reservations.check_out', '>', $checkIn)
                    ->when(
                        $exceptReservationId !== null,
                        fn ($legacyQuery) => $legacyQuery->where('reservations.id', '<>', $exceptReservationId),
                    );
            })
            ->orderBy('room_number')
            ->get();
    }

    public function assertCategoryAvailable(string $roomType, string $checkIn, string $checkOut): void
    {
        if (! Room::query()->where('room_type', $roomType)->exists()) {
            $this->assertAvailable($roomType, $checkIn, $checkOut);

            return;
        }

        if ($this->availableRooms($roomType, $checkIn, $checkOut)->isEmpty()) {
            throw ValidationException::withMessages([
                'check_in' => __('site.reservation.errors.unavailable'),
            ]);
        }
    }

    public function assertRoomAvailable(
        int $roomId,
        string $roomType,
        string $checkIn,
        string $checkOut,
        ?int $exceptReservationId = null,
    ): Room {
        $room = Room::query()->find($roomId);

        if (
            ! $room
            || $room->room_type !== $roomType
            || $room->status !== Room::STATUS_AVAILABLE
            || ! $this->availableRooms(
                $roomType,
                $checkIn,
                $checkOut,
                $exceptReservationId,
            )->contains('id', $roomId)
        ) {
            throw ValidationException::withMessages([
                'room_id' => __('site.owner.errors.room_unavailable'),
            ]);
        }

        return $room;
    }

    public function assertAvailable(
        string $roomType,
        string $checkIn,
        string $checkOut,
        ?int $exceptReservationId = null,
        string $errorKey = 'check_in',
    ): void {
        if (! $this->isAvailable($roomType, $checkIn, $checkOut, $exceptReservationId)) {
            throw ValidationException::withMessages([
                $errorKey => __('site.reservation.errors.unavailable'),
            ]);
        }
    }
}
