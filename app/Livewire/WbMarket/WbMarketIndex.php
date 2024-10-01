<?php

namespace App\Livewire\WbMarket;

use App\Livewire\BaseComponent;
use App\Livewire\Components\Toast;
use App\Livewire\Forms\WbMarket\WbMarketPostForm;
use App\Models\WbMarket;
use App\Services\UsersPermissionsService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Title;

#[Title('ВБ')]
class WbMarketIndex extends BaseComponent
{
    public WbMarketPostForm $form;

    public function store(): void
    {
        $this->authorize('create', WbMarket::class);

        if (!UsersPermissionsService::checkWbPremission(auth()->user())) {
            $this->js((new Toast('Не разрешено', 'Ваша подписка не позволяет добавлять ещё кабинеты'))->warning());
            return;
        }

        $this->form->store();

        \Flux::modal('create-wb-market')->close();

    }

    public function changeOpen(string $id): void
    {
        $market = WbMarket::find($id);

        $this->authorize('update', $market);

        $market->open = !$market->open;
        $market->save();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.wb-market.wb-market-index', [
            'markets' => auth()->user()->wbMarkets
        ]);
    }
}
