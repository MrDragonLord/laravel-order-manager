<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
 * @property string                $status
 *
 * @property-read Warehouse        $warehouse
 */
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
