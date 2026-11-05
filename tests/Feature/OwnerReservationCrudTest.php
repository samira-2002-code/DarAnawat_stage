<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\PaymentDecision;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerReservationCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_an_owner_can_access_reservation_management(): void
    {
        $reservation = $this->createReservation();

        $this->get(route('owner.reservations.index'))
            ->assertRedirect(route('login'));

        $customer = User::factory()->create();
        $this->actingAs($customer)
            ->get(route('owner.reservations.show', $reservation))
            ->assertForbidden();

        $this->post(route('owner.reservations.store'), $this->validReservation())
            ->assertForbidden();
    }

    public function test_owner_can_create_view_and_update_a_reservation_and_its_payment_amount(): void
    {
        $owner = $this->createOwner();
        $customer = User::factory()->create();
        $arrival = now()->addDays(10)->toDateString();
        $departure = now()->addDays(12)->toDateString();

        $this->actingAs($owner)
            ->get(route('owner.reservations.create'))
            ->assertOk()
            ->assertSee('name="guest_name"', false);

        $this->post(route('owner.reservations.store'), [
            ...$this->validReservation(),
            'user_id' => $customer->getKey(),
            'status' => Reservation::STATUS_CONFIRMED,
            'amount_mad' => 1,
        ])->assertRedirect();

        $reservation = Reservation::query()->latest('id')->firstOrFail();
        $payment = $reservation->payment;

        $this->assertSame($customer->getKey(), $reservation->user_id);
        $this->assertSame(Reservation::STATUS_PENDING, $reservation->status);
        $this->assertSame(config('rooms.rates_mad.double') * 2, $payment->amount_mad);
        $this->assertSame(Payment::STATUS_AWAITING_TRANSFER, $payment->status);

        $this->get(route('owner.reservations.show', $reservation))
            ->assertOk()
            ->assertSee($reservation->guest_email)
            ->assertSee(__('site.owner.reservations.delete_button'));
        $this->get(route('owner.reservations.edit', $reservation))->assertOk();

        $this->put(route('owner.reservations.update', $reservation), [
            ...$this->validReservation(),
            'room_type' => 'suite',
            'guests' => 4,
            'check_in' => $arrival,
            'check_out' => $departure,
            'guest_name' => 'Updated Guest',
            'status' => Reservation::STATUS_CONFIRMED,
        ])->assertRedirect(route('owner.reservations.show', $reservation));

        $reservation->refresh();
        $this->assertSame('Updated Guest', $reservation->guest_name);
        $this->assertSame(Reservation::STATUS_PENDING, $reservation->status);
        $this->assertSame(config('rooms.rates_mad.suite') * 2, $reservation->payment->amount_mad);
    }

    public function test_owner_reservation_input_is_validated_like_public_booking(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner)
            ->from(route('owner.reservations.create'))
            ->post(route('owner.reservations.store'), [
                ...$this->validReservation(),
                'room_type' => 'not-a-room',
                'guests' => 3,
            ])
            ->assertSessionHasErrors('room_type');

        $this->from(route('owner.reservations.create'))
            ->post(route('owner.reservations.store'), [
                ...$this->validReservation(),
                'room_type' => 'single',
                'guests' => 2,
            ])
            ->assertSessionHasErrors('guests');

        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_owner_can_cancel_an_unpaid_reservation_with_audited_reason(): void
    {
        $owner = $this->createOwner();
        $reservation = $this->createReservation();

        $this->actingAs($owner)
            ->patch(route('owner.reservations.cancel', $reservation), [
                'reason' => 'Guest requested cancellation',
            ])
            ->assertRedirect(route('owner.reservations.show', $reservation));

        $this->assertSame(Reservation::STATUS_CANCELLED, $reservation->fresh()->status);
        $this->assertDatabaseHas('payment_decisions', [
            'payment_id' => $reservation->payment->getKey(),
            'administrator_id' => $owner->getKey(),
            'action' => PaymentDecision::ACTION_RESERVATION_CANCELLED,
            'reason' => 'Guest requested cancellation',
        ]);
    }

    public function test_cancelled_reservation_cannot_receive_a_transfer_report_or_be_verified(): void
    {
        $owner = $this->createOwner();
        $customer = User::factory()->create();
        $reservation = $this->createReservation();
        $reservation->update(['user_id' => $customer->getKey()]);

        $this->actingAs($owner)
            ->patch(route('owner.reservations.cancel', $reservation), [
                'reason' => 'Guest requested cancellation',
            ])
            ->assertRedirect(route('owner.reservations.show', $reservation));

        $this->actingAs($customer)
            ->post(route('payments.report', $reservation))
            ->assertStatus(409);

        $this->get(route('payments.show', $reservation))
            ->assertOk()
            ->assertSee(__('site.payment.cancelled_message'))
            ->assertDontSee(config('payment.rib'))
            ->assertDontSee('name="receipt"', false);

        $this->actingAs($owner)
            ->post(route('owner.payments.verify', $reservation->payment), [
                'funds_received' => '1',
            ])
            ->assertStatus(409);

        $this->assertSame(Payment::STATUS_AWAITING_TRANSFER, $reservation->fresh()->payment->status);
    }

    public function test_owner_cannot_cancel_or_delete_after_transfer_has_been_reported(): void
    {
        $owner = $this->createOwner();
        $reservation = $this->createReservation();
        $reservation->payment->update(['status' => Payment::STATUS_REPORTED]);

        $this->actingAs($owner)
            ->patch(route('owner.reservations.cancel', $reservation), [
                'reason' => 'Guest requested cancellation',
            ])
            ->assertStatus(409);

        $this->delete(route('owner.reservations.destroy', $reservation))
            ->assertStatus(409);

        $this->assertDatabaseHas('reservations', ['id' => $reservation->getKey()]);
    }

    public function test_owner_can_delete_only_an_unprocessed_pending_reservation(): void
    {
        $owner = $this->createOwner();
        $reservation = $this->createReservation();

        $this->actingAs($owner)
            ->delete(route('owner.reservations.destroy', $reservation))
            ->assertRedirect(route('owner.reservations.index'));

        $this->assertDatabaseMissing('reservations', ['id' => $reservation->getKey()]);
        $this->assertDatabaseMissing('payments', ['reservation_id' => $reservation->getKey()]);
    }

    private function createOwner(): User
    {
        $owner = User::factory()->create();
        $owner->role = 'owner';
        $owner->save();

        return $owner;
    }

    private function createReservation(): Reservation
    {
        $reservation = Reservation::create([
            'guest_name' => 'Test Guest',
            'guest_email' => 'guest@example.com',
            'room_type' => 'double',
            'check_in' => now()->addDays(10)->toDateString(),
            'check_out' => now()->addDays(12)->toDateString(),
            'guests' => 2,
            'status' => Reservation::STATUS_PENDING,
        ]);

        $reservation->payment()->create([
            'amount_mad' => config('rooms.rates_mad.double') * 2,
            'status' => Payment::STATUS_AWAITING_TRANSFER,
        ]);

        return $reservation;
    }

    private function validReservation(): array
    {
        return [
            'guest_name' => 'New Guest',
            'guest_email' => 'new-guest@example.com',
            'room_type' => 'double',
            'check_in' => now()->addDays(10)->toDateString(),
            'check_out' => now()->addDays(12)->toDateString(),
            'guests' => 2,
        ];
    }
}
