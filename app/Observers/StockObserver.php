<?php

namespace App\Observers;

use App\Models\Movement;
use App\Models\Stock;

class StockObserver
{

    /**
     * Handle the Stock "updated" event.
     */
    public function updated(Stock $stock): void
    {
        if (!$stock->isDirty('stock')) {
            return;
        }

        $old = (int) $stock->getOriginal('stock');
        $new = (int) $stock->stock;
        $diff = $new - $old;

        Movement::create([
            'warehouse_id'    => $stock->warehouse_id,
            'product_id'      => $stock->product_id,
            'quantity_change' => $diff,
            'balance_after'   => $new,
            'type'            => $diff > 0 ? 'restock' : 'sale',
            'note'            => 'Auto from Stock model',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }
}
