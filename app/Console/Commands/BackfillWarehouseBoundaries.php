<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillWarehouseBoundaries extends Command
{
    protected $signature = 'wms:backfill-warehouses';
    protected $description = 'Safely and segmentally backfill warehouse_id values based on item catalog prefix mappings';

    public function handle()
    {
        $this->info("Initializing WMS physical warehouse boundary backfill...");

        // 1. Resolve Warehouse Primary Keys
        $spId = DB::table('warehouses')->where('code', 'SPAREPART')->value('id');
        $rmId = DB::table('warehouses')->where('code', 'RAW_MATERIAL')->value('id');
        $csId = DB::table('warehouses')->where('code', 'CONSUMABLE')->value('id');
        $fgId = DB::table('warehouses')->where('code', 'FINISHED_GOODS')->value('id');

        if (!$spId || !$rmId || !$csId || !$fgId) {
            $this->error("Error: Core warehouses are missing. Run the WarehouseGovernanceSeeder first!");
            return 1;
        }

        // 2. Backfill Bins via Prefix Analysis
        // 5.xxx / 6.xxx / 7.xxx -> SPAREPART, 1.xxx -> RAW_MATERIAL, 3.xxx -> CONSUMABLE, 4.xxx -> FINISHED_GOODS
        $this->info("Segment 1: Backfilling physical bins...");
        DB::table('bins')->chunkById(100, function ($bins) use ($spId, $rmId, $csId, $fgId) {
            foreach ($bins as $bin) {
                $targetId = $spId; // default fallback
                
                if (str_starts_with($bin->code, '1.')) {
                    $targetId = $rmId;
                } elseif (str_starts_with($bin->code, '3.')) {
                    $targetId = $csId;
                } elseif (str_starts_with($bin->code, '4.')) {
                    $targetId = $fgId;
                }

                DB::table('bins')->where('id', $bin->id)->update(['warehouse_id' => $targetId]);
            }
        });
        $this->info("Bins successfully completed.");

        // 3. Backfill committed transactions
        $this->info("Segment 2: Backfilling committed stock transactions...");
        DB::table('stock_transactions')->whereNull('warehouse_id')->chunkById(100, function ($transactions) use ($spId) {
            foreach ($transactions as $tx) {
                DB::table('stock_transactions')->where('id', $tx->id)->update([
                    'warehouse_id' => $spId, // default all past movements to Spareparts (which is active)
                ]);
            }
        });
        $this->info("Transactions successfully completed.");

        // 4. Backfill inbound receipts
        $this->info("Segment 3: Backfilling stock inbound receipts...");
        DB::table('stock_in_receipts')->whereNull('warehouse_id')->chunkById(100, function ($receipts) use ($spId) {
            foreach ($receipts as $rc) {
                DB::table('stock_in_receipts')->where('id', $rc->id)->update([
                    'warehouse_id' => $spId,
                ]);
            }
        });
        $this->info("Inbound receipts successfully completed.");

        // 5. Backfill immutable stock movements ledger
        $this->info("Segment 4: Backfilling immutable stock movements...");
        DB::table('stock_movements')->whereNull('warehouse_id')->chunkById(100, function ($movements) use ($spId) {
            foreach ($movements as $mov) {
                DB::table('stock_movements')->where('id', $mov->id)->update([
                    'warehouse_id' => $spId,
                ]);
            }
        });
        $this->info("Stock movements backfill successfully completed!");

        return 0;
    }
}
