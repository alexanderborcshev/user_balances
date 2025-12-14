<?php

namespace Database\Factories;

use App\Models\Operation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Operation>
 */
class OperationFactory extends Factory
{
    protected $model = Operation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'amount' => fake()->randomFloat(2, -5000, 5000),
            'description' => fake()->sentence(),
            'created_at' => now()->subDays(fake()->numberBetween(0, 90)),
            'updated_at' => now(),
        ];
    }
}