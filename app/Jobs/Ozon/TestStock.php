<?php

namespace App\Jobs\Ozon;

use App\Models\MarketActionReport;
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

class TestStock implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 600;

    public MarketActionReport $report;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user, public array $testWarehouses, public OzonMarket $market)
    {
        $this->report = $this->market->actionReports()->create([
            'action' => 'Тест остатков',
            'status' => 2,
            'message' => 'В процессе'
        ]);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->user->suppliers->each(function (Supplier $supplier) {

            $warehouses = collect($this->testWarehouses[$supplier->getKey()] ?? [])->filter(fn($value, $key) => $value)->keys();

            if ($warehouses->isNotEmpty()) {
                $warehouses = $warehouses->search('userWarehouses') !== false ? [] : $warehouses->toArray();

                $service = new OzonItemPriceService($supplier, $this->market, $warehouses);
                $service->updateStock();
            }

        });

        $this->report->update([
            'status' => 0,
            'message' => 'Успех'
        ]);
    }

    public function uniqueId(): string
    {
        return $this->market->id . 'test-stock';
    }

    public function failed(\Throwable $th): void
    {
        $this->report->update([
            'status' => 1,
            'message' => 'Ошибка'
        ]);
    }
}
