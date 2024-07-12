<?php

namespace App\Livewire\WbMarket;

use App\Livewire\Components\Toast;
use App\Livewire\Forms\WbMarket\WbMarketPostForm;
use App\Livewire\Traits\WithSubscribeNotification;
use App\Models\WbMarket;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Component;

class WbMarketIndex extends Component
{
    use WithSubscribeNotification;

    public WbMarketPostForm $form;

    public $showCreateForm = false;

    public function add()
    {
        $this->showCreateForm = ! $this->showCreateForm;
    }

    public function create()
    {
        try {
            $this->authorize('create', WbMarket::class);
        } catch (AuthorizationException) {
            $this->js((new Toast('Не разрешено', 'Ваша подписка не позволяет добавлять ещё кабинеты'))->warning());
            $this->reset('showCreateForm');
            return;
        }

        $this->form->store();

        $this->reset('showCreateForm');
    }

    public function destroy($market)
    {
        $market = WbMarket::find($market['id']);

        $this->authorize('delete', $market);

        $market->delete();
    }

    public function changeOpen($market)
    {
        $market = WbMarket::find($market['id']);

        $this->authorize('update', $market);

        $market->open = !$market->open;
        $market->save();
    }

    public function render()
    {
        return view('livewire.wb-market.wb-market-index', [
            'markets' => auth()->user()->wbMarkets
        ]);
    }
}
