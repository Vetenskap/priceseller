<?php

namespace App\Livewire\Forms\WbMarket;

use App\Models\WbMarket;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Livewire\Form;

class WbMarketPostForm extends Form
{
    public ?WbMarket $market;

    public $name;

    public $api_key;

    public $open = false;

    public $max_count = 50;

    public $min = 2;

    public $max = 5;

    public $volume = null;

    public $coefficient = 1.0;

    public $basic_logistics = null;

    public $price_one_liter = null;

    public $organization_id = null;

    public function setMarket(WbMarket $market)
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
    }

    public function store()
    {
        $market = WbMarket::create(Arr::add($this->except('market'), 'user_id', auth()->user()->id));

        $market->refresh();

        $this->reset();

        return $market;
    }

    public function update()
    {
        $this->market->update($this->except('market'));

        Cache::tags(['wb', 'warehouses'])->forget($this->market->id);
    }
}
