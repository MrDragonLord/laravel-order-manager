<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        $created   = $this->faker->dateTimeBetween('-1 year', 'now');
        $completed = $this->faker->boolean(70)
            ? $this->faker->dateTimeBetween($created, 'now')
            : null;

        return [
            'customer'     => $this->faker->name(),
            'created_at'   => $created,
            'completed_at' => $completed,
            'warehouse_id' => Warehouse::factory(),
            'status'       => $this->faker->randomElement(['active', 'completed', 'canceled']),
        ];
    }
}
