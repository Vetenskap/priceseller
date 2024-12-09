<?php

namespace Modules\BergApi\Jobs;

use App\Jobs\Supplier\MarketsUnload;
use App\Services\OzonItemPriceService;
use App\Services\SupplierReportService;
use App\Services\WbItemPriceService;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\BergApi\Models\BergApi;
use Modules\BergApi\Services\BergUnloadService;

class BergUserProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    /**
     * Create a new job instance.
     */
    public function __construct(public BergApi $bergApi)
    {
        $this->queue = 'email-supplier-unload';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (SupplierReportService::get($this->bergApi->supplier)) {
            return;
        } else {
            SupplierReportService::new($this->bergApi->supplier, message: 'по АПИ');
        }

        $service = new BergUnloadService($this->bergApi);
        $service->getNewPrice();

        $user = $this->bergApi->user;
        $supplier = $this->bergApi->supplier;

        MarketsUnload::dispatch($user, $supplier, 'по АПИ');

    }

    public function failed(\Throwable $th)
    {
        SupplierReportService::error($this->bergApi->supplier, message: 'по АПИ');
    }
}
