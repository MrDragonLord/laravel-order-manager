<?php

namespace App\Http\Controllers\Order;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Resources\OrderResource;
use App\Models\Movement;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class OrderStoreController extends Controller
{
    /**
     * Создание заказа и прикрепление товаров
     *
     * @param OrderStoreRequest $request
     * @return OrderResource
     */
    public function __invoke(OrderStoreRequest $request): OrderResource
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validated();

            $order = Order::create([
                'customer' => $data['customer'],
                'warehouse_id' => $data['warehouse_id'],
                'status' => OrderStatusEnum::ACTIVE->value,
                'created_at' => now(),
            ]);

            $items = [];

            $productIds = collect($data['items'])->pluck('product_id');
            $stocks = Stock::where('warehouse_id', $order->warehouse_id)
                ->whereIn('product_id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('product_id');

            $now = now();
            $movements = [];

            foreach ($data['items'] as $itemData) {
                $stock = $stocks[$itemData['product_id']];

                if ($stock->stock < $itemData['count']) {
                    abort(422, 'Insufficient stock for product ' . $itemData['product_id']);
                }
                $stock->decrement('stock', $itemData['count']);

                $items[] = [
                    'order_id' => $order->id,
                    'product_id' => $itemData['product_id'],
                    'count' => $itemData['count'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $movements[] = [
                    'warehouse_id'    => $order->warehouse_id,
                    'product_id'      => $itemData['product_id'],
                    'quantity_change' => $itemData['count'] * -1,
                    'balance_after'   => $stock->stock,
                    'type'            => 'sale',
                    'note'            => "Order #$order->id",
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
            }

            if (!empty($items)) {
                OrderItem::insert($items);
            }

            if (!empty($movements)) {
                Movement::insert($movements);
            }

            return new OrderResource($order->load(['warehouse', 'items', 'items.product', 'items.product.stock']));
        });
    }
}
