<?php

namespace Modules\SamsonApi\Jobs;

use App\Contracts\ReportContract;
use App\Enums\TaskTypes;
use App\Exceptions\ReportCancelled;
use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\SamsonApi\Contracts\SamsonUnloadContract;
use Modules\SamsonApi\Models\SamsonApi;

class SamsonUserProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ReportContract $reportContract;
    public Report $report;

    public int $tries = 2;
    public int $backoff = 600;
    /**
     * Create a new job instance.
     */
    public function __construct(public SamsonApi $samsonApi)
    {
        $this->reportContract = app(ReportContract::class);
        $this->report = $this->reportContract->new(TaskTypes::SupplierUnload, [
            'type' => 'По АПИ',
            'path' => ''
        ], $this->samsonApi->supplier);
        $this->queue = 'supplier-unload';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->reportContract->running($this->report);

        $service = app(SamsonUnloadContract::class);

        try {
            $service->getNewPrice();
        } catch (ReportCancelled $e) {
            return;
        }

        $this->reportContract->finished($this->report);
    }

    public function failed(\Throwable $th)
    {
        $this->reportContract->failed($this->report);
    }
}
