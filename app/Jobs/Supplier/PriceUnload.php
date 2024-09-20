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
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class PriceUnload implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 600;
    public int $tries = 1;

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
            return;
        } else {
            SupplierReportService::new($emailSupplier->supplier, $this->path);
        }

        $service = new EmailSupplierService($emailSupplier, Storage::disk('public')->path($this->path));
        $service->unload();

        $supplier = $emailSupplier->supplier;
        $user = User::findOrFail($supplier->user_id);

        $ttl = Redis::ttl('laravel_unique_job:'.MarketsUnload::class.':'.MarketsUnload::getUniqueId($supplier));

        if ($ttl > 0) {
            SupplierReportService::addLog($supplier, 'Кабинеты этого поставщика уже выгружаются или не прошло 10 минут с первой выгрузки. Оставшееся время: ' . $ttl . ' секунд');
            SupplierReportService::error($supplier);
        } else {
            MarketsUnload::dispatch($user, $supplier);
        }

    }

    public function failed(\Throwable $th): void
    {
        $emailSupplier = EmailSupplier::findOrFail($this->emailSupplierId);
        SupplierReportService::error($emailSupplier->supplier);
    }

    public function uniqueId(): string
    {
        return $this->emailSupplierId . "price_unload";
    }
}
