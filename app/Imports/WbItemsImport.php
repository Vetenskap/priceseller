<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\User;
use App\Models\WbItem;
use App\Models\WbMarket;
use App\Services\ItemsImportReportService;
use App\Services\MarketItemRelationshipService;
use Illuminate\Database\Eloquent\Builder;
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
use PHPUnit\Logging\Exception;

class WbItemsImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    const HEADERS = [
        'Артикул WB (nmID)',
        'Артикул продавца (vendorCode)',
        'Код',
        'Тип (Комплект или Товар)',
        'Баркод (sku)',
        'Комиссия, процент',
        'Мин. цена',
        'Розничная наценка, процент',
        'Упаковка',
        'Объем',
        'Новая цена',
        'Цена из маркета',
        'Остаток',
        'Обновлено',
        'Создано',
        'Удалить'
    ];

    public int $correct = 0;
    public int $error = 0;
    public int $updated = 0;
    public int $deleted = 0;

    public User $user;

    public function __construct(public WbMarket $market)
    {
        $this->user = User::findOrFail($this->market->user_id);
        $this->market->clearSuppliersCache();
    }

    public function model(array $row)
    {
        $row = collect($row);

        if ($row->get('Тип (Комплект или Товар)') === 'Комплект') {
            $item = $this->user->bundles()->where('code', $row->get('Код'))->first();
        } else if ($row->get('Тип (Комплект или Товар)') === 'Товар') {
            $item = $this->user->items()->where('code', $row->get('Код'))->first();
        } else {
            $item = $this->user->bundles()->where('code', $row->get('Код'))->exists()
                ? $this->user->bundles()->where('code', $row->get('Код'))->first()
                : $this->user->items()->where('code', $row->get('Код'))->first();
        }

        if (!$item) {
            MarketItemRelationshipService::handleNotFoundItem(
                externalCode: $row->get('Артикул продавца (vendorCode)'),
                marketId: $this->market->id,
                marketType: 'App\Models\WbMarket',
                code: $row->get('Код')
            );

            $this->error++;

            return null;
        }

        MarketItemRelationshipService::handleFoundItem(
            externalCode: $row->get('Артикул продавца (vendorCode)'),
            code: $row->get('Код'),
            marketId: $this->market->id,
            marketType: 'App\Models\WbMarket',
        );

        if ($wbItem = $this->market->items()->where('vendor_code', $row->get('Артикул продавца (vendorCode)'))->first()) {

            if ($row->get('Удалить') === 'Да') {

                $this->deleted++;
                $wbItem->delete();
                return null;
            }

            $this->updated++;

            $wbItem->nm_id = $row->get('Артикул WB (nmID)');
            $wbItem->vendor_code = $row->get('Артикул продавца (vendorCode)');
            $wbItem->sku = $row->get('Баркод (sku)');
            $wbItem->sales_percent = $row->get('Комиссия, процент');
            $wbItem->min_price = $row->get('Мин. цена');
            $wbItem->retail_markup_percent = $row->get('Розничная наценка, процент');
            $wbItem->package = $row->get('Упаковка');
            $wbItem->volume = $row->get('Объем');
            $wbItem->wbitemable_id = $item->id;
            $wbItem->wbitemable_type = $item->getMorphClass();

            $wbItem->save();

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

        return new WbItem([
            'wb_market_id' => $this->market->id,
            'nm_id' => $row->get('Артикул WB (nmID)'),
            'vendor_code' => $row->get('Артикул продавца (vendorCode)'),
            'sku' => $row->get('Баркод (sku)'),
            'sales_percent' => $row->get('Комиссия, процент'),
            'min_price' => $row->get('Мин. цена'),
            'retail_markup_percent' => $row->get('Розничная наценка, процент'),
            'package' => $row->get('Упаковка'),
            'volume' => $row->get('Объем'),
            'wbitemable_id' => $item->id,
            'wbitemable_type' => $item->getMorphClass(),
            'id' => Str::uuid()
        ]);
    }

    public function prepareForValidation($data, $index)
    {
        if ($index % 1000 === 0) ItemsImportReportService::flush($this->market, $this->correct, $this->error, $this->updated, $this->deleted);

        return $data;
    }

    public function rules(): array
    {
        return [
            'Артикул WB (nmID)' => ['nullable', 'integer'],
            'Артикул продавца (vendorCode)' => ['required'],
            'Баркод (sku)' => ['nullable'],
            'Комиссия, процент' => ['nullable', 'numeric', 'min:0'],
            'Мин. цена' => ['nullable', 'integer', 'min:0'],
            'Розничная наценка, процент' => ['nullable', 'numeric', 'min:0'],
            'Упаковка' => ['nullable', 'numeric', 'min:0'],
            'Объем' => ['nullable', 'numeric'],
            'Код' => ['required'],
            'Тип (Комплект или Товар)' => ['nullable'],
        ];
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
                    externalCode: $values->get('Артикул продавца (vendorCode)'),
                    marketId: $this->market->id,
                    marketType: 'App\Models\WbMarket',
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
