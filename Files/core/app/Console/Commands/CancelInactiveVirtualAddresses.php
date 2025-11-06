<?php

namespace App\Console\Commands;

use App\Services\VirtualAddressService;
use Illuminate\Console\Command;

class CancelInactiveVirtualAddresses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'virtualaddress:cancel-inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel virtual addresses for customers with no orders in the past 12 months';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting virtual address cleanup...');

        $virtualAddressService = app(VirtualAddressService::class);

        $cancelledCount = $virtualAddressService->cancelInactiveAddresses();

        if ($cancelledCount > 0) {
            $this->info("Successfully cancelled {$cancelledCount} inactive virtual address(es).");
        } else {
            $this->info('No inactive virtual addresses found.');
        }

        return Command::SUCCESS;
    }
}
