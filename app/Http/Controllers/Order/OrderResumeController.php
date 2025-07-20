<?php

namespace App\Http\Controllers\Order;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Stock;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class OrderResumeController extends Controller
{
    /**
     * Возобнавление заказа
     *
     * @param string $id
     * @return OrderResource|AuthorizationException
     * @throws AuthorizationException
     */
    public function __invoke(string $id): OrderResource|AuthorizationException
    {
        $order = Order::findOrFail($id);

        $this->authorize('resume', $order);

        return DB::transaction(function () use ($order) {
            $items      = $order->items()->select('product_id', 'count')->get();
            $productIds = $items->pluck('product_id')->all();
            $required   = $items->pluck('count', 'product_id');

            $stocks = DB::table('stocks')
                ->where('warehouse_id', $order->warehouse_id)
                ->whereIn('product_id', $productIds)
                ->lockForUpdate()
                ->pluck('stock', 'product_id');

            foreach ($required as $pid => $cnt) {
                if (($stocks[$pid] ?? 0) < $cnt) {
                    abort(422, "Insufficient stock to resume order $order->id");
                }
            }

            // Строим SQL CASE для массового обновления остатков
            $caseSql = collect($required)
                ->map(fn($cnt, $pid) => "WHEN {$pid} THEN {$cnt}")
                ->implode(' ');

            // Обновляем все позиции на стоке одним запросом
            Stock::where('warehouse_id', $order->warehouse_id)
                ->whereIn('product_id', $productIds)
                ->update([
                    'stock' => DB::raw("stock - CASE product_id $caseSql END"),
                ]);

            $order->update(['status' => OrderStatusEnum::ACTIVE]);

            return new OrderResource($order->load(['warehouse', 'items', 'items.product', 'items.product.stock']));
        });
    }
}
