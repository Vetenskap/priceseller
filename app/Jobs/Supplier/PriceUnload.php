<?php

namespace App\Jobs\Supplier;

use App\Contracts\ReportContract;
use App\Contracts\SupplierUnloadContract;
use App\Enums\TaskTypes;
use App\Exceptions\ReportCancelled;
use App\Models\EmailSupplier;
use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PriceUnload implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 7200;
    public int $tries = 1;

    public ReportContract $reportContract;
    public Report $report;

    /**
     * Create a new job instance.
     */
    public function __construct(public EmailSupplier $emailSupplier, public string $path)
    {
        $this->reportContract = app(ReportContract::class);
        $this->report = $this->reportContract->new(TaskTypes::SupplierUnload, [
            'type' => $this->emailSupplier->mainEmail->address,
            'path' => $path
        ], $this->emailSupplier->supplier);
        $this->queue = 'supplier-unload';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->reportContract->running($this->report);

        try {
            $service = app(SupplierUnloadContract::class);
            $service->make($this->emailSupplier, Storage::disk('public')->path($this->path), $this->report);
            $service->unload();
        } catch (ReportCancelled $e) {
            return;
        }

        $this->reportContract->finished($this->report);
    }

    public function failed(\Throwable $th): void
    {
        $this->reportContract->failed($this->report);
    }

    public function uniqueId(): string
    {
        return $this->emailSupplier->id . "_price_unload";
    }
}
