<?php

namespace App\Http\Controllers\Order;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Stock;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderCancelController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param string $id
     * @return OrderResource|AuthorizationException
     * @throws AuthorizationException
     */
    public function __invoke(string $id): OrderResource|AuthorizationException
    {
        $order = Order::findOrFail($id);

        $this->authorize('cancel', $order);

        return DB::transaction(function () use ($order) {
            $items = $order->items()->select('product_id', 'count')->get();
            $productIds = $items->pluck('product_id')->all();
            $countsById = $items->pluck('count', 'product_id');

            $caseSql = collect($countsById)
                ->map(fn($count, $pid) => "WHEN {$pid} THEN {$count}")
                ->implode(' ');

            Stock::where('warehouse_id', $order->warehouse_id)
                ->whereIn('product_id', $productIds)
                ->update([
                    'stock' => DB::raw("stock + CASE product_id $caseSql END"),
                ]);

            $order->update([
                'status'       => OrderStatusEnum::CANCELED->value,
                'completed_at' => null,
            ]);

            return new OrderResource($order->load(['warehouse', 'items', 'items.product', 'items.product.stock']));
        });
    }
}
