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

class PriceUnload implements ShouldQueue
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

        if (app(SupplierReportService::class)->get($emailSupplier->supplier)) {
            return;
        } else {
            app(SupplierReportService::class)->new($emailSupplier->supplier, $this->path, "({$emailSupplier->mainEmail->name})");
        }

        app(EmailSupplierService::class, [
            'supplier' => $emailSupplier,
            'path' => Storage::disk('public')->path($this->path)
        ])->unload();

        $ttl = Redis::ttl('laravel_unique_job:'.MarketsEmailSupplierUnload::class.':'.MarketsEmailSupplierUnload::getUniqueId($emailSupplier));

        if ($ttl > 0) {
            app(SupplierReportService::class)->addLog($emailSupplier->supplier, 'Кабинеты этого поставщика уже выгружаются или не прошло 10 минут с первой выгрузки. Оставшееся время: ' . $ttl . ' секунд');
            app(SupplierReportService::class)->error($emailSupplier->supplier);
        } else {
            MarketsEmailSupplierUnload::dispatch($emailSupplier->supplier->user, $emailSupplier);
        }

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
