<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\OzonItem;
use App\Models\OzonMarket;
use App\Services\MarketItemRelationshipService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class OzonItemsImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts, WithUpserts
{
    public int $correct = 0;
    public int $error = 0;

    public function __construct(public OzonMarket $market)
    {
    }

    public function model(array $row)
    {
        $row = collect($row);

        $item = Item::where('code', $row->get('Код'))->first();

        if (!$item) {
            MarketItemRelationshipService::handleNotFoundItem($row->get('offer_id'), $this->market->id, 'App\Models\OzonMarket', $row->get('Код'));
            $this->error++;
            return null;
        }

        $this->correct++;
        MarketItemRelationshipService::handleFoundItem($row->get('offer_id'), $row->get('Код'), $this->market->id, 'App\Models\OzonMarket');

        return new OzonItem([
            'ozon_market_id' => $this->market->id,
            'product_id' => $row->get('product_id'),
            'offer_id' => $row->get('offer_id'),
            'min_price_percent' => $row->get('Мин. Цена, процент'),
            'min_price' => $row->get('Мин. Цена'),
            'shipping_processing' => $row->get('Обработка отправления'),
            'direct_flow_trans' => $row->get('Магистраль'),
            'deliv_to_customer' => $row->get('Последняя миля'),
            'sales_percent' => (int) $row->get('Комиссия'),
            'item_id' => $item->id
        ]);
    }

    public function uniqueBy()
    {
        return ['offer_id', 'ozon_market_id'];
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
