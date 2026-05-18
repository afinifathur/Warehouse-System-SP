<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockInItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_in_receipt_id',
        'item_variant_id',
        'qty',
        'bin_id',
        'supplier_id',
    ];

    /**
     * Parent receipt relationship.
     */
    public function receipt(): BelongsTo
    {
        return $this->belongsTo(StockInReceipt::class, 'stock_in_receipt_id');
    }

    /**
     * Item variant relationship.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ItemVariant::class, 'item_variant_id');
    }

    /**
     * Destination storage bin relationship.
     */
    public function bin(): BelongsTo
    {
        return $this->belongsTo(Bin::class, 'bin_id');
    }

    /**
     * Supplier relationship.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
