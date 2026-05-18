<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWarehouseAccess extends Model
{
    use HasFactory;

    protected $table = 'user_warehouse_access';

    protected $fillable = [
        'user_id',
        'warehouse_id',
        'can_stock_in',
        'can_stock_out',
        'can_opname',
        'can_adjust',
        'can_print',
        'can_view_reports',
    ];

    protected $casts = [
        'can_stock_in' => 'boolean',
        'can_stock_out' => 'boolean',
        'can_opname' => 'boolean',
        'can_adjust' => 'boolean',
        'can_print' => 'boolean',
        'can_view_reports' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
