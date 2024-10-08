<?php

namespace Modules\VoshodApi\Jobs;

use App\Jobs\Supplier\MarketsUnload;
use App\Services\SupplierReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\VoshodApi\Models\VoshodApi;
use Modules\VoshodApi\Services\VoshodUnloadService;

class VoshodUserProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public VoshodApi $voshodApi)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (SupplierReportService::get($this->voshodApi->supplier)) {
            return;
        } else {
            SupplierReportService::new($this->voshodApi->supplier);
        }

        $service = new VoshodUnloadService($this->voshodApi);
        $service->getNewPrice();

        $user = $this->voshodApi->user;
        $supplier = $this->voshodApi->supplier;

        MarketsUnload::dispatch($user, $supplier);
    }

    public function failed(\Throwable $th)
    {
        SupplierReportService::error($this->voshodApi->supplier);
    }
}
