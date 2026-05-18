<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\StockInReceipt;
use Illuminate\Http\Request;

class WarehouseSwitchController extends Controller
{
    public function switchWarehouse(Request $request, $id)
    {
        $warehouse = Warehouse::findOrFail($id);

        // Enforce user warehouse access permissions boundary
        $hasAccess = auth()->user()->warehouses()->where('warehouses.id', $id)->exists();
        if (!$hasAccess) {
            abort(403, 'Unauthorized warehouse access domain.');
        }

        // 1. Clear Active Stock Out Session Carts
        session()->forget('scan_cart');

        // 2. Abandon Active Inbound Draft Receipts to avoid contamination
        \App\Models\StockInReceipt::where('user_id', auth()->id())
            ->where('status', 'ACTIVE')
            ->update(['status' => 'ABANDONED']);

        // 3. Save Active Tenant Context inside Session State
        session()->put('active_warehouse_id', $warehouse->id);
        session()->put('active_warehouse_code', $warehouse->code);
        session()->put('active_warehouse_name', $warehouse->name);

        return redirect()->back()->with('success', "Switched active warehouse to {$warehouse->name}.");
    }
}
