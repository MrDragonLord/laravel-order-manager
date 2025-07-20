<?php

namespace App\Policies;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Заверешение
     *
     * @param User|null $user
     * @param Order $order
     * @return bool
     */
    public function complete(?User $user, Order $order): bool
    {
        return $order->status === OrderStatusEnum::ACTIVE;
    }

    /**
     * Отмена
     *
     * @param User|null $user
     * @param Order $order
     * @return bool
     */
    public function cancel(?User $user, Order $order): bool
    {
        return $order->status === OrderStatusEnum::ACTIVE;
    }

    /**
     * Возобновление
     *
     * @param User|null $user
     * @param Order $order
     * @return bool
     */
    public function resume(?User $user, Order $order): bool
    {
        if ($order->status !== OrderStatusEnum::CANCELED) {
            return false;
        }

        foreach ($order->items as $item) {
            $stock = $order->warehouse->stocks()
                ->where('product_id', $item->product_id)
                ->value('stock');

            if ($stock < $item->count) {
                return false;
            }
        }
        return true;
    }
}
