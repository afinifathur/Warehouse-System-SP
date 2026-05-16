<?php

namespace App\Console\Commands;

use App\Services\Barcode\PrintJobService;
use Illuminate\Console\Command;

class ClearPendingPrintJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'print:clear-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all pending print jobs by marking them as failed';

    /**
     * Execute the console command.
     */
    public function handle(PrintJobService $printJobService)
    {
        $this->info('Starting queue hygiene cleanup...');
        
        $count = $printJobService->clearAllPending();

        if ($count > 0) {
            $this->success("Successfully cleared $count pending jobs.");
        } else {
            $this->info('No pending jobs found in queue.');
        }

        return Command::SUCCESS;
    }
}
