<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use App\Models\UserWarehouseAccess;
use App\Models\User;
use Illuminate\Database\Seeder;

class WarehouseGovernanceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Core Warehouses
        $sp = Warehouse::firstOrCreate(['code' => 'SPAREPART'], ['name' => 'Spareparts Warehouse', 'status' => 'ACTIVE']);
        $rm = Warehouse::firstOrCreate(['code' => 'RAW_MATERIAL'], ['name' => 'Raw Materials Warehouse', 'status' => 'ACTIVE']);
        $cs = Warehouse::firstOrCreate(['code' => 'CONSUMABLE'], ['name' => 'Consumables Warehouse', 'status' => 'ACTIVE']);
        $fg = Warehouse::firstOrCreate(['code' => 'FINISHED_GOODS'], ['name' => 'Finished Goods Warehouse', 'status' => 'ACTIVE']);

        // 2. Grant permissions to Admin user (adminsp@peroniks.com)
        $admin = User::where('email', 'adminsp@peroniks.com')->first();
        if ($admin) {
            foreach ([$sp, $rm, $cs, $fg] as $wh) {
                UserWarehouseAccess::firstOrCreate([
                    'user_id' => $admin->id,
                    'warehouse_id' => $wh->id,
                ], [
                    'can_stock_in' => true,
                    'can_stock_out' => true,
                    'can_opname' => true,
                    'can_adjust' => true,
                    'can_print' => true,
                    'can_view_reports' => true,
                ]);
            }
        }
    }
}
