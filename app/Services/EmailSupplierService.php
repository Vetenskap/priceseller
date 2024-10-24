<?php

namespace App\Services;

use App\Imports\SupplierPriceImport;
use App\Models\EmailSupplier;
use App\Services\Item\ItemPriceService;
use App\Services\Item\ItemPriceWithCacheService;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class EmailSupplierService
{
    protected Collection $stockValues;
    protected int $totalRows = 0;

    public function __construct(protected EmailSupplier $supplier, protected string $path)
    {
        $this->stockValues = $this->supplier->stockValues->pluck('value', 'name');
    }

    public function unload(): void
    {
        $this->nullUpdated();
        $this->nullAllStocks();

        SupplierReportService::changeMessage($this->supplier->supplier, 'Чтение прайса');

        $ext = pathinfo($this->path, PATHINFO_EXTENSION);

        if ($ext === 'xlsx') {

            try {
                $this->xlsxHandle();
            } catch (IOException $e) {

                report($e);

                $this->importHandle();
            }
        } else {

            try {
                $this->importHandle();
            } catch (\TypeError $e) {

                $this->anotherHandle();

            }
        }

        SupplierReportService::changeMessage($this->supplier->supplier, 'Прайс прочитан');
    }

    protected function nullAllStocks(): void
    {
        $this->supplier->supplier->items()->update(['count' => 0]);
    }

    protected function xlsxHandle(): void
    {
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open($this->path);

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $this->processData(collect($row->toArray()));
            }
        }

        $reader->close();
    }

    protected function anotherHandle(): void
    {
        $sheets = Excel::toCollection(new Collection(), $this->path);

        $sheets->each(function (Collection $sheet) {
            $sheet->each(function (Collection $row) {
                $this->processData($row);
            });
        });
    }

    protected function importHandle(): void
    {
        Excel::import(new SupplierPriceImport($this), $this->path);
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

        $itemService = new ItemPriceService($article, $this->supplier->supplier->id);
        $items = $this->supplier->supplier->use_brand ? $itemService->withBrand($brand)->find() : $itemService->find();

        if (count($items) > 0) {

            foreach ($items as $item) {

                EmailPriceItemService::handleFoundItem($this->supplier->supplier->id, $article, $brand, $price, $stock, $item->id);

                $stock = $this->prepareStock($stock);
                $price = $this->preparePrice($price);

                $item->count = $stock;
                $item->price = $price;
                $item->updated = true;

                $itemService->save($item);
            }


        } else {
            EmailPriceItemService::handleNotFoundItem($this->supplier->supplier->id, $article, $brand, $price, $stock);
        }

        $this->totalRows++;

        if ($this->totalRows % 1000 === 0) SupplierReportService::changeMessage($this->supplier->supplier, 'Выгружено: ' . $this->totalRows);
    }

    public function prepareStock(string $stock): int
    {
        $stock = $this->stockValues->get($stock, $stock);
        $stock = (int) preg_replace("/[^0-9]/", "", $stock);

        return $stock;
    }

    public function preparePrice(string $price): float
    {
        $price = (float) preg_replace("/,/", '.', $price);

        return $price;
    }
}
