<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\PaymentDecision;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_request_rejects_an_overlap_with_a_confirmed_reservation_and_keeps_input(): void
    {
        $this->createReservation([
            'check_in' => $this->date(10),
            'check_out' => $this->date(12),
        ], Reservation::STATUS_CONFIRMED);
        $request = $this->validRequest([
            'guest_name' => 'Guest With Preserved Input',
            'check_in' => $this->date(11),
            'check_out' => $this->date(13),
        ]);

        $this->from(route('reservations.create'))
            ->post(route('reservations.store'), $request)
            ->assertRedirect(route('reservations.create'))
            ->assertSessionHasErrors('check_in');

        $this->get(route('reservations.create'))
            ->assertOk()
            ->assertSee('value="Guest With Preserved Input"', false)
            ->assertSee('value="'.$request['check_in'].'"', false)
            ->assertSee('value="'.$request['check_out'].'"', false);

        $this->assertDatabaseCount('reservations', 1);
    }

    public function test_consecutive_reservations_in_the_same_category_are_allowed(): void
    {
        $this->createReservation([
            'check_in' => $this->date(10),
            'check_out' => $this->date(12),
        ], Reservation::STATUS_CONFIRMED);

        $this->from(route('reservations.create'))
            ->post(route('reservations.store'), $this->validRequest([
                'check_in' => $this->date(12),
                'check_out' => $this->date(14),
            ]))
            ->assertRedirect(route('reservations.create'))
            ->assertSessionHas('reservation_success');

        $this->assertDatabaseCount('reservations', 2);
    }

    public function test_overlapping_reservations_in_different_categories_are_allowed(): void
    {
        $this->createReservation([
            'room_type' => 'double',
            'check_in' => $this->date(10),
            'check_out' => $this->date(12),
        ], Reservation::STATUS_CONFIRMED);

        $this->post(route('reservations.store'), $this->validRequest([
            'room_type' => 'suite',
            'check_in' => $this->date(10),
            'check_out' => $this->date(12),
            'guests' => 4,
        ]))
            ->assertRedirect(route('reservations.create'))
            ->assertSessionHas('reservation_success');

        $this->assertDatabaseCount('reservations', 2);
    }

    public function test_cancelled_reservations_do_not_block_availability(): void
    {
        $this->createReservation([
            'check_in' => $this->date(10),
            'check_out' => $this->date(12),
        ], Reservation::STATUS_CANCELLED);

        $this->post(route('reservations.store'), $this->validRequest([
            'check_in' => $this->date(11),
            'check_out' => $this->date(13),
        ]))
            ->assertRedirect(route('reservations.create'))
            ->assertSessionHas('reservation_success');

        $this->assertDatabaseCount('reservations', 2);
    }

    public function test_owner_cannot_create_a_reservation_for_unavailable_dates(): void
    {
        $owner = $this->createOwner();
        $this->createReservation([
            'check_in' => $this->date(10),
            'check_out' => $this->date(12),
        ], Reservation::STATUS_CONFIRMED);

        $this->actingAs($owner)
            ->from(route('owner.reservations.create'))
            ->post(route('owner.reservations.store'), $this->validRequest([
                'check_in' => $this->date(11),
                'check_out' => $this->date(13),
            ]))
            ->assertRedirect(route('owner.reservations.create'))
            ->assertSessionHasErrors('check_in');

        $this->assertDatabaseCount('reservations', 1);
    }

    public function test_owner_cannot_modify_dates_to_overlap_and_form_values_are_preserved(): void
    {
        $owner = $this->createOwner();
        $reservation = $this->createReservation([
            'check_in' => $this->date(10),
            'check_out' => $this->date(12),
        ]);
        $this->createReservation([
            'check_in' => $this->date(14),
            'check_out' => $this->date(16),
        ], Reservation::STATUS_CONFIRMED);
        $request = $this->validRequest([
            'check_in' => $this->date(15),
            'check_out' => $this->date(17),
        ]);

        $this->actingAs($owner)
            ->from(route('owner.reservations.edit', $reservation))
            ->put(route('owner.reservations.update', $reservation), $request)
            ->assertRedirect(route('owner.reservations.edit', $reservation))
            ->assertSessionHasErrors('check_in');

        $this->get(route('owner.reservations.edit', $reservation))
            ->assertOk()
            ->assertSee('value="'.$request['check_in'].'"', false)
            ->assertSee('value="'.$request['check_out'].'"', false);

        $this->assertSame($this->date(10), $reservation->fresh()->check_in->toDateString());
        $this->assertSame($this->date(12), $reservation->fresh()->check_out->toDateString());
    }

    public function test_owner_cannot_change_room_category_to_one_that_is_unavailable(): void
    {
        $owner = $this->createOwner();
        $reservation = $this->createReservation([
            'room_type' => 'suite',
            'check_in' => $this->date(10),
            'check_out' => $this->date(12),
        ]);
        $this->createReservation([
            'room_type' => 'double',
            'check_in' => $this->date(10),
            'check_out' => $this->date(12),
        ], Reservation::STATUS_CONFIRMED);

        $this->actingAs($owner)
            ->from(route('owner.reservations.edit', $reservation))
            ->put(route('owner.reservations.update', $reservation), $this->validRequest([
                'room_type' => 'double',
                'check_in' => $this->date(10),
                'check_out' => $this->date(12),
                'guests' => 2,
            ]))
            ->assertRedirect(route('owner.reservations.edit', $reservation))
            ->assertSessionHasErrors('check_in');

        $this->assertSame('suite', $reservation->fresh()->room_type);
    }

    public function test_owner_cannot_confirm_an_overlapping_reservation(): void
    {
        $owner = $this->createOwner();
        $this->createReservation([
            'check_in' => $this->date(10),
            'check_out' => $this->date(12),
        ], Reservation::STATUS_CONFIRMED);
        $reservation = $this->createReservation([
            'check_in' => $this->date(11),
            'check_out' => $this->date(13),
        ], Reservation::STATUS_PENDING, Payment::STATUS_VERIFIED);

        $this->actingAs($owner)
            ->from(route('owner.dashboard'))
            ->post(route('owner.reservations.confirm', $reservation), [
                'availability_confirmed' => '1',
            ])
            ->assertRedirect(route('owner.dashboard'))
            ->assertSessionHasErrors('availability');

        $this->assertSame(Reservation::STATUS_PENDING, $reservation->fresh()->status);
        $this->assertDatabaseMissing('payment_decisions', [
            'payment_id' => $reservation->payment->getKey(),
            'action' => PaymentDecision::ACTION_RESERVATION_CONFIRMED,
        ]);
    }

    public function test_competing_confirmations_allow_only_one_overlapping_reservation(): void
    {
        $owner = $this->createOwner();
        $dates = [
            'check_in' => $this->date(10),
            'check_out' => $this->date(12),
        ];
        $first = $this->createReservation($dates, Reservation::STATUS_PENDING, Payment::STATUS_VERIFIED);
        $second = $this->createReservation($dates, Reservation::STATUS_PENDING, Payment::STATUS_VERIFIED);

        $this->actingAs($owner)
            ->from(route('owner.dashboard'))
            ->post(route('owner.reservations.confirm', $first), [
                'availability_confirmed' => '1',
            ])
            ->assertRedirect(route('owner.dashboard'))
            ->assertSessionHasNoErrors();

        $this->from(route('owner.dashboard'))
            ->post(route('owner.reservations.confirm', $second), [
                'availability_confirmed' => '1',
            ])
            ->assertRedirect(route('owner.dashboard'))
            ->assertSessionHasErrors('availability');

        $this->assertSame(1, Reservation::query()
            ->where('room_type', 'double')
            ->where('status', Reservation::STATUS_CONFIRMED)
            ->count());
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

    private function validRequest(array $overrides = []): array
    {
        return [
            'guest_name' => 'Request Guest',
            'guest_email' => 'request@example.com',
            'room_type' => 'double',
            'check_in' => $this->date(20),
            'check_out' => $this->date(22),
            'guests' => 2,
            ...$overrides,
        ];
    }

    private function date(int $daysFromNow): string
    {
        return now()->addDays($daysFromNow)->toDateString();
    }
}
