<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhysicalRoomInventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_owner_can_manage_physical_rooms(): void
    {
        $this->get(route('owner.rooms.index'))->assertRedirect(route('login'));

        $customer = User::factory()->create();
        $this->actingAs($customer)
            ->get(route('owner.rooms.index'))
            ->assertForbidden();

        $this->post(route('owner.rooms.store'), $this->validRoom())
            ->assertForbidden();

        $reservation = $this->createReservation([], Reservation::STATUS_CONFIRMED);
        $this->post(route('owner.reservations.assign-room', $reservation), [
            'room_id' => 1,
        ])->assertForbidden();
    }

    public function test_owner_can_create_filter_edit_and_delete_unassigned_rooms(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner)
            ->get(route('owner.rooms.create'))
            ->assertOk()
            ->assertSee('name="room_number"', false);

        $this->post(route('owner.rooms.store'), $this->validRoom())
            ->assertRedirect(route('owner.rooms.index'))
            ->assertSessionHas('status', __('site.owner.rooms.created'));

        $room = Room::query()->firstOrFail();
        $this->assertSame('D-101', $room->room_number);
        $this->assertSame('double', $room->room_type);
        $this->assertSame(1250, $room->price_per_night);
        $this->assertSame(Room::STATUS_AVAILABLE, $room->status);

        $this->get(route('owner.rooms.index', ['search' => 'D-10', 'room_type' => 'double', 'status' => 'available']))
            ->assertOk()
            ->assertSee('D-101')
            ->assertSee(__('site.owner.rooms.statuses.available'));

        $this->put(route('owner.rooms.update', $room), [
            ...$this->validRoom(),
            'room_number' => 'D-102',
            'price_per_night' => 1450,
            'status' => Room::STATUS_MAINTENANCE,
        ])->assertRedirect(route('owner.rooms.index'));

        $room->refresh();
        $this->assertSame('D-102', $room->room_number);
        $this->assertSame(1450, $room->price_per_night);
        $this->assertSame(Room::STATUS_MAINTENANCE, $room->status);

        $this->delete(route('owner.rooms.destroy', $room))
            ->assertRedirect(route('owner.rooms.index'));
        $this->assertDatabaseMissing('rooms', ['id' => $room->getKey()]);
    }

    public function test_room_numbers_are_unique_and_categories_must_be_supported(): void
    {
        $owner = $this->createOwner();
        Room::query()->create($this->validRoom());

        $this->actingAs($owner)
            ->from(route('owner.rooms.create'))
            ->post(route('owner.rooms.store'), $this->validRoom([
                'room_type' => 'penthouse',
            ]))
            ->assertSessionHasErrors('room_type');

        $this->post(route('owner.rooms.store'), $this->validRoom())
            ->assertSessionHasErrors('room_number');

        $this->assertDatabaseCount('rooms', 1);
    }

    public function test_room_cannot_be_deleted_or_recategorized_after_reservation_linkage(): void
    {
        $owner = $this->createOwner();
        $room = Room::query()->create($this->validRoom());
        $reservation = $this->createReservation(['room_id' => $room->getKey()]);

        $this->actingAs($owner)
            ->delete(route('owner.rooms.destroy', $room))
            ->assertSessionHasErrors('room');

        $this->put(route('owner.rooms.update', $room), [
            ...$this->validRoom(),
            'room_type' => 'suite',
        ])->assertSessionHasErrors('room_type');

        $this->assertDatabaseHas('rooms', ['id' => $room->getKey(), 'room_type' => 'double']);
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->getKey(),
            'room_id' => $room->getKey(),
        ]);
    }

    public function test_room_cannot_be_made_unavailable_while_a_future_confirmed_stay_uses_it(): void
    {
        $owner = $this->createOwner();
        $room = Room::query()->create($this->validRoom());
        $this->createReservation([
            'room_id' => $room->getKey(),
            'check_in' => $this->date(10),
            'check_out' => $this->date(12),
        ], Reservation::STATUS_CONFIRMED);

        $this->actingAs($owner)
            ->put(route('owner.rooms.update', $room), [
                ...$this->validRoom(),
                'status' => Room::STATUS_MAINTENANCE,
            ])
            ->assertSessionHasErrors('status');

        $this->assertSame(Room::STATUS_AVAILABLE, $room->fresh()->status);
    }

    public function test_owner_can_confirm_overlapping_stays_in_two_different_physical_rooms_of_one_category(): void
    {
        $owner = $this->createOwner();
        $firstRoom = Room::query()->create($this->validRoom(['room_number' => 'D-101']));
        $secondRoom = Room::query()->create($this->validRoom(['room_number' => 'D-102']));
        $dates = ['check_in' => $this->date(10), 'check_out' => $this->date(12)];
        $first = $this->createReservation($dates, Reservation::STATUS_PENDING, Payment::STATUS_VERIFIED);
        $second = $this->createReservation($dates, Reservation::STATUS_PENDING, Payment::STATUS_VERIFIED);

        $this->actingAs($owner)
            ->from(route('owner.dashboard'))
            ->post(route('owner.reservations.confirm', $first), [
                'availability_confirmed' => '1',
                'room_id' => $firstRoom->getKey(),
            ])
            ->assertRedirect(route('owner.dashboard'))
            ->assertSessionHasNoErrors();

        $this->post(route('owner.reservations.confirm', $second), [
            'availability_confirmed' => '1',
            'room_id' => $secondRoom->getKey(),
        ])
            ->assertRedirect(route('owner.dashboard'))
            ->assertSessionHasNoErrors();

        $this->assertSame(Reservation::STATUS_CONFIRMED, $first->fresh()->status);
        $this->assertSame(Reservation::STATUS_CONFIRMED, $second->fresh()->status);
        $this->assertSame($firstRoom->getKey(), $first->fresh()->room_id);
        $this->assertSame($secondRoom->getKey(), $second->fresh()->room_id);
    }

    public function test_same_physical_room_cannot_be_confirmed_for_overlapping_stays(): void
    {
        $owner = $this->createOwner();
        $room = Room::query()->create($this->validRoom());
        $first = $this->createReservation([
            'check_in' => $this->date(10),
            'check_out' => $this->date(12),
            'room_id' => $room->getKey(),
        ], Reservation::STATUS_CONFIRMED);
        $second = $this->createReservation([
            'check_in' => $this->date(11),
            'check_out' => $this->date(13),
        ], Reservation::STATUS_PENDING, Payment::STATUS_VERIFIED);

        $this->actingAs($owner)
            ->from(route('owner.dashboard'))
            ->post(route('owner.reservations.confirm', $second), [
                'availability_confirmed' => '1',
                'room_id' => $room->getKey(),
            ])
            ->assertRedirect(route('owner.dashboard'))
            ->assertSessionHasErrors('room_id');

        $this->assertSame(Reservation::STATUS_CONFIRMED, $first->fresh()->status);
        $this->assertSame(Reservation::STATUS_PENDING, $second->fresh()->status);
        $this->assertNull($second->fresh()->room_id);
    }

    public function test_consecutive_stays_can_use_the_same_physical_room(): void
    {
        $owner = $this->createOwner();
        $room = Room::query()->create($this->validRoom());
        $first = $this->createReservation([
            'check_in' => $this->date(10),
            'check_out' => $this->date(12),
            'room_id' => $room->getKey(),
        ], Reservation::STATUS_CONFIRMED);
        $second = $this->createReservation([
            'check_in' => $this->date(12),
            'check_out' => $this->date(14),
        ], Reservation::STATUS_PENDING, Payment::STATUS_VERIFIED);

        $this->actingAs($owner)
            ->from(route('owner.dashboard'))
            ->post(route('owner.reservations.confirm', $second), [
                'availability_confirmed' => '1',
                'room_id' => $room->getKey(),
            ])
            ->assertRedirect(route('owner.dashboard'))
            ->assertSessionHasNoErrors();

        $this->assertSame(Reservation::STATUS_CONFIRMED, $first->fresh()->status);
        $this->assertSame(Reservation::STATUS_CONFIRMED, $second->fresh()->status);
        $this->assertSame($room->getKey(), $second->fresh()->room_id);
    }

    public function test_maintenance_and_inactive_rooms_cannot_be_assigned(): void
    {
        $owner = $this->createOwner();
        $maintenance = Room::query()->create($this->validRoom([
            'room_number' => 'D-M',
            'status' => Room::STATUS_MAINTENANCE,
        ]));
        $inactive = Room::query()->create($this->validRoom([
            'room_number' => 'D-I',
            'status' => Room::STATUS_INACTIVE,
        ]));
        $reservation = $this->createReservation([], Reservation::STATUS_PENDING, Payment::STATUS_VERIFIED);

        $this->actingAs($owner)
            ->from(route('owner.dashboard'))
            ->post(route('owner.reservations.confirm', $reservation), [
                'availability_confirmed' => '1',
                'room_id' => $maintenance->getKey(),
            ])
            ->assertSessionHasErrors('room_id');

        $this->post(route('owner.reservations.confirm', $reservation), [
            'availability_confirmed' => '1',
            'room_id' => $inactive->getKey(),
        ])->assertSessionHasErrors('room_id');

        $this->assertSame(Reservation::STATUS_PENDING, $reservation->fresh()->status);
    }

    public function test_physical_room_is_required_when_its_category_has_inventory(): void
    {
        $owner = $this->createOwner();
        Room::query()->create($this->validRoom());
        $reservation = $this->createReservation([], Reservation::STATUS_PENDING, Payment::STATUS_VERIFIED);

        $this->actingAs($owner)
            ->from(route('owner.dashboard'))
            ->post(route('owner.reservations.confirm', $reservation), [
                'availability_confirmed' => '1',
            ])
            ->assertSessionHasErrors('room_id');

        $this->assertSame(Reservation::STATUS_PENDING, $reservation->fresh()->status);
    }

    public function test_dashboard_offers_available_physical_rooms_for_verified_reservations(): void
    {
        $owner = $this->createOwner();
        Room::query()->create($this->validRoom());
        $reservation = $this->createReservation([], Reservation::STATUS_PENDING, Payment::STATUS_VERIFIED);

        $this->actingAs($owner)
            ->get(route('owner.dashboard'))
            ->assertOk()
            ->assertSee(route('owner.rooms.index'))
            ->assertSee('name="room_id"', false)
            ->assertSee('D-101')
            ->assertSee(route('owner.reservations.confirm', $reservation));
    }

    public function test_maintenance_or_inactive_category_has_no_public_availability(): void
    {
        Room::query()->create($this->validRoom(['status' => Room::STATUS_MAINTENANCE]));
        Room::query()->create($this->validRoom([
            'room_number' => 'D-102',
            'status' => Room::STATUS_INACTIVE,
        ]));

        $this->from(route('reservations.create'))
            ->post(route('reservations.store'), $this->validRequest())
            ->assertSessionHasErrors('check_in');

        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_legacy_confirmed_reservation_can_be_assigned_without_losing_its_data(): void
    {
        $owner = $this->createOwner();
        $dates = ['check_in' => $this->date(10), 'check_out' => $this->date(12)];
        $reservation = $this->createReservation($dates, Reservation::STATUS_CONFIRMED);
        $room = Room::query()->create($this->validRoom(['room_number' => 'D-101']));
        $secondRoom = Room::query()->create($this->validRoom(['room_number' => 'D-102']));

        $this->actingAs($owner)
            ->post(route('owner.reservations.assign-room', $reservation), [
                'room_id' => $room->getKey(),
            ])
            ->assertRedirect(route('owner.reservations.show', $reservation));

        $secondReservation = $this->createReservation($dates, Reservation::STATUS_CONFIRMED);
        $this->post(route('owner.reservations.assign-room', $secondReservation), [
            'room_id' => $secondRoom->getKey(),
        ])->assertRedirect(route('owner.reservations.show', $secondReservation));

        $this->assertSame($room->getKey(), $reservation->fresh()->room_id);
        $this->assertSame($secondRoom->getKey(), $secondReservation->fresh()->room_id);
        $this->assertSame(Reservation::STATUS_CONFIRMED, $reservation->fresh()->status);
    }

    public function test_owner_can_reassign_a_confirmed_reservation_to_an_available_room(): void
    {
        $owner = $this->createOwner();
        $currentRoom = Room::query()->create($this->validRoom(['room_number' => 'D-101']));
        $newRoom = Room::query()->create($this->validRoom(['room_number' => 'D-102']));
        $reservation = $this->createReservation([
            'room_id' => $currentRoom->getKey(),
        ], Reservation::STATUS_CONFIRMED);

        $this->actingAs($owner)
            ->get(route('owner.reservations.show', $reservation))
            ->assertOk()
            ->assertSee(__('site.owner.rooms.reassign_button'))
            ->assertSee('D-102');

        $this->post(route('owner.reservations.assign-room', $reservation), [
            'room_id' => $newRoom->getKey(),
        ])->assertRedirect(route('owner.reservations.show', $reservation));

        $this->assertSame($newRoom->getKey(), $reservation->fresh()->room_id);
    }

    public function test_reassignment_to_an_occupied_room_fails_and_keeps_the_old_room(): void
    {
        $owner = $this->createOwner();
        $currentRoom = Room::query()->create($this->validRoom(['room_number' => 'D-101']));
        $occupiedRoom = Room::query()->create($this->validRoom(['room_number' => 'D-102']));
        $dates = ['check_in' => $this->date(10), 'check_out' => $this->date(12)];
        $reservation = $this->createReservation([
            ...$dates,
            'room_id' => $currentRoom->getKey(),
        ], Reservation::STATUS_CONFIRMED);
        $this->createReservation([
            ...$dates,
            'room_id' => $occupiedRoom->getKey(),
        ], Reservation::STATUS_CONFIRMED);

        $this->actingAs($owner)
            ->from(route('owner.reservations.show', $reservation))
            ->post(route('owner.reservations.assign-room', $reservation), [
                'room_id' => $occupiedRoom->getKey(),
            ])
            ->assertSessionHasErrors('room_id');

        $this->assertSame($currentRoom->getKey(), $reservation->fresh()->room_id);
    }

    public function test_reassignment_to_a_different_category_fails_and_keeps_the_old_room(): void
    {
        $owner = $this->createOwner();
        $currentRoom = Room::query()->create($this->validRoom(['room_number' => 'D-101']));
        $wrongCategoryRoom = Room::query()->create($this->validRoom([
            'room_number' => 'S-201',
            'room_type' => 'suite',
        ]));
        $reservation = $this->createReservation([
            'room_id' => $currentRoom->getKey(),
        ], Reservation::STATUS_CONFIRMED);

        $this->actingAs($owner)
            ->from(route('owner.reservations.show', $reservation))
            ->post(route('owner.reservations.assign-room', $reservation), [
                'room_id' => $wrongCategoryRoom->getKey(),
            ])
            ->assertSessionHasErrors('room_id');

        $this->assertSame($currentRoom->getKey(), $reservation->fresh()->room_id);
    }

    public function test_reassignment_to_maintenance_or_inactive_room_fails_and_keeps_old_room(): void
    {
        $owner = $this->createOwner();
        $currentRoom = Room::query()->create($this->validRoom(['room_number' => 'D-101']));
        $maintenanceRoom = Room::query()->create($this->validRoom([
            'room_number' => 'D-M',
            'status' => Room::STATUS_MAINTENANCE,
        ]));
        $inactiveRoom = Room::query()->create($this->validRoom([
            'room_number' => 'D-I',
            'status' => Room::STATUS_INACTIVE,
        ]));
        $reservation = $this->createReservation([
            'room_id' => $currentRoom->getKey(),
        ], Reservation::STATUS_CONFIRMED);

        $this->actingAs($owner)
            ->from(route('owner.reservations.show', $reservation))
            ->post(route('owner.reservations.assign-room', $reservation), [
                'room_id' => $maintenanceRoom->getKey(),
            ])
            ->assertSessionHasErrors('room_id');

        $this->post(route('owner.reservations.assign-room', $reservation), [
            'room_id' => $inactiveRoom->getKey(),
        ])->assertSessionHasErrors('room_id');

        $this->assertSame($currentRoom->getKey(), $reservation->fresh()->room_id);
    }

    public function test_unassigned_legacy_reservation_blocks_reassignment_of_another_stay(): void
    {
        $owner = $this->createOwner();
        $currentRoom = Room::query()->create($this->validRoom(['room_number' => 'D-101']));
        $newRoom = Room::query()->create($this->validRoom(['room_number' => 'D-102']));
        $dates = ['check_in' => $this->date(10), 'check_out' => $this->date(12)];
        $reservation = $this->createReservation([
            ...$dates,
            'room_id' => $currentRoom->getKey(),
        ], Reservation::STATUS_CONFIRMED);
        $this->createReservation($dates, Reservation::STATUS_CONFIRMED);

        $this->actingAs($owner)
            ->from(route('owner.reservations.show', $reservation))
            ->post(route('owner.reservations.assign-room', $reservation), [
                'room_id' => $newRoom->getKey(),
            ])
            ->assertSessionHasErrors('room_id');

        $this->assertSame($currentRoom->getKey(), $reservation->fresh()->room_id);
    }

    private function createReservation(
        array $overrides = [],
        string $status = Reservation::STATUS_PENDING,
        string $paymentStatus = Payment::STATUS_AWAITING_TRANSFER,
    ): Reservation {
        $customer = User::factory()->create();
        $reservation = Reservation::query()->create([
            'user_id' => $customer->getKey(),
            'guest_name' => 'Test Guest',
            'guest_email' => 'guest@example.com',
            'room_type' => 'double',
            'check_in' => $this->date(20),
            'check_out' => $this->date(22),
            'guests' => 2,
            'status' => $status,
            ...$overrides,
        ]);
        $reservation->payment()->create([
            'amount_mad' => 2180,
            'status' => $paymentStatus,
        ]);

        return $reservation;
    }

    private function createOwner(): User
    {
        $owner = User::factory()->create();
        $owner->role = 'owner';
        $owner->save();

        return $owner;
    }

    private function validRoom(array $overrides = []): array
    {
        return [
            'room_number' => 'D-101',
            'room_type' => 'double',
            'price_per_night' => 1250,
            'status' => Room::STATUS_AVAILABLE,
            ...$overrides,
        ];
    }

    private function validRequest(array $overrides = []): array
    {
        return [
            'guest_name' => 'Request Guest',
            'guest_email' => 'request@example.com',
            'room_type' => 'double',
            'check_in' => $this->date(10),
            'check_out' => $this->date(12),
            'guests' => 2,
            ...$overrides,
        ];
    }

    private function date(int $daysFromNow): string
    {
        return now()->addDays($daysFromNow)->toDateString();
    }
}
