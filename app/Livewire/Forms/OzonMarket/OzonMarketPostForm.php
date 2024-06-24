<?php

namespace App\Livewire\Forms\OzonMarket;

use App\Models\OzonMarket;
use Illuminate\Support\Arr;
use Livewire\Form;

class OzonMarketPostForm extends Form
{
    public ?OzonMarket $market;

    public $name;

    public $client_id;

    public $api_key;

    public $min_price_percent = null;

    public $max_price_percent = null;

    public $seller_price_percent = null;

    public $open = false;

    public $max_count = 50;

    public $min = 2;

    public $max = 5;

    public $seller_price = true;

    public $acquiring = null;

    public $last_mile = null;

    public $max_mile = null;

    public function setMarket(OzonMarket $market)
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
    }

    public function store()
    {
        $market = OzonMarket::create(Arr::add($this->except('market'), 'user_id', auth()->user()->id));

        $market->refresh();

        $this->reset();

        return $market;
    }

    public function update()
    {
        $this->market->update($this->only([
            'client_id',
            'api_key',
            'min_price_percent',
            'max_price_percent',
            'seller_price_percent',
            'acquiring',
            'last_mile',
            'max_mile',
            'open',
            'max_count',
            'min',
            'max'
        ]));
    }
}
