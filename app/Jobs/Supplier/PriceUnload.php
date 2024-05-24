<?php

namespace App\Jobs\Supplier;

use App\Models\EmailSupplier;
use App\Services\EmailSupplierService;
use App\Services\SupplierReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PriceUnload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $emailSupplierId, public string $path)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $emailSupplier = EmailSupplier::findOrFail($this->emailSupplierId);

        if (SupplierReportService::get($emailSupplier->supplier)) {
            SupplierReportService::changeMessage($emailSupplier->supplier, 'Повторная попытка выгрузки');
        } else {
            SupplierReportService::new($emailSupplier->supplier, $this->path);
        }

        $service = new EmailSupplierService($emailSupplier, Storage::disk('public')->path($this->path));
        $service->unload();

        Bus::batch([
            [
                \App\Jobs\Wb\Unload::dispatch($emailSupplier->supplier_id)
            ],
            [
                \App\Jobs\Ozon\Unload::dispatch($emailSupplier->supplier_id)
            ]
        ])->then(function () use ($emailSupplier) {
            SupplierReportService::success($emailSupplier->supplier);
        })->catch(function () use ($emailSupplier) {
            SupplierReportService::error($emailSupplier->supplier);
        });

    }

    public function failed(\Throwable $th)
    {
        $emailSupplier = EmailSupplier::findOrFail($this->emailSupplierId);
        SupplierReportService::error($emailSupplier->supplier);
    }
}
