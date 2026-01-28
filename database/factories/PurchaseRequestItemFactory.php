<?php

namespace Database\Factories;

use App\Models\PurchaseRequestItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseRequestItem>
 */
class PurchaseRequestItemFactory extends Factory
{
    protected $model = PurchaseRequestItem::class;

    public function definition(): array
    {
        return [
            'item_name' => fake()->word(),
            'quantity' => fake()->numberBetween(1, 100),
            'unit' => fake()->randomElement(['шт', 'м', 'кг', 'л', 'упак']),
            'estimated_price' => fake()->optional(0.7)->randomFloat(2, 10, 5000),
            'item_code' => fake()->optional(0.6)->bothify('???-####'),
            'specifications' => fake()->optional(0.4)->sentence(),
        ];
    }
}
