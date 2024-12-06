<?php

namespace App\Jobs\Supplier;

use App\Contracts\ReportContract;
use App\Enums\TaskTypes;
use App\Exceptions\ReportCancelled;
use App\Models\EmailSupplier;
use App\Models\Report;
use App\Services\EmailSupplierService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PriceUnload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 600;
    public int $tries = 1;

    public ReportContract $reportContract;
    public Report $report;

    /**
     * Create a new job instance.
     */
    public function __construct(public EmailSupplier $emailSupplier, public string $path)
    {
        $this->reportContract = app(ReportContract::class);
        $this->report = $this->reportContract->new(TaskTypes::SupplierEmailUnload, [], $this->emailSupplier->supplier);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->reportContract->running($this->report);

        try {
            app(EmailSupplierService::class, [
                'supplier' => $this->emailSupplier,
                'path' => Storage::disk('public')->path($this->path),
                'report' => $this->report
            ])->unload();
        } catch (ReportCancelled $e) {
            return;
        }

        MarketsEmailSupplierUnload::dispatch($this->emailSupplier->supplier->user, $this->emailSupplier, $this->report);

    }

    public function failed(\Throwable $th): void
    {
        $this->reportContract->failed($this->report);
    }

    public function uniqueId(): string
    {
        return $this->emailSupplier->id . "price_unload";
    }
}
