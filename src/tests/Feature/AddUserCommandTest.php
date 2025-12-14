<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Tests\TestCase;

class AddUserCommandTest extends TestCase
{
    public function test_it_creates_user_via_command(): void
    {
        $this->artisan('user:add', [
            'name' => 'Alice Example',
            'email' => 'alice@example.com',
            'password' => 'secret123',
        ])->assertExitCode(CommandAlias::SUCCESS);

        $user = User::where('email', 'alice@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame('Alice Example', $user->name);
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    public function test_it_fails_for_duplicate_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->artisan('user:add', [
            'name' => 'Another User',
            'email' => 'taken@example.com',
            'password' => 'secret123',
        ])->assertExitCode(CommandAlias::FAILURE);
    }
}
