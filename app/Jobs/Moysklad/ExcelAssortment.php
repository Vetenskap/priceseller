<?php

namespace App\Jobs\Moysklad;

use App\Models\ItemsMoyskladImportReport;
use App\Models\Moysklad;
use App\Services\ItemsMoyskladImportReportService;
use App\Services\MoyskladService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ExcelAssortment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $uuid, public string $ext, public Moysklad $moysklad, public Collection $attributes)
    {
        if (!ItemsMoyskladImportReportService::new($this->moysklad, $this->uuid)) {
            throw new \Exception("Уже идёт импорт");
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = new MoyskladService($this->moysklad);
        $service->setClient();
        $result = $service->importItemsExcel($this->uuid, $this->ext, $this->attributes);

        ItemsMoyskladImportReportService::success(
            moysklad: $this->moysklad,
            correct: $result->get('correct', 0),
            error: $result->get('error', 0),
            updated: $result->get('updated', 0),
        );
    }

    public function failed()
    {
        ItemsMoyskladImportReportService::error($this->moysklad);
    }
}
