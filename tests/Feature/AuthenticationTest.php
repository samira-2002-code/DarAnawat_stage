<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'New Guest',
            'email' => 'new-guest@example.com',
            'password' => 'correct-horse-battery',
            'password_confirmation' => 'correct-horse-battery',
        ]);

        $user = User::query()->where('email', 'new-guest@example.com')->firstOrFail();

        $response->assertRedirect(route('payments.index'));
        $this->assertAuthenticatedAs($user);
        $this->assertSame('customer', $user->role);
    }

    public function test_customer_can_sign_in_and_sign_out(): void
    {
        $user = User::factory()->create([
            'email' => 'guest@example.com',
            'password' => 'correct-horse-battery',
        ]);

        $this->post(route('login.store'), [
            'email' => 'guest@example.com',
            'password' => 'correct-horse-battery',
        ])->assertRedirect(route('payments.index'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_registration_rejects_weak_or_mismatched_passwords(): void
    {
        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'New Guest',
                'email' => 'new-guest@example.com',
                'password' => 'short',
                'password_confirmation' => 'different',
            ])
            ->assertSessionHasErrors(['password']);

        $this->assertDatabaseCount('users', 0);
    }
}
