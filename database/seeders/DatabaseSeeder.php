<?php

namespace Database\Seeders;

use App\Models\Movement;
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
        $now = now();

        foreach ($warehouses as $warehouse) {
            $movements = [];

            foreach ($products as $product) {
                $initialStock = rand(1, 100);

                Stock::factory()->create([
                    'warehouse_id' => $warehouse->id,
                    'product_id'   => $product->id,
                    'stock'        => $initialStock,
                ]);

                $movements[] = [
                    'warehouse_id'    => $warehouse->id,
                    'product_id'      => $product->id,
                    'quantity_change' => $initialStock,
                    'balance_after'   => $initialStock,
                    'type'            => 'initial',
                    'note'            => 'Initial stock',
                    'created_at'      => now()->subDays(rand(10, 365)),
                    'updated_at'      => $now,
                ];
            }

            Movement::insert($movements);


            $orders = Order::factory(rand(3, 5))->create([
                'warehouse_id' => $warehouse->id,
            ]);

            foreach ($orders as $order) {
                $movementsOrder = [];


                $items = OrderItem::factory(rand(2, 5))
                    ->create([
                        'order_id'   => $order->id,
                        'product_id' => $products->random()->id,
                        'count'      => $count = rand(1, 10),
                    ]);

                foreach ($items as $item) {
                    $stock = Stock::where([
                        'warehouse_id' => $warehouse->id,
                        'product_id'   => $item->product_id,
                    ])->first();

                    $newBalance = max(0, $stock->stock - $item->count);
                    $stock->update(['stock' => $newBalance]);

                    $movementsOrder[] = [
                        'warehouse_id'    => $warehouse->id,
                        'product_id'      => $item->product_id,
                        'quantity_change' => $item->count * -1,
                        'balance_after'   => $newBalance,
                        'type'            => 'sale',
                        'note'            => "Order #{$order->id}",
                        'created_at'      => $order->created_at,
                        'updated_at'      => $now,
                    ];
                }

                Movement::insert($movementsOrder);
            }
        }
    }
}
