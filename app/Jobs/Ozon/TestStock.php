<?php

namespace App\Jobs\Ozon;

use App\Models\OzonMarket;
use App\Models\Supplier;
use App\Models\User;
use App\Services\OzonItemPriceService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TestStock implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 600;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user, public array $testWarehouses, public OzonMarket $market)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->user->suppliers->each(function (Supplier $supplier) {

            $warehouses = collect($this->testWarehouses[$supplier->getKey()] ?? [])->filter(fn ($value, $key) => $value)->keys()->toArray();

            if ($warehouses) {
                $service = new OzonItemPriceService($supplier, $this->market, $warehouses);
                $service->updateStock();
            }

        });
    }

    public function uniqueId(): string
    {
        return $this->market->id . 'test-stock';
    }
}
