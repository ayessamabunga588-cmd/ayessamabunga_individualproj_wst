<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use Carbon\Carbon;
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
        // 1. Users. "john@example.com" is required by the filtering exercises.
        User::factory()->create([
            'name' => 'John Doe',
            'username' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone_number' => '09171234567',
            'last_login_time' => now(),
            'address_id' => 1,
            'email' => 'john@example.com',
        ]);

        User::factory(29)->create();

        // 2. Products.
        Product::factory(60)->create();

        // 3. Purchases spread over four years so some years hold more than
        //    100 purchases (the "purchase trends" exercise needs that).
        $users = User::all();
        $products = Product::all();

        $purchasesPerYear = [
            now()->subYears(3)->year => 130,
            now()->subYears(2)->year => 115,
            now()->subYears(1)->year => 140,
            now()->year => 95,
        ];

        foreach ($purchasesPerYear as $year => $count) {
            for ($i = 0; $i < $count; $i++) {
                $purchaseTime = Carbon::create(
                    $year,
                    fake()->numberBetween(1, 12),
                    fake()->numberBetween(1, 28),
                    fake()->numberBetween(0, 23),
                    fake()->numberBetween(0, 59),
                );

                // Keep the current year's purchases in the past.
                if ($purchaseTime->isFuture()) {
                    $purchaseTime = now()->subDays(fake()->numberBetween(1, 30));
                }

                $purchase = Purchase::factory()->create([
                    'email' => $users->random()->email,
                    'purchase_time' => $purchaseTime,
                ]);

                $totalPrice = 0;

                foreach ($products->random(fake()->numberBetween(1, 3)) as $product) {
                    $quantity = fake()->numberBetween(1, 5);

                    $purchase->items()->create([
                        'code' => $product->code,
                        'product_price' => $product->price,
                        'product_quantity' => $quantity,
                        'product_name' => $product->name,
                    ]);

                    $totalPrice += (float) $product->price * $quantity;
                }

                $purchase->update(['total_price' => $totalPrice]);
            }
        }
    }
}
