<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cost = fake()->randomFloat(2, 5, 400);
        $price = round($cost * fake()->randomFloat(2, 1.1, 2.5), 2);
        $name = fake()->unique()->words(3, true);

        return [
            // CHAR(12) primary key, e.g. "APL-4821-009".
            'code' => strtoupper(fake()->unique()->bothify('???-####-###')),
            'name' => ucfirst($name),
            'description' => fake()->sentence(14),
            'manufacturer' => fake()->randomElement(['Apple', 'Sony', 'Samsung', 'Dell', 'HP', 'Lenovo']),
            'photo' => 'https://placehold.co/600x400?text='.urlencode($name),
            'price' => $price,
            'cost' => $cost,
            'inventory_quantity' => fake()->numberBetween(0, 500),
        ];
    }
}
