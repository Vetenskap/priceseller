<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\OzonItem;
use App\Models\OzonMarket;
use App\Models\User;
use App\Services\ItemsImportReportService;
use App\Services\MarketItemRelationshipService;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class OzonItemsImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts, WithValidation, SkipsEmptyRows, SkipsOnFailure, SkipsOnError
{
    CONST HEADERS = [
        'product_id',
        'Артикул ozon (offer_id)',
        'Код',
        'Тип (Комплект или Товар)',
        'Мин. Цена, процент',
        'Мин. Цена',
        'Обработка отправления',
        'Магистраль',
        'Последняя миля',
        'Комиссия',
        'Цена продажи',
        'Цена конкурента',
        'Минимальная цена',
        'Цена до скидки, процент',
        'Цена из маркета',
        'Остаток',
        'Закупочная цена',
        'Закупочная цена резерв',
        'Кратность отгрузки',
        'Обновлено',
        'Загружено',
        'Удалить'
    ];

    public int $correct = 0;
    public int $error = 0;
    public int $updated = 0;
    public int $deleted = 0;

    public User $user;

    public function __construct(public OzonMarket $market)
    {
        $this->user = User::findOrFail($this->market->user_id);
    }

    public function model(array $row)
    {
        $row = collect($row);

        $item = $row->get('Тип (Комплект или Товар)') === 'Комплект'
            ? $this->user->bundles()->where('code', $row->get('Код'))->first()
            : $this->user->items()->where('code', $row->get('Код'))->first();

        if (!$item) {

            MarketItemRelationshipService::handleNotFoundItem(
                externalCode: $row->get('Артикул ozon (offer_id)'),
                marketId: $this->market->id,
                marketType: 'App\Models\OzonMarket',
                code: $row->get('Код')
            );

            $this->error++;

            return null;
        }

        MarketItemRelationshipService::handleFoundItem(
            externalCode: $row->get('Артикул ozon (offer_id)'),
            code: $row->get('Код'),
            marketId: $this->market->id,
            marketType: 'App\Models\OzonMarket'
        );

        if ($ozonItem = $this->market->items()->where('offer_id', $row->get('Артикул ozon (offer_id)'))->first()) {

            if ($row->get('Удалить') === 'Да') {

                $this->deleted++;
                $ozonItem->delete();
                return null;
            }

            $this->updated++;

            $ozonItem->update([
                'product_id' => $row->get('product_id'),
                'offer_id' => $row->get('Артикул ozon (offer_id)'),
                'min_price_percent' => $row->get('Мин. Цена, процент'),
                'min_price' => $row->get('Мин. Цена'),
                'shipping_processing' => $row->get('Обработка отправления'),
                'direct_flow_trans' => $row->get('Магистраль'),
                'deliv_to_customer' => $row->get('Последняя миля'),
                'sales_percent' => (int) $row->get('Комиссия'),
            ]);
            return null;
        }

        if ($row->get('Удалить') === 'Да') {

            $this->error++;

            ItemsImportReportService::addBadItem(
                $this->market,
                0,
                'Удалить',
                ['Не удалось создать товар, стоит метка "Удалить"'],
                $row->all()
            );

            return null;
        }

        $this->correct++;

        return new OzonItem([
            'ozon_market_id' => $this->market->id,
            'product_id' => $row->get('product_id'),
            'offer_id' => $row->get('Артикул ozon (offer_id)'),
            'min_price_percent' => $row->get('Мин. Цена, процент'),
            'min_price' => $row->get('Мин. Цена'),
            'shipping_processing' => $row->get('Обработка отправления'),
            'direct_flow_trans' => $row->get('Магистраль'),
            'deliv_to_customer' => $row->get('Последняя миля'),
            'sales_percent' => (int) $row->get('Комиссия'),
            'ozonitemable_id' => $item->id,
            'ozonitemable_type' => $item->getMorphClass(),
            'id' => Str::uuid()
        ]);
    }

    public function prepareForValidation($data, $index)
    {
        if ($index % 1000 === 0) ItemsImportReportService::flush($this->market, $this->correct, $this->error, $this->updated);

        return $data;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['nullable'],
            'Артикул ozon (offer_id)' => ['required'],
            'Мин. Цена, процент' => ['nullable', 'integer', 'min:0'],
            'Мин. Цена' => ['nullable', 'integer', 'min:0'],
            'Обработка отправления' => ['nullable', 'numeric', 'min:0'],
            'Магистраль' => ['nullable', 'numeric', 'min:0'],
            'Последняя миля' => ['nullable', 'numeric', 'min:0'],
            'Комиссия' => ['nullable', 'integer', 'min:0'],
            'Код' => ['required'],
            'Тип (Комплект или Товар)' => ['nullable'],
        ];
    }

    public function onError(\Throwable $e)
    {
        $this->error++;

        logger('Товар не создан', [
            'message' => $e->getMessage()
        ]);
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {

            $this->error++;

            ItemsImportReportService::addBadItem(
                $this->market,
                $failure->row(),
                $failure->attribute(),
                $failure->errors(),
                $failure->values()
            );

            $values = collect($failure->values());

            if ($failure->attribute() == 'Код') {
                MarketItemRelationshipService::handleNotFoundItem(
                    externalCode: $values->get('Артикул ozon (offer_id)'),
                    marketId: $this->market->id,
                    marketType: 'App\Models\OzonMarket',
                    code: $values->get('Код')
                );
            }

        }
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
