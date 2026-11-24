<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'total_price' => fake()->randomFloat(2, 20, 2500),
            'purchase_time' => fake()->dateTimeBetween('-3 years', 'now'),
            'payment_id' => fake()->numberBetween(1, 5),
            'email' => User::query()->inRandomOrder()->value('email') ?? User::factory()->create()->email,
        ];
    }
}
