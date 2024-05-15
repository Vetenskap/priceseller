<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\WbItem;
use App\Models\WbMarket;
use App\Services\MarketItemRelationshipService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class WbItemsImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts, WithUpserts
{

    public int $correct = 0;
    public int $error = 0;

    public function __construct(public WbMarket $market)
    {
    }

    public function model(array $row)
    {
        $row = collect($row);

        $item = Item::where('code', $row->get('Код'))->first();

        if (!$item) {

            MarketItemRelationshipService::handleNotFoundItem($row->get('vendorCode'), $this->market->id, $row->get('Код'));
            $this->error++;

            return null;
        };

        $this->correct++;
        MarketItemRelationshipService::handleFoundItem($row->get('vendorCode'), $row->get('Код'), $this->market->id, 'App\Models\WbMarket');

        return new WbItem([
            'wb_market_id' => $this->market->id,
            'nm_id' => $row->get('nmID'),
            'vendor_code' => $row->get('vendorCode'),
            'sku' => $row->get('sku'),
            'sales_percent' => $row->get('Комиссия, процент'),
            'min_price' => $row->get('Мин. цена'),
            'retail_markup_percent' => $row->get('Розничная наценка, процент'),
            'package' => $row->get('Упаковка'),
            'volume' => $row->get('Объем'),
            'item_id' => $item->id
        ]);
    }

    public function uniqueBy()
    {
        return ['vendor_code', 'wb_market_id'];
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
