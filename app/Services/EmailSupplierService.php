<?php

namespace App\Services;

use App\Contracts\MarketContract;
use App\Contracts\ReportContract;
use App\Contracts\SupplierUnloadContract;
use App\Helpers\Helpers;
use App\Imports\SupplierPriceImport;
use App\Jobs\Supplier\ProcessData;
use App\Models\EmailSupplier;
use App\Models\EmailSupplierWarehouse;
use App\Models\Item;
use App\Models\Report;
use App\Models\User;
use App\Services\Item\ItemPriceService;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Reader\XLSX\Sheet;
use Illuminate\Bus\Batch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Moysklad\Models\Moysklad;

class EmailSupplierService implements SupplierUnloadContract
{
    public Collection $stockValues;
    public Collection $warehouses;
    public ReportContract $reportContract;
    public EmailSupplier $supplier;
    public string $path;
    public Report $report;
    public bool $enabled_diff_price = false;
    public User $user;
    public ?Moysklad $moysklad = null;

    public function make(EmailSupplier $supplier, string $path, Report $report): void
    {
        $this->supplier = $supplier;
        $this->path = $path;
        $this->report = $report;
        $this->stockValues = $this->supplier->stockValues->pluck('value', 'name');
        $this->warehouses = $this->supplier->warehouses->pluck('supplier_warehouse_id', 'value');
        $this->reportContract = app(ReportContract::class);
        $this->user = $this->supplier->supplier->user;
        $this->moysklad = $this->user->moysklad;
        $this->enabled_diff_price = ModuleService::moduleIsEnabled('Moysklad', $this->user) && $this->moysklad->enabled_diff_price;
    }

    public function unload(): void
    {
        $this->nullUpdated();
        $this->nullAllStocks();

        $ext = pathinfo($this->path, PATHINFO_EXTENSION);

        switch ($ext) {
            case 'xlsx':
                $this->reportContract->addLog($this->report, 'Читаем xlsx прайс');
                try {
                    $this->xlsxHandle();
                } catch (IOException $e) {

                    $this->reportContract->addLog($this->report, 'Не удалось прочитать прайс в формате "xlsx", пытаемся перевести в формат "ods"');

                    Log::warning($e);

                    $pathInfo = pathinfo($this->path);
                    $directory = $pathInfo['dirname'];

                    $command = "/usr/bin/soffice --convert-to ods {$this->path} --headless --outdir {$directory}";

                    $process = Process::timeout(600)->run($command);

                    if (!$process->successful()) {
                        $this->reportContract->addLog($this->report, 'Не удалось перевести прайс в формат "ods", ошибка: ' . $process->errorOutput());
                        throw new \Exception($process->errorOutput());
                    }

                    $this->path = str_replace('xlsx', 'ods', $this->path);

                    $this->reportContract->addLog($this->report, 'Перевели прайс в формат "ods", читаем..');

                    $this->odsHandle();
                }
                break;
            default:
                $this->reportContract->addLog($this->report, 'Читаем прайс другого формата');
                try {
                    $this->importHandle();
                } catch (\TypeError $e) {

                    $this->reportContract->addLog($this->report, 'Не удалось прочитать прайс данным методом, пробуем другой..');

                    Log::warning($e);

                    $this->anotherHandle();

                }
                break;
        }

        $this->reportContract->addLog($this->report, 'Прайс прочитан');
        $marketContract = app(MarketContract::class);
        $this->reportContract->addLog($this->report, 'Выгружаем новые данные в кабинеты..');
        $marketContract->unload($this->supplier, $this->report);
    }

    protected function xlsxHandle(): void
    {
        Helpers::toBatch(function (Batch $batch) {
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->open($this->path);

            /** @var Sheet $sheet */
            foreach ($reader->getSheetIterator() as $sheet) {

                $rows = collect();

                /** @var Row $row */
                foreach ($sheet->getRowIterator() as $row) {

                    $rows->add(collect($row->toArray()));

                    if ($rows->count() >= 10000) {
                        $batch->add(new ProcessData($this, $rows));
                        $rows = collect();
                    }
                }

                if ($rows->isNotEmpty()) {
                    $batch->add(new ProcessData($this, $rows));
                }
            }

            $reader->close();
        }, 'supplier-unload', function (): bool {
            $this->report = $this->report->fresh();
            return $this->report->isCancelled();
        });
    }

    protected function odsHandle(): void
    {
        Helpers::toBatch(function (Batch $batch) {
            $reader = ReaderEntityFactory::createODSReader();
            $reader->open($this->path);

            /** @var Sheet $sheet */
            foreach ($reader->getSheetIterator() as $sheet) {

                $rows = collect();

                /** @var Row $row */
                foreach ($sheet->getRowIterator() as $row) {

                    $rows->add(collect($row->toArray()));

                    if ($rows->count() >= 10000) {
                        $batch->add(new ProcessData($this, $rows));
                        $rows = collect();
                    }
                }

                if ($rows->isNotEmpty()) {
                    $batch->add(new ProcessData($this, $rows));
                }
            }

            $reader->close();
        }, 'supplier-unload', function (): bool {
            $this->report = $this->report->fresh();
            return $this->report->isCancelled();
        });
    }

    protected function anotherHandle(): void
    {
        Helpers::toBatch(function (Batch $batch) {

            $sheets = Excel::toCollection(new Collection(), $this->path);

            $sheets->each(function (Collection $sheet) use ($batch) {

                $sheet->chunk(10000)->each(function (Collection $rows) use ($batch) {
                    $batch->add(new ProcessData($this, $rows));
                });

            });
        }, 'supplier-unload', function (): bool {
            $this->report = $this->report->fresh();
            return $this->report->isCancelled();
        });
    }

    protected function importHandle(): void
    {
        Helpers::toBatch(function (Batch $batch) {
            Excel::import(new SupplierPriceImport($this, $batch), $this->path);
        }, 'supplier-unload', function (): bool {
            $this->report = $this->report->fresh();
            return $this->report->isCancelled();
        });
    }

    public function nullAllStocks(): void
    {
        $this->reportContract->addLog($this->report, 'Обнуляем все остатки поставщика');
        $this->supplier->warehouses->each(function (EmailSupplierWarehouse $warehouse) {
            $warehouse->supplierWarehouse->stocks()->update(['stock' => 0]);
        });
        $this->reportContract->addLog($this->report, 'Обнулили все остатки поставщика');
    }

    public function nullUpdated(): void
    {
        $this->reportContract->addLog($this->report, 'Переводим все товары поставщика в статус "Не обновлён"');
        $this->supplier->supplier->items()->update(['updated' => false]);
        $this->reportContract->addLog($this->report, 'Перевели все товары в статус "Не обновлён"');
    }

    public function processData(Collection $row): void
    {
        $article = $row->get($this->supplier->header_article - 1);
        $brand = $row->get($this->supplier->header_brand - 1);
        $price = $row->get($this->supplier->header_price - 1);
        $stock = $row->get($this->supplier->header_count - 1);
        $warehouse = $row->get($this->supplier->header_warehouse - 1);

        if (is_null($article)) {
            $this->handleNotFound($article, $brand, $price, $stock);
            return;
        }

        $items = $this->findItems($article, $brand);

        if ($items->isEmpty()) {
            $this->handleNotFound($article, $brand, $price, $stock);
            return;
        } else {
            foreach ($items as $item) {
                $this->handleFound($article, $brand, $price, $stock, $item->id);
            }
        }

        foreach ($items as $item) {
            $this->updateItem($item, $price, $stock, $warehouse);
        }
    }

    public function findItems(?string $article, ?string $brand): Collection
    {
        $itemService = new ItemPriceService($article, $this->supplier->supplier->id);

        return $this->supplier->supplier->use_brand
            ? $itemService->withBrand($brand)->find()
            : $itemService->find();
    }

    public function handleNotFound(?string $article, ?string $brand, ?string $price, ?string $stock): void
    {
        EmailPriceItemService::handleNotFoundItem(
            $this->supplier->supplier->id,
            $article,
            $brand,
            $price,
            $stock
        );
    }

    public function handleFound(?string $article, ?string $brand, ?string $price, ?string $stock, string $itemId): void
    {
        EmailPriceItemService::handleFoundItem(
            $this->supplier->supplier->id,
            $article,
            $brand,
            $price,
            $stock,
            $itemId
        );
    }

    public function updateItem(
        Item    $item,
        ?string $price,
        ?string $stock,
        ?string $warehouse
    ): void
    {
        $this->updatePrice($item, $price);
        $this->updateStock($item, $stock, $warehouse);
        $item->updated = true;

        $item->save();
    }

    public function updatePrice(Item $item, ?string $price): void
    {
        if (!is_numeric($price) || $price <= 0) {
            return;
        }

        $price = $this->preparePrice($price);
        $item->price = $price;

        if ($this->enabled_diff_price) {
            $this->handlePriceQuarantine($item, $price);
        }
    }

    public function handlePriceQuarantine(Item $item, float $price): void
    {
        $diffPrice = $this->moysklad->diff_price;

        if (($price + ($price / 100 * $diffPrice)) < $item->buy_price_reserve ||
            ($price - ($price / 100 * $diffPrice)) > $item->buy_price_reserve) {
        $this->moysklad->quarantine()->updateOrCreate(
            ['item_id' => $item->id],
            ['item_id' => $item->id, 'supplier_buy_price' => $price]
        );
    }
}

    public function updateStock(Item $item, ?string $stock, ?string $warehouse): void
    {
        if (is_null($stock) || $stock < 0) {
            return;
        }

        $stock = $this->prepareStock($stock);

        $supplierWarehouseId = $this->resolveWarehouseId($warehouse);

        if ($supplierWarehouseId) {
            $item->supplierWarehouseStocks()->updateOrCreate(
                ['supplier_warehouse_id' => $supplierWarehouseId, 'item_id' => $item->id],
                ['supplier_warehouse_id' => $supplierWarehouseId, 'stock' => $stock]
            );
        }
    }

    public function resolveWarehouseId(?string $warehouse): ?string
    {
        return $this->supplier->header_warehouse
            ? $this->warehouses->get($warehouse)
            : $this->warehouses->first();
    }

    public function prepareStock(string $stock): int
    {
        $stock = $this->stockValues->get($stock, $stock);
        return (int)preg_replace("/[^0-9]/", "", $stock);
    }

    public function preparePrice(string $price): float
    {
        return (float)preg_replace("/,/", '.', $price);
    }
}
