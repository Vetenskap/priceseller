<?php

namespace App\Services;

use App\Contracts\ReportContract;
use App\Helpers\Helpers;
use App\Imports\SupplierPriceImport;
use App\Jobs\Supplier\ProcessData;
use App\Models\EmailSupplier;
use App\Models\EmailSupplierWarehouse;
use App\Models\Item;
use App\Models\OzonMarket;
use App\Models\Report;
use App\Models\WbMarket;
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

class EmailSupplierService
{
    public Collection $stockValues;
    public Collection $warehouses;
    public ReportContract $reportContract;

    public function __construct(public EmailSupplier $supplier, public string $path, public Report $report)
    {
        $this->stockValues = $this->supplier->stockValues->pluck('value', 'name');
        $this->warehouses = $this->supplier->warehouses->pluck('supplier_warehouse_id', 'value');
        $this->reportContract = app(ReportContract::class);
    }

    public function unload(): void
    {
        $this->reportContract->addLog($this->report, 'Обнуление остатков');

        $this->nullUpdated();
        $this->nullAllStocks();

        $this->reportContract->addLog($this->report, 'Чтение прайса');

        $ext = pathinfo($this->path, PATHINFO_EXTENSION);

        if ($ext === 'xlsx') {

            try {
                $this->xlsxHandle();
            } catch (IOException $e) {

                Log::warning($e);

                $pathInfo = pathinfo($this->path);
                $directory = $pathInfo['dirname'];

                $command = "/usr/bin/soffice --convert-to ods {$this->path} --headless --outdir {$directory}";

                $process = Process::timeout(600)->run($command);

                if (!$process->successful()) {
                    throw new \Exception($process->errorOutput());
                }

                $this->path = str_replace('xlsx', 'ods', $this->path);

                $this->odsHandle();
            }
        } else {

            try {
                $this->importHandle();
            } catch (\TypeError $e) {

                Log::warning($e);

                $this->anotherHandle();

            }
        }

        $this->reportContract->addLog($this->report, 'Прайс прочитан');
        $this->marketsUnload();
    }

    protected function xlsxHandle(): void
    {
        app(Helpers::class)->toBatch(function (Batch $batch) {
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
        app(Helpers::class)->toBatch(function (Batch $batch) {
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
        app(Helpers::class)->toBatch(function (Batch $batch) {
            Excel::import(new SupplierPriceImport($this, $batch), $this->path);
        }, 'supplier-unload', function (): bool {
            $this->report = $this->report->fresh();
            return $this->report->isCancelled();
        });
    }

    public function nullAllStocks(): void
    {
        $this->supplier->warehouses->each(function (EmailSupplierWarehouse $warehouse) {
            $warehouse->supplierWarehouse->stocks()->update(['stock' => 0]);
        });
    }

    public function nullUpdated(): void
    {
        $this->supplier->supplier->items()->update(['updated' => false]);
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
            $this->updateItem($item, $article, $brand, $price, $stock, $warehouse);
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
        ?string $article,
        ?string $brand,
        ?string $price,
        ?string $stock,
        ?string $warehouse
    ): void
    {
        $this->updatePrice($item, $price);
        $this->updateStock($item, $stock, $warehouse);
        $item->updated = true;

        $itemService = new ItemPriceService($article, $this->supplier->supplier->id);
        $itemService->save($item);
    }

    public function updatePrice(Item $item, ?string $price): void
    {
        if (!is_numeric($price) || $price <= 0) {
            return;
        }

        $price = $this->preparePrice($price);
        $item->price = $price;

        $user = $this->supplier->supplier->user;
        $moysklad = $user->moysklad;

        if (ModuleService::moduleIsEnabled('Moysklad', $user) && $moysklad->enabled_diff_price) {
            $this->handlePriceQuarantine($item, $price, $moysklad);
        }
    }

    public function handlePriceQuarantine(Item $item, float $price, $moysklad): void
    {
        $diffPrice = $moysklad->diff_price;

        if (($price + ($price / 100 * $diffPrice)) < $item->buy_price_reserve ||
            ($price - ($price / 100 * $diffPrice)) > $item->buy_price_reserve) {
        $moysklad->quarantine()->updateOrCreate(
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

    public function marketsUnload(): void
    {
        Helpers::toBatch(function (Batch $batch) {

            $this->supplier->supplier->user->ozonMarkets()
                ->where('open', true)
                ->where('close', false)
                ->get()
                ->filter(fn(OzonMarket $market) => $market->suppliers()->where('id', $this->supplier->supplier->id)->first())
                ->each(function (OzonMarket $market) use ($batch) {
                    $batch->add(new \App\Jobs\Ozon\PriceUnload($market, $this->supplier));
                });

            $this->supplier->supplier->user->wbMarkets()
                ->where('open', true)
                ->where('close', false)
                ->get()
                ->filter(fn(WbMarket $market) => $market->suppliers()->where('id', $this->supplier->supplier->id)->first())
                ->each(function (WbMarket $market) use ($batch) {
                    $batch->add(new \App\Jobs\Wb\PriceUnload($market, $this->supplier));
                });
        }, 'market-unload', function (): bool {
            $this->report = $this->report->fresh();
            return $this->report->isCancelled();
        });
    }
}
