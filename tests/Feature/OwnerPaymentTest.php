<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\PaymentDecision;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OwnerPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_protected_payment_and_owner_routes(): void
    {
        $this->get(route('owner.dashboard'))
            ->assertRedirect(route('login'));

        $this->get(route('payments.index'))
            ->assertRedirect(route('login'));
    }

    public function test_customer_cannot_access_owner_dashboard_or_verify_their_payment(): void
    {
        $customer = User::factory()->create(['name' => 'Client Test']);
        $reservation = $this->createReservation($customer);

        $this->actingAs($customer)
            ->get(route('owner.dashboard'))
            ->assertForbidden();

        $this->post(route('owner.payments.verify', $reservation->payment), [
            'funds_received' => '1',
        ])->assertForbidden();

        $this->assertSame(Payment::STATUS_AWAITING_TRANSFER, $reservation->fresh()->payment->status);
        $this->assertSame('pending', $reservation->fresh()->status);
    }

    public function test_owner_can_verify_transfer_and_separately_confirm_reservation(): void
    {
        $owner = $this->createOwner();
        $customer = User::factory()->create(['name' => 'Client Test']);
        $reservation = $this->createReservation($customer);

        $this->actingAs($owner)
            ->get(route('owner.dashboard'))
            ->assertOk()
            ->assertSee('Client Test')
            ->assertSee(__('site.owner.funds_received'));

        $this->post(route('owner.payments.verify', $reservation->payment), [
            'funds_received' => '1',
        ])->assertRedirect(route('owner.dashboard'));

        $this->assertSame(Payment::STATUS_VERIFIED, $reservation->fresh()->payment->status);
        $this->assertSame('pending', $reservation->fresh()->status);
        $this->assertDatabaseHas('payment_decisions', [
            'payment_id' => $reservation->payment->getKey(),
            'administrator_id' => $owner->getKey(),
            'action' => PaymentDecision::ACTION_VERIFIED,
        ]);

        $this->post(route('owner.reservations.confirm', $reservation), [
            'availability_confirmed' => '1',
            'reason' => 'Availability checked',
        ])->assertRedirect(route('owner.dashboard'));

        $this->assertSame('confirmed', $reservation->fresh()->status);
        $this->assertDatabaseHas('payment_decisions', [
            'payment_id' => $reservation->payment->getKey(),
            'administrator_id' => $owner->getKey(),
            'action' => PaymentDecision::ACTION_RESERVATION_CONFIRMED,
            'reason' => 'Availability checked',
        ]);
    }

    public function test_owner_cannot_verify_without_acknowledging_real_funds_or_verify_twice(): void
    {
        $owner = $this->createOwner();
        $reservation = $this->createReservation(User::factory()->create());

        $this->actingAs($owner)
            ->post(route('owner.payments.verify', $reservation->payment), [])
            ->assertSessionHasErrors('funds_received');

        $this->assertSame(Payment::STATUS_AWAITING_TRANSFER, $reservation->fresh()->payment->status);

        $this->post(route('owner.payments.verify', $reservation->payment), [
            'funds_received' => '1',
        ])->assertRedirect(route('owner.dashboard'));

        $this->post(route('owner.payments.verify', $reservation->payment), [
            'funds_received' => '1',
        ])->assertStatus(409);

        $this->assertDatabaseCount('payment_decisions', 1);
    }

    public function test_owner_can_reject_receipt_or_mark_transfer_not_received_with_reason(): void
    {
        Storage::fake('payment_receipts');
        $owner = $this->createOwner();
        $customer = User::factory()->create();
        $reservation = $this->createReservation($customer);
        $receiptPath = UploadedFile::fake()->createWithContent(
            'receipt.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/lqkAAAAASUVORK5CYII='),
        )->store('receipts', 'payment_receipts');
        $receipt = $reservation->payment->receipts()->create([
            'uploaded_by' => $customer->getKey(),
            'path' => $receiptPath,
            'mime_type' => 'image/png',
            'size_bytes' => 100,
        ]);

        $this->actingAs($owner)
            ->post(route('owner.receipts.reject', $receipt), ['reason' => 'Name does not match'])
            ->assertRedirect(route('owner.dashboard'));

        $this->assertSame(Payment::STATUS_REJECTED, $reservation->fresh()->payment->status);
        $this->assertDatabaseHas('payment_receipts', [
            'id' => $receipt->getKey(),
            'status' => 'rejected',
            'review_reason' => 'Name does not match',
            'reviewed_by' => $owner->getKey(),
        ]);

        $secondReservation = $this->createReservation(User::factory()->create());
        $this->actingAs($owner);
        $this->post(route('owner.payments.not-received', $secondReservation->payment), [
            'reason' => 'No matching credit in bank statement',
        ])->assertRedirect(route('owner.dashboard'));

        $this->assertSame(Payment::STATUS_NOT_RECEIVED, $secondReservation->fresh()->payment->status);
        $this->assertDatabaseHas('payment_decisions', [
            'payment_id' => $secondReservation->payment->getKey(),
            'action' => PaymentDecision::ACTION_NOT_RECEIVED,
            'reason' => 'No matching credit in bank statement',
        ]);
    }

    public function test_reservation_cannot_be_confirmed_before_verified_payment_or_by_customer(): void
    {
        $owner = $this->createOwner();
        $customer = User::factory()->create();
        $reservation = $this->createReservation($customer);

        $this->actingAs($owner)
            ->post(route('owner.reservations.confirm', $reservation), [
                'availability_confirmed' => '1',
            ])
            ->assertStatus(409);

        $this->actingAs($customer)
            ->post(route('owner.reservations.confirm', $reservation), [
                'availability_confirmed' => '1',
            ])
            ->assertForbidden();

        $this->assertSame('pending', $reservation->fresh()->status);
    }

    public function test_customer_cannot_view_another_customers_receipt_but_owner_can(): void
    {
        Storage::fake('payment_receipts');
        $customer = User::factory()->create();
        $reservation = $this->createReservation($customer);
        $receiptPath = UploadedFile::fake()->createWithContent(
            'receipt.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/lqkAAAAASUVORK5CYII='),
        )->store('receipts', 'payment_receipts');
        $receipt = $reservation->payment->receipts()->create([
            'uploaded_by' => $customer->getKey(),
            'path' => $receiptPath,
            'mime_type' => 'image/png',
            'size_bytes' => 100,
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('payments.receipts.download', $receipt))
            ->assertNotFound();

        $this->actingAs($this->createOwner())
            ->get(route('owner.receipts.download', $receipt))
            ->assertOk();
    }

    private function createOwner(): User
    {
        $owner = User::factory()->create();
        $owner->role = 'owner';
        $owner->save();

        return $owner;
    }

    private function createReservation(User $user): Reservation
    {
        $this->actingAs($user)->post(route('reservations.store'), [
            'guest_name' => 'Test Guest',
            'guest_email' => 'guest@example.com',
            'room_type' => 'double',
            'check_in' => now()->addDays(10)->toDateString(),
            'check_out' => now()->addDays(12)->toDateString(),
            'guests' => 2,
        ]);

        return Reservation::query()->latest('id')->firstOrFail();
    }
}
