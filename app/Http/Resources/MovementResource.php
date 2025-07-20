<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Movement
 */
class MovementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'warehouse'       => [
                'id'   => $this->whenLoaded('warehouse')?->id,
                'name' => $this->whenLoaded('warehouse')?->name,
            ],
            'product'         => [
                'id'   => $this->whenLoaded('warehouse')?->id,
                'name' => $this->whenLoaded('product')?->name,
            ],
            'quantity_change' => $this->quantity_change,
            'balance_after'   => $this->balance_after,
            'type'            => $this->type,
            'note'            => $this->note,
            'created_at'      => $this->created_at?->toDateTimeString(),
        ];
    }
}
