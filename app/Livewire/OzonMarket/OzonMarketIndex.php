<?php

namespace App\Livewire\OzonMarket;

use App\Livewire\Forms\OzonMarket\OzonMarketPostForm;
use App\Models\OzonMarket;
use Livewire\Component;

class OzonMarketIndex extends Component
{
    public OzonMarketPostForm $form;

    public $markets;

    public $showCreateForm = false;

    public function add()
    {
        $this->showCreateForm = ! $this->showCreateForm;
    }

    public function mount()
    {
        $this->markets = OzonMarket::where('user_id', auth()->user()->id)->get();
    }

    public function create()
    {
        $this->authorize('create', OzonMarket::class);

        $market = $this->form->store();

        $this->markets->add($market);

        $this->reset('showCreateForm');
    }

    public function destroy(OzonMarket $market)
    {
        $this->authorize('delete', $market);

        $market->delete();
    }
}
