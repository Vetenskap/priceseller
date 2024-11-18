<?php

namespace Modules\Moysklad\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladItemApiReport;
use Modules\Moysklad\Services\MoyskladService;

class MoyskladItemsApiImport implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 600;

    public MoyskladItemApiReport $report;
    /**
     * Create a new job instance.
     */
    public function __construct(public Moysklad $moysklad)
    {
        $this->report = $moysklad->apiItemsReports()->create([
            'status' => 2,
            'message' => 'Идёт выгрузка товаров',
            'updated' => 0,
            'created' => 0,
            'errors' => 0
        ]);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = new MoyskladService($this->moysklad);
        $service->importApiItems($this->report);

        $this->report->update([
            'status' => 0,
            'message' => 'Все товары успешно выгружены'
        ]);
    }

    public function failed(\Throwable $th): void
    {
        $this->report->update([
            'status' => 1,
            'message' => 'Ошибка в выгрузке товаров'
        ]);
    }

    public function uniqueId(): string
    {
        return $this->moysklad->id . 'items_api_import';
    }

    public static function getUniqueId(Moysklad $moysklad): string
    {
        return $moysklad->id . 'items_api_import';
    }
}
