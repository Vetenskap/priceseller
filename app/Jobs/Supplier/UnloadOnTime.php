<?php

namespace App\Jobs\Supplier;

use App\Contracts\MarketContract;
use App\Contracts\ReportContract;
use App\Enums\TaskTypes;
use App\Models\Report;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UnloadOnTime implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public int $uniqueFor = 7200;
    public int $tries = 1;
    public ReportContract $reportContract;
    public Report $report;


    /**
     * Create a new job instance.
     */
    public function __construct(public Supplier $supplier)
    {
        $this->reportContract = app(ReportContract::class);
        $this->report = $this->reportContract->new(TaskTypes::SupplierUnload, [
            'type' => 'По времени',
            'path' => ''
        ], $supplier);
        $this->queue = 'supplier-unload';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->reportContract->running($this->report);

        \app(SupplierService::class)->setAllItemsUpdated($this->supplier);

        $marketContract = app(MarketContract::class);
        $this->reportContract->addLog($this->report, 'Выгружаем новые данные в кабинеты..');
        $marketContract->unload($this->supplier, $this->report);

        $this->reportContract->finished($this->report);

    }

    public function failed(\Throwable $th): void
    {
        $this->reportContract->failed($this->report);
    }

    public function uniqueId(): string
    {
        return $this->supplier->id . "unload_on_time";
    }
}
