<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_reservation_form_is_available_and_room_details_link_to_it(): void
    {
        $this->get(route('reservations.create'))
            ->assertOk()
            ->assertSee('name="guest_name"', false)
            ->assertSee('name="check_in"', false)
            ->assertSee('name="check_out"', false)
            ->assertSee('name="guests"', false)
            ->assertSee(route('reservations.store'));

        $this->get(route('chambre.details', ['type' => 'double']))
            ->assertOk()
            ->assertSee(route('reservations.create', ['room' => 'double']))
            ->assertSee('mailto:www.dar.ennawat@gmail.com');
    }

    public function test_guest_can_submit_a_reservation_request_that_is_saved_as_pending(): void
    {
        $arrival = now()->addDays(10)->toDateString();
        $departure = now()->addDays(13)->toDateString();

        $response = $this->from(route('reservations.create'))
            ->post(route('reservations.store'), [
                'guest_name' => 'Samira Guest',
                'guest_email' => 'samira@example.com',
                'guest_phone' => '+212 600 000 000',
                'room_type' => 'double',
                'check_in' => $arrival,
                'check_out' => $departure,
                'guests' => 2,
                'special_requests' => 'Late arrival',
                'status' => 'confirmed',
            ]);

        $response->assertRedirect(route('reservations.create'))
            ->assertSessionHas('reservation_success');

        $this->assertDatabaseHas('reservations', [
            'user_id' => null,
            'guest_name' => 'Samira Guest',
            'guest_email' => 'samira@example.com',
            'room_type' => 'double',
            'check_in' => $arrival.' 00:00:00',
            'check_out' => $departure.' 00:00:00',
            'guests' => 2,
            'status' => 'pending',
        ]);
    }

    public function test_triple_room_reservation_accepts_three_guests(): void
    {
        $this->post(route('reservations.store'), [
            'guest_name' => 'Samira Guest',
            'guest_email' => 'samira@example.com',
            'room_type' => 'triple',
            'check_in' => now()->addDays(10)->toDateString(),
            'check_out' => now()->addDays(12)->toDateString(),
            'guests' => 3,
        ])->assertRedirect(route('reservations.create'))
            ->assertSessionHas('reservation_success');
    }

    public function test_authenticated_user_is_linked_to_their_reservation_request(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('reservations.store'), $this->validRequest());

        $this->assertDatabaseHas('reservations', [
            'user_id' => $user->id,
            'guest_email' => 'samira@example.com',
            'status' => 'pending',
        ]);
        $this->assertTrue($user->reservations()->exists());
    }

    public function test_invalid_dates_and_guest_capacity_are_rejected(): void
    {
        $arrival = now()->addDays(10)->toDateString();

        $this->from(route('reservations.create'))
            ->post(route('reservations.store'), [
                ...$this->validRequest(),
                'check_in' => $arrival,
                'check_out' => $arrival,
            ])
            ->assertSessionHasErrors('check_out');

        $this->from(route('reservations.create'))
            ->post(route('reservations.store'), [
                ...$this->validRequest(),
                'room_type' => 'single',
                'guests' => 2,
            ])
            ->assertSessionHasErrors('guests');

        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_past_arrival_dates_are_rejected(): void
    {
        $this->from(route('reservations.create'))
            ->post(route('reservations.store'), [
                ...$this->validRequest(),
                'check_in' => now()->subDay()->toDateString(),
                'check_out' => now()->addDay()->toDateString(),
            ])
            ->assertSessionHasErrors('check_in');

        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_malformed_room_type_input_is_rejected(): void
    {
        $this->from(route('reservations.create'))
            ->post(route('reservations.store'), [
                ...$this->validRequest(),
                'room_type' => ['suite'],
            ])
            ->assertSessionHasErrors('room_type');

        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_overlapping_requests_remain_pending_until_availability_is_checked(): void
    {
        $request = $this->validRequest();

        $this->post(route('reservations.store'), $request);
        $this->post(route('reservations.store'), $request);

        $this->assertSame(
            2,
            Reservation::query()->where('status', 'pending')->count(),
            'Requests are inquiries, not confirmed room allocations; availability must be checked before confirmation.',
        );
    }

    private function validRequest(): array
    {
        return [
            'guest_name' => 'Samira Guest',
            'guest_email' => 'samira@example.com',
            'room_type' => 'double',
            'check_in' => now()->addDays(10)->toDateString(),
            'check_out' => now()->addDays(12)->toDateString(),
            'guests' => 2,
        ];
    }
}
