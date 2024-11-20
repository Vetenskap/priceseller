<?php

namespace App\Livewire\MarketActionReports;

use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithSort;
use App\Models\OzonMarket;
use App\Models\WbMarket;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class MarketActionReportsIndex extends BaseComponent
{
    use WithSort, WithPagination;

    public OzonMarket|WbMarket $market;

    public function getListeners(): array
    {
        return [
            'echo:notification.' . $this->currentUser()->id . ',.notify' => 'render',
        ];
    }

    #[Computed]
    public function reports(): LengthAwarePaginator
    {
        return $this->tapQuery($this->market->actionReports());
    }

    public function render()
    {
        return view('livewire.market-action-reports.market-action-reports-index');
    }
}
