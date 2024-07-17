<?php

namespace App\Livewire\OzonMarket;

use App\Livewire\BaseComponent;
use App\Livewire\Components\Toast;
use App\Livewire\Forms\OzonMarket\OzonMarketPostForm;
use App\Models\OzonMarket;
use Illuminate\Auth\Access\AuthorizationException;

class OzonMarketIndex extends BaseComponent
{
    public OzonMarketPostForm $form;

    public $showCreateForm = false;

    public function add()
    {
        $this->showCreateForm = ! $this->showCreateForm;
    }

    public function create()
    {
        try {
            $this->authorize('create', OzonMarket::class);
        } catch (AuthorizationException) {
            $this->js((new Toast('Не разрешено', 'Ваша подписка не позволяет добавлять ещё кабинеты'))->warning());
            $this->reset('showCreateForm');
            return;
        }

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
            'markets' => auth()->user()->ozonMarkets
        ]);
    }
}
