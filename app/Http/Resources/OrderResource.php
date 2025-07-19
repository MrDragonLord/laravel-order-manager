<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Order
 */
class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'customer'     => $this->customer,
            'created_at'   => $this->created_at?->toDateTimeString(),
            'completed_at' => $this->completed_at?->toDateTimeString(),
            'status'       => $this->status,
            'warehouse'    => new WarehouseResource($this->whenLoaded('warehouse')),
            'items'        => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
