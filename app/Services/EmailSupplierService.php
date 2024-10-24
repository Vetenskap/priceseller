<?php

namespace App\Services;

use App\Helpers\Helpers;
use App\Imports\SupplierPriceImport;
use App\Jobs\Supplier\ProcessData;
use App\Models\EmailSupplier;
use App\Models\EmailSupplierWarehouse;
use App\Models\Item;
use App\Services\Item\ItemPriceService;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Reader\XLSX\Sheet;
use Illuminate\Bus\Batch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class EmailSupplierService
{
    protected Collection $stockValues;
    protected Collection $warehouses;
    public int $limitMemory = 10485760000;

    public function __construct(protected EmailSupplier $supplier, protected string $path)
    {
        $this->stockValues = $this->supplier->stockValues->pluck('value', 'name');
        $this->warehouses = $this->supplier->warehouses->pluck('supplier_warehouse_id', 'value');
    }

    public function unload(): void
    {
        SupplierReportService::changeMessage($this->supplier->supplier, 'Обнуление остатков');

        $this->nullUpdated();
        $this->nullAllStocks();

        SupplierReportService::changeMessage($this->supplier->supplier, 'Чтение прайса');

        $ext = pathinfo($this->path, PATHINFO_EXTENSION);

        if ($ext === 'xlsx') {

            try {
                $this->xlsxHandle();
            } catch (IOException $e) {

                report($e);

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

                report($e);

                $this->anotherHandle();

            }
        }

        SupplierReportService::changeMessage($this->supplier->supplier, 'Прайс прочитан');
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
        }, 'email-supplier-unload');
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
        }, 'email-supplier-unload');
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
        }, 'email-supplier-unload');
    }

    protected function importHandle(): void
    {
        Helpers::toBatch(function (Batch $batch) {
            Excel::import(new SupplierPriceImport($this, $batch), $this->path);
        }, 'email-supplier-unload');
    }

    protected function nullAllStocks(): void
    {
        $this->supplier->warehouses->each(function (EmailSupplierWarehouse $warehouse) {
            $warehouse->supplierWarehouse->stocks()->update(['stock' => 0]);
        });
    }

    protected function nullUpdated(): void
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

        $itemService = new ItemPriceService($article, $this->supplier->supplier->id);
        $items = $this->supplier->supplier->use_brand ? $itemService->withBrand($brand)->find() : $itemService->find();

        if (count($items) > 0 && !is_null($article)) {

            /** @var Item $item */
            foreach ($items as $item) {

                EmailPriceItemService::handleFoundItem($this->supplier->supplier->id, $article, $brand, $price, $stock, $item->id);

                if (is_numeric($price) && $price > 0) {
                    $price = $this->preparePrice($price);
                    $item->price = $price;

                    $user = $this->supplier->supplier->user;
                    $moysklad = $user->moysklad;
                    if (ModuleService::moduleIsEnabled('Moysklad', $user) && $moysklad->enabled_diff_price) {
                        if (($price + ($price / 100 * $moysklad->diff_price)) < $item->buy_price_reserve || ($price - ($price / 100 * $moysklad->diff_price)) > $item->buy_price_reserve) {
                            $moysklad->quarantine()->updateOrCreate([
                                'item_id' => $item->id
                            ], [
                                'item_id' => $item->id,
                                'supplier_buy_price' => $price
                            ]);
                        }
                    }

                }

                $item->updated = true;

                $itemService->save($item);

                if (!is_null($stock) && $stock >= 0) {

                    $stock = $this->prepareStock($stock);

                    if (!is_null($this->supplier->header_warehouse)) {

                        if ($supplier_warehouse_id = $this->warehouses->get($warehouse)) {

                            $item->supplierWarehouseStocks()->updateOrCreate([
                                'supplier_warehouse_id' => $supplier_warehouse_id,
                                'item_id' => $item->id
                            ], [
                                'supplier_warehouse_id' => $supplier_warehouse_id,
                                'stock' => $stock
                            ]);

                        }

                    } else {

                        $supplier_warehouse_id = $this->warehouses->first();

                        $item->supplierWarehouseStocks()->updateOrCreate([
                            'supplier_warehouse_id' => $supplier_warehouse_id,
                            'item_id' => $item->id
                        ], [
                            'supplier_warehouse_id' => $supplier_warehouse_id,
                            'stock' => $stock
                        ]);

                    }

                }
            }


        } else {
            EmailPriceItemService::handleNotFoundItem($this->supplier->supplier->id, $article, $brand, $price, $stock);
        }
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
