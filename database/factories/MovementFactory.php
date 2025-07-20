<?php

namespace Database\Factories;

use App\Models\Movement;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movement>
 */
class MovementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Movement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $warehouse = Warehouse::factory();
        $product = Product::factory();

        $quantityChange = $this->faker->numberBetween(-20, 20);

        $balanceAfter = max(0, $this->faker->numberBetween(0, 100) + $quantityChange);

        return [
            'warehouse_id'    => $warehouse,
            'product_id'      => $product,
            'quantity_change' => $quantityChange,
            'balance_after'   => $balanceAfter,
            'type'            => $this->faker->randomElement(['sale', 'restock', 'adjustment']),
            'note'            => $this->faker->optional()->sentence(),
            'created_at'      => $this->faker->dateTimeBetween('-1 years', 'now'),
            'updated_at'      => now(),
        ];
    }
}
