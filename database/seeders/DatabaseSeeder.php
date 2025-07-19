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
        User::factory(10)->create();

        $warehouses = Warehouse::factory(25)->create();

        $products = Product::factory(80)->create();

        foreach ($warehouses as $warehouse) {
            foreach ($products as $product) {
                Stock::factory()->create([
                    'warehouse_id' => $warehouse->id,
                    'product_id'   => $product->id,
                ]);
            }

            $orders = Order::factory(10)->create([
                'warehouse_id' => $warehouse->id,
            ]);

            foreach ($orders as $order) {
                OrderItem::factory(rand(5, 15))->create([
                    'order_id' => $order->id,
                ]);
            }
        }
    }
}
