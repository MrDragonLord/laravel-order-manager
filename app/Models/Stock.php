<?php

namespace App\Models;

use App\Http\Resources\StockResource;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Stock
 *
 * @extends Model<Stock>
 *
 * @property int             $product_id
 * @property int             $warehouse_id
 * @property int             $stock
 *
 * @property-read  Product   $product
 * @property-read  Warehouse $warehouse
 */
#[ObservedBy([StockResource::class])]
class Stock extends Model
{
    /** @use HasFactory<\Database\Factories\StockFactory> */
    use HasFactory;

    /**
     * Атрибуты, доступные для массового заполнения.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'stock',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
