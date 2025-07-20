<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Movement
 *
 * @extends Model<Movement>
 *
 * @property int         $id
 * @property int         $warehouse_id
 * @property int         $product_id
 * @property int         $quantity_change
 * @property int         $balance_after
 * @property string|null $type
 * @property string|null $note
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read Warehouse $warehouse
 * @property-read Product   $product
 *
 * @method static Builder scopeWarehouse(int|null $warehouseId)
 * @method static Builder scopeProduct(int|null $productId)
 * @method static Builder dateFrom(string|null $from)
 * @method static Builder dateTo(string|null $to)
 */
class Movement extends Model
{
    /** @use HasFactory<\Database\Factories\MovementFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'warehouse_id',
        'product_id',
        'quantity_change',
        'balance_after',
        'type',
        'note',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope a query to filter by warehouse.
     *
     * @param Builder      $query
     * @param int|null     $warehouseId
     * @return Builder
     */
    #[Scope]
    public function scopeWarehouse(Builder $query, ?int $warehouseId): Builder
    {
        return $warehouseId !== null
            ? $query->where('warehouse_id', $warehouseId)
            : $query;
    }

    /**
     * Scope a query to filter by product.
     *
     * @param Builder      $query
     * @param int|null     $productId
     * @return Builder
     */
    #[Scope]
    public function scopeProduct(Builder $query, ?int $productId): Builder
    {
        return $productId !== null
            ? $query->where('product_id', $productId)
            : $query;
    }

    /**
     * Scope a query for movements from a given date.
     *
     * @param Builder      $query
     * @param string|null  $from
     * @return Builder
     */
    #[Scope]
    public function dateFrom(Builder $query, ?string $from): Builder
    {
        return $from
            ? $query->whereDate('created_at', '>=', $from)
            : $query;
    }

    /**
     * Scope a query for movements up to a given date.
     *
     * @param Builder      $query
     * @param string|null  $to
     * @return Builder
     */
    #[Scope]
    public function dateTo(Builder $query, ?string $to): Builder
    {
        return $to
            ? $query->whereDate('created_at', '<=', $to)
            : $query;
    }
}
