<?php

namespace App\Jobs\Wb;

use App\Models\Supplier;
use App\Models\User;
use App\Models\WbMarket;
use App\Services\WbItemPriceService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class TestPrice implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user, public $testWarehouses, public WbMarket $market)
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
                $service = new WbItemPriceService($supplier, $this->market, $warehouses);
                $service->updatePrice();
            }
        });
    }

    public function uniqueId(): string
    {
        return $this->market->id . 'test-price';
    }
}
