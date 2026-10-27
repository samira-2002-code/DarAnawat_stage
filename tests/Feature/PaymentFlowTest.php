<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\PaymentReceipt;
use App\Models\Reservation;
use App\Models\User;
use App\Support\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_customer_can_view_their_amount_and_bank_instructions(): void
    {
        $user = User::factory()->create();
        $reservation = $this->createReservation($user);

        $this->actingAs($user)
            ->get(route('payments.show', $reservation))
            ->assertOk()
            ->assertSee(Currency::formatMad(2180))
            ->assertSee(config('payment.bank_name'))
            ->assertSee(config('payment.rib'))
            ->assertSee(__('site.payment.copy_rib'));
    }

    public function test_customer_can_report_transfer_without_confirming_payment_or_reservation(): void
    {
        $user = User::factory()->create();
        $reservation = $this->createReservation($user);

        $this->actingAs($user)
            ->post(route('payments.report', $reservation))
            ->assertRedirect(route('payments.show', $reservation));

        $this->assertDatabaseHas('payments', [
            'reservation_id' => $reservation->getKey(),
            'status' => Payment::STATUS_REPORTED,
        ]);
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->getKey(),
            'status' => 'pending',
        ]);
    }

    public function test_customer_can_upload_a_private_receipt(): void
    {
        Storage::fake('payment_receipts');
        $user = User::factory()->create();
        $reservation = $this->createReservation($user);

        $this->actingAs($user)
            ->post(route('payments.report', $reservation), [
                'receipt' => UploadedFile::fake()->createWithContent(
                    'transfer.png',
                    base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/lqkAAAAASUVORK5CYII='),
                ),
            ])
            ->assertRedirect(route('payments.show', $reservation));

        $receipt = PaymentReceipt::query()->firstOrFail();
        Storage::disk('payment_receipts')->assertExists($receipt->path);
        $this->assertSame('reported', $reservation->fresh()->payment->status);
    }

    public function test_invalid_receipt_type_is_rejected(): void
    {
        Storage::fake('payment_receipts');
        $user = User::factory()->create();
        $reservation = $this->createReservation($user);

        $this->actingAs($user)
            ->from(route('payments.show', $reservation))
            ->post(route('payments.report', $reservation), [
                'receipt' => UploadedFile::fake()->create('transfer.txt', 10, 'text/plain'),
            ])
            ->assertSessionHasErrors('receipt');

        $this->assertDatabaseCount('payment_receipts', 0);
        $this->assertSame(Payment::STATUS_AWAITING_TRANSFER, $reservation->fresh()->payment->status);
    }

    public function test_receipt_larger_than_five_megabytes_is_rejected(): void
    {
        Storage::fake('payment_receipts');
        $user = User::factory()->create();
        $reservation = $this->createReservation($user);

        $this->actingAs($user)
            ->from(route('payments.show', $reservation))
            ->post(route('payments.report', $reservation), [
                'receipt' => UploadedFile::fake()->create('transfer.pdf', 5121, 'application/pdf'),
            ])
            ->assertSessionHasErrors('receipt');

        $this->assertDatabaseCount('payment_receipts', 0);
        $this->assertSame(Payment::STATUS_AWAITING_TRANSFER, $reservation->fresh()->payment->status);
    }

    public function test_customer_cannot_view_another_customers_payment_or_receipt(): void
    {
        Storage::fake('payment_receipts');
        $owner = User::factory()->create();
        $reservation = $this->createReservation($owner);
        $receiptPath = UploadedFile::fake()->createWithContent(
            'receipt.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/lqkAAAAASUVORK5CYII='),
        )->store('receipts', 'payment_receipts');
        $receipt = $reservation->payment->receipts()->create([
            'uploaded_by' => $owner->getKey(),
            'path' => $receiptPath,
            'mime_type' => 'image/png',
            'size_bytes' => 100,
        ]);

        $this->actingAs(User::factory()->create());

        $this->get(route('payments.show', $reservation))
            ->assertNotFound();

        $this->get(route('payments.receipts.download', $receipt))
            ->assertNotFound();
    }

    private function createReservation(User $user): Reservation
    {
        $response = $this->actingAs($user)->post(route('reservations.store'), [
            'guest_name' => 'Test Guest',
            'guest_email' => 'guest@example.com',
            'room_type' => 'double',
            'check_in' => now()->addDays(10)->toDateString(),
            'check_out' => now()->addDays(12)->toDateString(),
            'guests' => 2,
        ]);

        $reservation = Reservation::query()->latest('id')->firstOrFail();
        $response->assertRedirect(route('payments.show', $reservation));

        return $reservation;
    }
}
