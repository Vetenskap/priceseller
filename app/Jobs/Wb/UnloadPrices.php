<?php

namespace App\Jobs\Wb;

use App\Models\MarketActionReport;
use App\Models\Supplier;
use App\Models\User;
use App\Models\WbMarket;
use App\Services\NotificationService;
use App\Services\WbItemPriceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UnloadPrices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, \Illuminate\Bus\Queueable, SerializesModels;

    public int $uniqueFor = 600;

    public MarketActionReport $report;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user, public WbMarket $market)
    {
        $this->report = $this->market->actionReports()->create([
            'action' => 'Выгрузка цен',
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

            $service = new WbItemPriceService($supplier, $this->market, []);
            $service->updatePrice();
            $service->unloadAllPrices();

        });

        $this->report->update([
            'status' => 0,
            'message' => 'Успех'
        ]);

        NotificationService::send($this->market->user_id, $this->market->name, 'Цены выгружены', 0, null, 'export');
    }

    public function uniqueId(): string
    {
        return $this->market->id . 'unload-prices';
    }

    public function failed(\Throwable $th): void
    {
        $this->report->update([
            'status' => 1,
            'message' => 'Ошибка'
        ]);

        NotificationService::send($this->market->user_id, $this->market->name, 'Ошибка в выгрузке цен', 1, null, 'export');
    }
}