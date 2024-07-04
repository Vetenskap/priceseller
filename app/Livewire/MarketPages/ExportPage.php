<?php

namespace App\Livewire\MarketPages;

use App\Models\OzonMarket;
use App\Models\WbMarket;
use Livewire\Component;

class ExportPage extends Component
{
    public OzonMarket|WbMarket $market;

    public function render()
    {
        return view('livewire.market-pages.export');
    }
}
