<?php

namespace Tests\Feature;

use App\Models\Operation;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthBalanceOperationsTest extends TestCase
{
    public function test_login_success_and_user_endpoint(): void
    {
        $password = 'secret-pass';
        $user = User::factory()->create([
            'password' => $password,
        ]);

        $this->postJson('/login', [
            'email' => $user->email,
            'password' => $password,
        ])->assertOk();

        $this->assertAuthenticatedAs($user);

        $this->getJson('/user')
            ->assertOk()
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('email', $user->email);
    }

    public function test_login_failure_and_protected_user_endpoint_requires_auth(): void
    {
        $user = User::factory()->create();

        $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'wrong',
        ])->assertStatus(422);

        $this->assertGuest();

        $this->getJson('/user')->assertStatus(401);
    }

    public function test_logout_clears_authentication(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/logout')
            ->assertNoContent();

        $this->assertGuest();
    }

    public function test_balance_returns_sum_of_operations(): void
    {
        $user = User::factory()->create();

        Operation::factory()->for($user)->create(['amount' => 150.00]);
        Operation::factory()->for($user)->create(['amount' => -45.50]);

        $this->actingAs($user)
            ->getJson('/api/balance')
            ->assertOk()
            ->assertJson(['balance' => '104.50']);
    }

    public function test_operations_listing_supports_search_sort_and_pagination(): void
    {
        $user = User::factory()->create();

        $ops = [
            ['description' => 'Coffee beans', 'amount' => -12.50, 'created_at' => now()->subDays(3)],
            ['description' => 'Salary payout', 'amount' => 2000.00, 'created_at' => now()->subDays(2)],
            ['description' => 'Book purchase', 'amount' => -25.00, 'created_at' => now()->subDay()],
        ];

        foreach ($ops as $data) {
            Operation::factory()->for($user)->create($data);
        }

        // Search
        $this->actingAs($user)
            ->getJson('/api/operations?q=coffee&sort=date&dir=desc&per_page=10')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.description', 'Coffee beans');

        // Sort ascending by date
        $this->actingAs($user)
            ->getJson('/api/operations?sort=date&dir=asc&per_page=10')
            ->assertOk()
            ->assertJsonPath('data.0.description', 'Coffee beans')
            ->assertJsonPath('data.1.description', 'Salary payout')
            ->assertJsonPath('data.2.description', 'Book purchase');

        // Pagination with per_page=2
        $response = $this->actingAs($user)
            ->getJson('/api/operations?sort=date&dir=desc&per_page=2&page=1')
            ->assertOk()
            ->assertJsonPath('total', 3)
            ->assertJsonPath('per_page', 2)
            ->assertJsonCount(2, 'data');

        $this->actingAs($user)
            ->getJson('/api/operations?sort=date&dir=desc&per_page=2&page=2')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.description', 'Coffee beans');
    }
}