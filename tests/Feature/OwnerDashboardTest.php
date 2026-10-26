<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OwnerDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_and_customer_cannot_access_the_owner_dashboard(): void
    {
        $this->get(route('owner.dashboard'))
            ->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create())
            ->get(route('owner.dashboard'))
            ->assertForbidden();
    }

    public function test_owner_can_view_reservations_and_securely_download_receipts(): void
    {
        Storage::fake('payment_receipts');
        $owner = $this->createOwner();
        $customer = User::factory()->create(['name' => 'Client Test']);
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
            ->get(route('owner.dashboard'))
            ->assertOk()
            ->assertSee('Client Test')
            ->assertSee(__('site.owner.pending_title'))
            ->assertSee(__('site.owner.open_receipt'));

        $this->get(route('owner.receipts.download', $receipt))
            ->assertOk()
            ->assertHeader('Cache-Control', 'max-age=0, no-store, private');

        $reservation->update(['status' => 'confirmed']);
        $this->get(route('owner.dashboard'))
            ->assertOk()
            ->assertSee(__('site.owner.confirmed_title'));
    }

    public function test_owner_account_is_created_only_through_the_interactive_console_command(): void
    {
        $this->artisan('dar-anawat:make-owner')
            ->expectsQuestion('Owner email', 'owner@example.com')
            ->expectsQuestion('Owner name', 'Dar Anawat Owner')
            ->expectsQuestion('Password (minimum 12 characters)', 'correct-horse-battery')
            ->expectsQuestion('Confirm password', 'correct-horse-battery')
            ->assertExitCode(0);

        $owner = User::query()->where('email', 'owner@example.com')->firstOrFail();
        $this->assertTrue($owner->isOwner());
        $this->assertTrue(password_verify('correct-horse-battery', $owner->password));

        $this->post(route('login.store'), [
            'email' => 'owner@example.com',
            'password' => 'correct-horse-battery',
        ])->assertRedirect(route('owner.dashboard'));
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
