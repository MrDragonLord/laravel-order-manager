<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderUpdateRequest;
use App\Http\Resources\OrderResource;
use App\Models\Movement;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class OrderUpdateController extends Controller
{
    /**
     * Изменение заказа (без статуса)
     *
     * @param OrderUpdateRequest $request
     * @param string $id
     * @return OrderResource
     */
    public function __invoke(OrderUpdateRequest $request, string $id): OrderResource
    {
        $order = Order::findOrFail($id);

        return DB::transaction(function () use ($request, $order) {
            $data = $request->validated();

            if (!empty($data['customer'])) {
                $order->update(['customer' => $data['customer']]);
            }

            if (!empty($data['items'])) {
                $oldItems = $order->items()->get();
                $productIdsOld = $oldItems->pluck('product_id')->toArray();
                $countsByOld = $oldItems->pluck('count', 'product_id')->toArray();

                Stock::where('warehouse_id', $order->warehouse_id)
                    ->whereIn('product_id', $productIdsOld)
                    ->get()
                    ->each(function (Stock $stock) use ($countsByOld) {
                        $stock->increment('stock', $countsByOld[$stock->product_id]);
                    });

                $order->items()->delete();

                $newProductIds = collect($data['items'])->pluck('product_id')->toArray();
                $stocks = Stock::where('warehouse_id', $order->warehouse_id)
                    ->whereIn('product_id', $newProductIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('product_id');

                $now = now();
                $insertItems = [];
                $movementData = [];

                foreach ($data['items'] as $item) {
                    $productId = $item['product_id'];
                    $count     = $item['count'];

                    if (!isset($stocks[$productId])) {
                        abort(422, "Нет склада для товара $productId");
                    }

                    $stock = $stocks[$productId];
                    if ($stock->stock < $count) {
                        abort(422, "Недостаточно товара $productId");
                    }

                    $stock->decrement('stock', $count);
                    $delta = $count * -1;

                    $movementData[] = [
                        'warehouse_id'    => $stock->warehouse_id,
                        'product_id'      => $productId,
                        'quantity_change' => $delta,
                        'balance_after'   => $stock->stock - $delta,
                        'type'            => 'sale',
                        'note'            => "Order #{$order->id}",
                        'created_at'      => $now,
                        'updated_at'      => $now,
                    ];

                    $insertItems[] = [
                        'order_id'   => $order->id,
                        'product_id' => $productId,
                        'count'      => $count,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                if (!empty($insertItems)) {
                    OrderItem::insert($insertItems);
                }

                if (!empty($movementData)) {
                    Movement::insert($movementData);
                }
            }

            return new OrderResource($order->load(['warehouse', 'items', 'items.product', 'items.product.stock']));
        });
    }
}
