<?php

namespace App\Jobs\Supplier;

use App\Models\EmailSupplier;
use App\Models\Supplier;
use App\Models\User;
use App\Services\EmailSupplierService;
use App\Services\OzonItemPriceService;
use App\Services\SupplierReportService;
use App\Services\WbItemPriceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PriceUnload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

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

        $supplier = $emailSupplier->supplier;
        $user = User::findOrFail($supplier->user_id);

        $ozonMarkets = $user->ozonMarkets()
            ->whereHas('items', function (Builder $query) use ($supplier) {
                $query->whereHas('item', function (Builder $query) use ($supplier) {
                    $query->where('supplier_id', $supplier->id);
                });
            })
            ->where('open', true)
            ->where('close', false)
            ->get();

        $wbMarkets = $user->wbMarkets()
            ->whereHas('items', function (Builder $query) use ($supplier) {
                $query->whereHas('item', function (Builder $query) use ($supplier) {
                    $query->where('supplier_id', $supplier->id);
                });
            })
            ->where('open', true)
            ->where('close', false)
            ->get();

        foreach ($ozonMarkets as $market) {
            $service = new OzonItemPriceService($supplier, $market);
            $service->updateStock();
            $service->updatePrice();
            $service->unloadAllStocks();
            $service->unloadAllPrices();
        }

        foreach ($wbMarkets as $market) {
            $service = new WbItemPriceService($supplier, $market);
            $service->updateStock();
            $service->updatePrice();
            $service->unloadAllStocks();
            $service->unloadAllPrices();
        }

        SupplierReportService::success($emailSupplier->supplier);

    }

    public function failed(\Throwable $th)
    {
        $emailSupplier = EmailSupplier::findOrFail($this->emailSupplierId);
        SupplierReportService::error($emailSupplier->supplier);
    }
}
