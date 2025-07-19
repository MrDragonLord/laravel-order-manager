<?php

namespace App\Models;

use App\Enums\OrderStatusEnum;
use App\Policies\OrderPolicy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Order
 *
 * @extends Model<Order>
 *
 * @property int                   $id
 * @property string                $customer
 * @property \Carbon\Carbon        $created_at
 * @property \Carbon\Carbon|null   $completed_at
 * @property int                   $warehouse_id
 * @property OrderStatusEnum       $status
 *
 * @property-read Warehouse        $warehouse
 * @property-read \Illuminate\Database\Eloquent\Collection<Product> $items
 *
 * @method static Builder status(?string $status)
 * @method static Builder customer(?string $customer)
 * @method static Builder dateFrom(?string $from)
 * @method static Builder dateTo(?string $to)
 * @mixin Builder
 */
#[UsePolicy(OrderPolicy::class)]
class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    public const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'customer',
        'created_at',
        'completed_at',
        'warehouse_id',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatusEnum::class,
            'completed_at' => 'datetime',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    #[Scope]
    public function status(Builder $query, ?string $status): Builder
    {
        return $status
            ? $query->where('status', $status)
            : $query;
    }

    #[Scope]
    public function customer(Builder $query, ?string $customer): Builder
    {
        return $customer
            ? $query->where('customer', 'like', "%{$customer}%")
            : $query;
    }

    #[Scope]
    protected function dateFrom(Builder $query, ?string $from): Builder
    {
        return $from
            ? $query->where('created_at', '>=', $from)
            : $query;
    }

    #[Scope]
    protected function dateTo(Builder $query, ?string $to): Builder
    {
       return $to
           ? $query->where('created_at', '<=', $to)
           : $query;
    }
}
