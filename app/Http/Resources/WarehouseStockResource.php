<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Warehouse
 */
class WarehouseStockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'warehouse_id'   => $this->id,
            'warehouse_name' => $this->name,
            'products'       => StockResource::collection(
                $this->whenLoaded('stocks')
            ),
        ];
    }
}
