<?php

namespace Modules\SamsonApi\Jobs;

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
use Modules\SamsonApi\Models\SamsonApi;
use Modules\SamsonApi\Services\SamsonUnloadService;

class SamsonUserProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    /**
     * Create a new job instance.
     */
    public function __construct(public SamsonApi $samsonApi)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (SupplierReportService::get($this->samsonApi->supplier)) {
            return;
        } else {
            SupplierReportService::new($this->samsonApi->supplier);
        }

        $service = new SamsonUnloadService($this->samsonApi);
        $service->getNewPrice();

        $user = $this->samsonApi->user;
        $supplier = $this->samsonApi->supplier;

        MarketsUnload::dispatch($user, $supplier);
    }

    public function failed(\Throwable $th)
    {
        SupplierReportService::error($this->samsonApi->supplier);
    }
}
