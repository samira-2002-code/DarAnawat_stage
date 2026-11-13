<?php

namespace App\Http\Controllers;

use App\Http\Requests\OwnerRoomRequest;
use App\Models\Reservation;
use App\Models\Room;
use App\Support\RoomAvailability;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OwnerRoomController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:40'],
            'room_type' => ['nullable', 'string', 'in:'.implode(',', array_keys(Reservation::ROOM_CAPACITIES))],
            'status' => ['nullable', 'string', 'in:'.implode(',', Room::STATUSES)],
        ]);

        $rooms = Room::query()
            ->withCount('reservations')
            ->when(
                filled($filters['search'] ?? null),
                fn ($query) => $query->where('room_number', 'like', '%'.$filters['search'].'%'),
            )
            ->when(
                filled($filters['room_type'] ?? null),
                fn ($query) => $query->where('room_type', $filters['room_type']),
            )
            ->when(
                filled($filters['status'] ?? null),
                fn ($query) => $query->where('status', $filters['status']),
            )
            ->orderBy('room_number')
            ->paginate(20)
            ->withQueryString();

        return view('owner.rooms.index', [
            'rooms' => $rooms,
            'filters' => $filters,
            'roomTypes' => Reservation::ROOM_CAPACITIES,
            'statuses' => Room::STATUSES,
        ]);
    }

    public function create(): View
    {
        return view('owner.rooms.create', [
            'room' => new Room([
                'price_per_night' => config('rooms.rates_mad.double'),
                'status' => Room::STATUS_AVAILABLE,
            ]),
            'roomTypes' => Reservation::ROOM_CAPACITIES,
            'statuses' => Room::STATUSES,
        ]);
    }

    public function store(OwnerRoomRequest $request, RoomAvailability $availability): RedirectResponse
    {
        DB::transaction(function () use ($request, $availability): void {
            $availability->lockAllCategories();
            Room::query()->create($request->validated());
        });

        return redirect()->route('owner.rooms.index')->with('status', __('site.owner.rooms.created'));
    }

    public function edit(Room $room): View
    {
        return view('owner.rooms.edit', [
            'room' => $room,
            'roomTypes' => Reservation::ROOM_CAPACITIES,
            'statuses' => Room::STATUSES,
        ]);
    }

    public function update(
        OwnerRoomRequest $request,
        Room $room,
        RoomAvailability $availability,
    ): RedirectResponse {
        DB::transaction(function () use ($request, $room, $availability): void {
            $availability->lockAllCategories();
            $room = Room::query()->lockForUpdate()->findOrFail($room->getKey());
            $data = $request->validated();

            if ($room->room_type !== $data['room_type'] && $room->reservations()->exists()) {
                throw ValidationException::withMessages([
                    'room_type' => __('site.owner.errors.room_category_locked'),
                ]);
            }

            if (
                $data['status'] !== Room::STATUS_AVAILABLE
                && $room->reservations()
                    ->where('status', Reservation::STATUS_CONFIRMED)
                    ->whereDate('check_out', '>', today())
                    ->exists()
            ) {
                throw ValidationException::withMessages([
                    'status' => __('site.owner.errors.room_has_future_reservations'),
                ]);
            }

            $room->update($data);
        });

        return redirect()->route('owner.rooms.index')->with('status', __('site.owner.rooms.updated'));
    }

    public function destroy(Room $room, RoomAvailability $availability): RedirectResponse
    {
        DB::transaction(function () use ($room, $availability): void {
            $availability->lockAllCategories();
            $room = Room::query()->lockForUpdate()->findOrFail($room->getKey());

            if ($room->reservations()->exists()) {
                throw ValidationException::withMessages([
                    'room' => __('site.owner.errors.room_has_reservations'),
                ]);
            }

            $room->delete();
        });

        return redirect()->route('owner.rooms.index')->with('status', __('site.owner.rooms.deleted'));
    }
}
