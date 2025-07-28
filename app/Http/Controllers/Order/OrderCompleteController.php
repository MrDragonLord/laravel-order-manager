<?php

namespace App\Http\Controllers\Order;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderCompleteController extends Controller
{
    /**
     * Завершение заказа
     *
     * @param string $id
     * @return OrderResource|AuthorizationException|ModelNotFoundException
     * @throws AuthorizationException
     */
    public function __invoke(string $id): OrderResource|AuthorizationException|ModelNotFoundException
    {
        $order = Order::findOrFail($id);

        $this->authorize('complete', $order);

        $order->update([
            'status'       => OrderStatusEnum::COMPLETED,
            'completed_at' => now(),
        ]);

        return new OrderResource($order->load(['warehouse', 'items', 'items.product', 'items.product.stock']));
    }
}
