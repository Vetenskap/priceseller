<?php

namespace App\Livewire\Forms\OzonMarket;

use App\Helpers\Helpers;
use App\Models\OzonMarket;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Livewire\Attributes\Validate;
use Livewire\Form;

class OzonMarketPostForm extends Form
{
    public ?OzonMarket $market = null;

    #[Validate]
    public $name;
    #[Validate]
    public $client_id;
    #[Validate]
    public $api_key;
    #[Validate]
    public $min_price_percent = null;
    #[Validate]
    public $max_price_percent = null;
    #[Validate]
    public $seller_price_percent = null;
    #[Validate]
    public $open = false;
    #[Validate]
    public $max_count = 50;
    #[Validate]
    public $min = 2;
    #[Validate]
    public $max = 5;
    #[Validate]
    public $seller_price = true;
    #[Validate]
    public $acquiring = null;
    #[Validate]
    public $last_mile = null;
    #[Validate]
    public $max_mile = null;
    #[Validate]
    public $organization_id = null;
    #[Validate]
    public $minus_stock = 0;

    #[Validate]
    public $enabled_price = true;

    #[Validate]
    public $enabled_update_commissions_in_time = false;

    #[Validate]
    public $update_commissions_time = '00:00';
    #[Validate]
    public $tariff = 'fbs';

    #[Validate]
    public $enabled_stocks = true;

    #[Validate]
    public $enabled_orders = true;

    #[Validate]
    public $export_ext_item_fields = [];

    #[Validate]
    public $test_warehouses = [];

    #[Validate]
    public $min_price = null;

    #[Validate]
    public $shipping_processing = null;

    #[Validate]
    public $min_price_percent_comm = null;

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                Rule::unique('ozon_markets', 'name')
                    ->where('user_id', Helpers::user()->id)
                    ->when($this->market, fn(Unique $unique) => $unique->ignore($this->market->id, 'id'))
            ],
            'client_id' => ['required', 'integer'],
            'api_key' => ['required', 'string'],
            'min_price_percent' => ['nullable', 'integer'],
            'max_price_percent' => ['nullable', 'integer'],
            'seller_price_percent' => ['nullable', 'integer'],
            'max_count' => ['nullable', 'integer'],
            'min' => ['nullable', 'integer'],
            'max' => ['nullable', 'integer'],
            'seller_price' => ['nullable', 'boolean'],
            'acquiring' => ['nullable', 'numeric'],
            'last_mile' => ['nullable', 'numeric'],
            'max_mile' => ['nullable', 'integer'],
            'organization_id' => ['nullable', 'uuid', 'exists:organizations,id'],
            'minus_stock' => ['nullable', 'integer', 'min:0'],
            'enabled_price' => ['nullable', 'boolean'],
            'enabled_update_commissions_in_time' => ['nullable', 'boolean'],
            'update_commissions_time' => ['nullable', 'string', 'date_format:H:i'],
            'tariff' => ['nullable', 'in:fbs,fbo'],
            'enabled_stocks' => ['nullable', 'boolean'],
            'enabled_orders' => ['nullable', 'boolean'],
            'export_ext_item_fields' => ['nullable', 'array'],
            'test_warehouses' => ['nullable', 'array'],
            'min_price' => ['nullable', 'integer'],
            'shipping_processing' => ['nullable', 'integer'],
            'min_price_percent_comm' => ['nullable', 'integer', 'max:100'],
        ];
    }

    public function setMarket(OzonMarket $market): void
    {
        $this->market = $market;
        $this->name = $this->market->name;
        $this->client_id = $this->market->client_id;
        $this->api_key = $this->market->api_key;
        $this->min_price_percent = $this->market->min_price_percent;
        $this->max_price_percent = $this->market->max_price_percent;
        $this->seller_price_percent = $this->market->seller_price_percent;
        $this->open = $this->market->open;
        $this->max_count = $this->market->max_count;
        $this->min = $this->market->min;
        $this->max = $this->market->max;
        $this->seller_price = $this->market->seller_price;
        $this->acquiring = $this->market->acquiring;
        $this->last_mile = $this->market->last_mile;
        $this->max_mile = $this->market->max_mile;
        $this->organization_id = $market->organization_id;
        $this->minus_stock = $market->minus_stock;
        $this->enabled_price = $market->enabled_price;
        $this->enabled_update_commissions_in_time = $market->enabled_update_commissions_in_time;
        $this->update_commissions_time = $market->update_commissions_time;
        $this->tariff = $market->tariff;
        $this->enabled_stocks = $market->enabled_stocks;
        $this->enabled_orders = $market->enabled_orders;
        $this->export_ext_item_fields = $market->export_ext_item_fields;
        $this->test_warehouses = $market->test_warehouses;
        $this->min_price = $market->min_price;
        $this->shipping_processing = $market->shipping_processing;
        $this->min_price_percent_comm = $market->min_price_percent_comm;
    }

    public function store(): void
    {
        $this->validate();

        Helpers::user()->ozonMarkets()->create($this->except('market'));

        $this->reset();

    }

    public function update(): void
    {
        logger('валидация');
        $this->validate();

        $this->market->update($this->except('market'));
    }

    public function destroy(): void
    {
        $this->market->delete();
    }
}
