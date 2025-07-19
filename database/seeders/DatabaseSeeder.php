<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(5)->create();
        $warehouses = Warehouse::factory(5)->create();
        $products = Product::factory(20)->create();

        foreach ($warehouses as $warehouse) {
            foreach ($products as $product) {
                Stock::factory()->create([
                    'warehouse_id' => $warehouse->id,
                    'product_id'   => $product->id,
                    'stock'        => rand(1, 100),
                ]);
            }

            $orders = Order::factory(rand(3, 5))->create([
                'warehouse_id' => $warehouse->id,
            ]);

            foreach ($orders as $order) {
                OrderItem::factory(rand(2, 5))
                    ->create([
                        'order_id'   => $order->id,
                        'product_id' => $products->random()->id,
                    ]);
            }
        }
    }
}
