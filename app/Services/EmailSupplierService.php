<?php

namespace App\Services;

use App\Imports\SupplierPriceImport;
use App\Models\EmailSupplier;
use App\Services\Item\ItemPriceService;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class EmailSupplierService
{
    protected Collection $stockValues;

    public function __construct(protected EmailSupplier $supplier, protected string $path)
    {
        $this->stockValues = $this->supplier->stockValues->pluck('value', 'name');
    }

    public function unload()
    {
        $this->nullUpdated();

        SupplierReportService::changeMessage($this->supplier->supplier, 'Чтение прайса');

        $ext = pathinfo($this->path, PATHINFO_EXTENSION);

        if ($ext === 'xlsx') {
            $this->xlsxHandle();
        } else {
            $this->anotherHandle();
        }
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
        $price = (float) $row->get($this->supplier->header_price - 1);
        $stock = $row->get($this->supplier->header_count - 1);

        $itemService = new ItemPriceService($article, $this->supplier->supplier->id);
        $item = $this->supplier->supplier->use_brand ? $itemService->withBrand($brand)->find() : $itemService->find();

        if ($item) {

            EmailPriceItemService::handleFoundItem($this->supplier->supplier->id, $article, $brand, $price, $stock, $item->id);

            $stock = $this->prepareStock($stock);

            if ($item->count !== $stock || $item->price !== $price) {
                $item->count = $stock;
                $item->price = $price;

                $itemService->save($item);
            }

        } else {
            EmailPriceItemService::handleNotFoundItem($this->supplier->supplier->id, $article, $brand, $price, $stock);
        }
    }

    public function prepareStock(string $stock): int
    {
        $stock = $this->stockValues->get($stock, $stock);
        $stock = (int) preg_replace("/[^0-9]/", "", $stock);

        return $stock;
    }
}
