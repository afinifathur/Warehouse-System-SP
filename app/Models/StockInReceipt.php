<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockInReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_code',
        'user_id',
        'supplier_id',
        'purchase_order_ref',
        'status',
        'last_activity_at',
        'warehouse_id',
        'operator_id',
        'terminal_id',
        'terminal_session_id',
        'erp_transfer_status',
        'transferred_by',
        'transferred_at',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
        'transferred_at' => 'datetime',
    ];

    public function transferredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }

    /**
     * Owner user relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Supplier relationship.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Line items relationship.
     */
    public function items(): HasMany
    {
        return $this->hasMany(StockInItem::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function scopeForActiveWarehouse($query)
    {
        $strict = env('WMS_GOVERNANCE_STRICT_MODE', true);
        $activeWarehouseId = session()->get('active_warehouse_id');

        if ($activeWarehouseId) {
            return $query->where($this->getTable() . '.warehouse_id', $activeWarehouseId);
        }

        if ($strict) {
            return $query->whereRaw('1 = 0');
        }

        return $query;
    }
}
