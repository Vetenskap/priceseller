<?php

namespace App\Jobs\Ozon;

use App\Models\Supplier;
use App\Models\User;
use App\Services\OzonItemPriceService;
use App\Services\SupplierReportService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class Unload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $supplierId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $supplier = Supplier::findOrFail($this->supplierId);
        $user = User::findOrFail($supplier->user_id);

        foreach ($user->ozonMarkets()->where('open', true)->get() as $market) {
            $service = new OzonItemPriceService($supplier, $market);
            $service->updateStock();
            $service->updatePrice();
            $service->unloadAllStocks();
            $service->unloadAllPrices();
        }
    }

    public function failed()
    {
        $supplier = Supplier::findOrFail($this->supplierId);
        SupplierReportService::error($supplier);
    }
}
