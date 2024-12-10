<?php

namespace App\Jobs\Supplier;

use App\Helpers\Helpers;
use App\Models\EmailSupplier;
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

    /**
     * Create a new job instance.
     */
    public function __construct(public int $emailSupplierId, public string $path)
    {
        $this->queue = 'supplier-unload';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $emailSupplier = EmailSupplier::findOrFail($this->emailSupplierId);

        if (app(SupplierReportService::class)->get($emailSupplier->supplier)) {
            return;
        } else {
            app(SupplierReportService::class)->new($emailSupplier->supplier, $this->path, "({$emailSupplier->mainEmail->name})");
        }

        app(EmailSupplierService::class, [
            'supplier' => $emailSupplier,
            'path' => Storage::disk('public')->path($this->path)
        ])->unload();

        $user = $emailSupplier->supplier->user;

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
        $emailSupplier = EmailSupplier::findOrFail($this->emailSupplierId);
        app(SupplierReportService::class)->error($emailSupplier->supplier);
    }

    public function uniqueId(): string
    {
        return $this->emailSupplierId . "price_unload";
    }
}
