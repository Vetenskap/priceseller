<?php

namespace App\Livewire\OzonMarket;

use App\Livewire\Forms\OzonMarket\OzonMarketPostForm;
use App\Livewire\Traits\WithSubscribeNotification;
use App\Models\OzonMarket;
use Livewire\Component;

class OzonMarketIndex extends Component
{
    use WithSubscribeNotification;

    public OzonMarketPostForm $form;

    public $showCreateForm = false;

    public function add()
    {
        $this->showCreateForm = ! $this->showCreateForm;
    }

    public function create()
    {
        $this->authorize('create', OzonMarket::class);

        $this->form->store();

        $this->reset('showCreateForm');
    }

    public function changeOpen($market)
    {
        $market = OzonMarket::find($market['id']);

        $this->authorize('update', $market);

        $market->open = !$market->open;
        $market->save();
    }

    public function destroy($market)
    {
        $market = OzonMarket::find($market['id']);

        $this->authorize('delete', $market);

        $market->delete();
    }

    public function render()
    {
        return view('livewire.ozon-market.ozon-market-index', [
            'markets' => OzonMarket::where('user_id', auth()->user()->id)->get()
        ]);
    }
}
