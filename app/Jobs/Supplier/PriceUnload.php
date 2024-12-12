<?php

namespace App\Jobs\Supplier;

use App\Contracts\ReportContract;
use App\Enums\TaskTypes;
use App\Exceptions\ReportCancelled;
use App\Helpers\Helpers;
use App\Models\EmailSupplier;
use App\Models\Report;
use App\Models\OzonMarket;
use App\Models\WbMarket;
use App\Services\EmailSupplierService;
use App\Services\SupplierReportService;
use Illuminate\Bus\Batch;
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
        $this->report = $this->reportContract->new(TaskTypes::SupplierEmailUnload, [], $this->emailSupplier->supplier);
        $this->queue = 'supplier-unload';
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

        $user = $this->emailSupplier->supplier->user;

        Helpers::toBatch(function (Batch $batch) use ($user, $emailSupplier) {

            $user->ozonMarkets()
                ->where('open', true)
                ->where('close', false)
                ->get()
                ->filter(fn(OzonMarket $market) => $market->suppliers()->where('id', $emailSupplier->supplier->id)->first())
                ->each(function (OzonMarket $market) use ($batch, $emailSupplier) {
                    $batch->add(new \App\Jobs\Ozon\PriceUnload($market, $emailSupplier));
                });

            $user->wbMarkets()
                ->where('open', true)
                ->where('close', false)
                ->get()
                ->filter(fn(WbMarket $market) => $market->suppliers()->where('id', $emailSupplier->supplier->id)->first())
                ->each(function (WbMarket $market) use ($batch, $emailSupplier) {
                    $batch->add(new \App\Jobs\Wb\PriceUnload($market, $emailSupplier));
                });
        }, 'market-unload');

        SupplierReportService::success($emailSupplier->supplier);
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
