<?php

namespace App\Livewire\WbMarket;

use App\Livewire\Forms\WbMarket\WbMarketPostForm;
use App\Models\WbMarket;
use Livewire\Component;

class WbMarketIndex extends Component
{
    public WbMarketPostForm $form;

    public $markets;

    public $showCreateForm = false;

    public function add()
    {
        $this->showCreateForm = ! $this->showCreateForm;
    }

    public function mount()
    {
        $this->markets = WbMarket::where('user_id', auth()->user()->id)->get();
    }

    public function create()
    {
        $this->authorize('create', WbMarket::class);

        $market = $this->form->store();

        $this->markets->add($market);

        $this->reset('showCreateForm');
    }

    public function destroy(WbMarket $market)
    {
        $this->authorize('delete', $market);

        $market->delete();
    }
}
