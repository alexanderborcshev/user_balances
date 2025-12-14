<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Operation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $alice = User::factory()->create([
            'name' => 'Alice Balance',
            'email' => 'alice@example.com',
            'password' => 'password',
        ]);

        $bob = User::factory()->create([
            'name' => 'Bob Ledger',
            'email' => 'bob@example.com',
            'password' => 'password',
        ]);

        // Operations for Alice
        Operation::factory()->count(5)->for($alice)->state(['amount' => 150.00])->create();
        Operation::factory()->count(3)->for($alice)->state(['amount' => -45.50])->create();

        // Operations for Bob
        Operation::factory()->count(4)->for($bob)->state(['amount' => 250.75])->create();
        Operation::factory()->count(2)->for($bob)->state(['amount' => -120.25])->create();
    }
}
