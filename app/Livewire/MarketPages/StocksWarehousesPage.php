<?php

namespace App\Livewire\MarketPages;

use App\Models\OzonMarket;
use App\Models\WbMarket;
use Illuminate\Support\Collection;
use Livewire\Component;

class StocksWarehousesPage extends Component
{
    public OzonMarket|WbMarket $market;
    public array $apiWarehouses;

    public function render()
    {
        return view('livewire.market-pages.stocks-warehouses-page');
    }
}
