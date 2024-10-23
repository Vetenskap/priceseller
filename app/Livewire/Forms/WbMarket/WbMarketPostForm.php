<?php

namespace App\Livewire\Forms\WbMarket;

use App\Helpers\Helpers;
use App\Models\WbMarket;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Livewire\Attributes\Validate;
use Livewire\Form;

class WbMarketPostForm extends Form
{
    public ?WbMarket $market = null;

    #[Validate]
    public $name;

    #[Validate]
    public $api_key;

    #[Validate]
    public $open = false;

    #[Validate]
    public $max_count = 50;

    #[Validate]
    public $min = 2;

    #[Validate]
    public $max = 5;

    #[Validate]
    public $volume = null;

    #[Validate]
    public $coefficient = 1.0;

    #[Validate]
    public $basic_logistics = null;

    #[Validate]
    public $price_one_liter = null;

    #[Validate]
    public $organization_id = null;

    #[Validate]
    public $minus_stock = 0;

    #[Validate]
    public $enabled_update_commissions_in_time = false;

    #[Validate]
    public $update_commissions_time = '00:00';

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                Rule::unique('wb_markets', 'name')
                    ->where('user_id', Helpers::user()->id)
                    ->when($this->market, fn(Unique $unique) => $unique->ignore($this->market->id, 'id'))
            ],
            'api_key' => ['required', 'string'],
            'open' => ['nullable', 'boolean'],
            'max_count' => ['nullable', 'integer', 'min:1'],
            'min' => ['nullable', 'integer'],
            'max' => ['nullable', 'integer'],
            'volume' => ['nullable', 'integer'],
            'coefficient' => ['nullable', 'numeric'],
            'basic_logistics' => ['nullable', 'integer'],
            'price_one_liter' => ['nullable', 'numeric'],
            'organization_id' => ['nullable', 'uuid', 'exists:organizations,id'],
            'minus_stock' => ['nullable', 'integer', 'min:0'],
            'enabled_update_commissions_in_time' => ['nullable', 'boolean'],
            'update_commissions_time' => ['nullable', 'string', 'date_format:H:i'],
        ];
    }

    public function setMarket(WbMarket $market): void
    {
        $this->market = $market;
        $this->name = $this->market->name;
        $this->api_key = $this->market->api_key;
        $this->open = $this->market->open;
        $this->max_count = $this->market->max_count;
        $this->min = $this->market->min;
        $this->max = $this->market->max;
        $this->volume = $this->market->volume;
        $this->coefficient = $this->market->coefficient;
        $this->basic_logistics = $this->market->basic_logistics;
        $this->price_one_liter = $this->market->price_one_liter;
        $this->organization_id = $market->organization_id;
        $this->minus_stock = $market->minus_stock;
        $this->enabled_update_commissions_in_time = (bool) $market->enabled_update_commissions_in_time;
        $this->update_commissions_time = $market->update_commissions_time;
    }

    public function store(): void
    {
        $this->validate();

        Helpers::user()->wbMarkets()->create($this->except('market'));

        $this->reset();

    }

    public function update(): void
    {
        $this->validate();

        $this->market->update($this->except('market'));

    }

    public function destroy(): void
    {
        $this->market->delete();
    }
}
