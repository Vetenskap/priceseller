<?php

namespace App\Livewire\MarketPages;

use App\Models\OzonMarket;
use App\Models\WbMarket;
use Livewire\Component;

class RelationshipsCommissions extends Component
{
    public OzonMarket|WbMarket $market;

    public function render()
    {
        return view('livewire.market-pages.relationships-commissions');
    }
}
