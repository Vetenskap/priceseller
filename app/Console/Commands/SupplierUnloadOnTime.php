<?php

namespace App\Console\Commands;

use App\Jobs\Supplier\UnloadOnTime;
use App\Models\Supplier;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class SupplierUnloadOnTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supplier:unload-on-time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Supplier::where('open', true)
            ->where('unload_without_price', true)
            ->get()
            ->filter(fn(Supplier $supplier) => $supplier->user->isSub() || $supplier->user->isAdmin())
            ->each(function (Supplier $supplier) {
                UnloadOnTime::dispatch($supplier);
            });
    }
}
