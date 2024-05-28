<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\User;
use App\Models\WbItem;
use App\Models\WbMarket;
use App\Services\ItemsImportReportService;
use App\Services\MarketItemRelationshipService;
use Illuminate\Database\Eloquent\Builder;
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

class WbItemsImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts, WithValidation, SkipsEmptyRows, SkipsOnFailure
{

    public int $correct = 0;
    public int $error = 0;

    public User $user;

    public function __construct(public WbMarket $market)
    {
        $this->user = User::findOrFail($this->market->user_id);
    }

    public function model(array $row)
    {
        $row = collect($row);

        $item = $this->user->items()->where('code', $row->get('Код'))->first();

        if (!$item) {
            MarketItemRelationshipService::handleNotFoundItem(
                externalCode: $row->get('vendorCode'),
                marketId: $this->market->id,
                marketType: 'App\Models\WbMarket',
                code: $row->get('Код')
            );

            $this->error++;

            return null;
        }

        if (
            WbItem::where(function (Builder $query) use ($row) {
                $query->where('nm_id', $row->get('nmID'))
                    ->orWhere('sku', $row->get('sku'));
            })
                ->whereNot('vendor_code', $row->get('vendorCode'))
                ->exists()
        ) {
            MarketItemRelationshipService::handleItemWithMessage(
                externalCode: $row->get('vendorCode'),
                marketId: $this->market->id,
                marketType: 'App\Models\WbMarket',
                code: $row->get('Код'),
                message: "Уже существует такой nmID или sku"
            );

            $this->error++;

            return null;
        }

        MarketItemRelationshipService::handleFoundItem(
            externalCode: $row->get('vendorCode'),
            code: $row->get('Код'),
            marketId: $this->market->id,
            marketType: 'App\Models\WbMarket',
        );

        $this->correct++;

        if ($wbItem = $this->market->items()->where('vendor_code', $row->get('vendorCode'))->first()) {
            $wbItem->update([
                'nm_id' => $row->get('nmID'),
                'vendor_code' => $row->get('vendorCode'),
                'sku' => $row->get('sku'),
                'sales_percent' => $row->get('Комиссия, процент'),
                'min_price' => $row->get('Мин. цена'),
                'retail_markup_percent' => $row->get('Розничная наценка, процент'),
                'package' => $row->get('Упаковка'),
                'volume' => $row->get('Объем'),
            ]);
            return null;
        }

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

    public function prepareForValidation($data, $index)
    {
        if ($index % 1000 === 0) ItemsImportReportService::flush($this->market, $this->correct, $this->error);

        return $data;
    }

    public function rules(): array
    {
        return [
            'nmID' => ['nullable', 'integer'],
            'vendorCode' => ['required'],
            'sku' => ['nullable', 'integer'],
            'Комиссия, процент' => ['nullable', 'integer', 'min:0'],
            'Мин. цена' => ['nullable', 'integer', 'min:0'],
            'Розничная наценка, процент' => ['nullable', 'numeric', 'min:0'],
            'Упаковка' => ['nullable', 'numeric', 'min:0'],
            'Объем' => ['nullable', 'numeric'],
            'Код' => ['required'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nmID.integer' => 'Поле должно быть целым числом',
            'vendorCode.required' => 'Поле обязательно',
            'sku.integer' => 'Поле должно быть целым числом',
            'Комиссия, процент.integer' => 'Поле должно быть целым числом',
            'Комиссия, процент.min' => 'Поле должно быть больше 0',
            'Мин. цена.integer' => 'Поле должно быть целым числом',
            'Мин. цена.min' => 'Поле должно быть больше 0',
            'Розничная наценка, процент.numeric' => 'Поле должно быть числом',
            'Розничная наценка, процент.min' => 'Поле должно быть больше 0',
            'Упаковка.numeric' => 'Поле должно быть числом',
            'Упаковка.min' => 'Поле должно быть больше 0',
            'Объем.numeric' => 'Поле должно быть числом',
            'Код.required' => 'Поле обязательно',
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
                    externalCode: $values->get('vendorCode'),
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
