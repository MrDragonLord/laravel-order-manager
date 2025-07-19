<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Stock
 */
class StockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->whenLoaded('product')?->id,
            'name'  => $this->whenLoaded('product')?->name,
            'price' => $this->whenLoaded('product')?->price,
            'stock' => $this->stock,
        ];
    }
}
