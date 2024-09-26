<?php

namespace App\Livewire\OzonMarket;

use App\Livewire\BaseComponent;
use App\Livewire\Components\Toast;
use App\Livewire\Forms\OzonMarket\OzonMarketPostForm;
use App\Models\OzonMarket;
use App\Services\UsersPermissionsService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Title;

#[Title('ОЗОН')]
class OzonMarketIndex extends BaseComponent
{
    public OzonMarketPostForm $form;

    public function store(): void
    {
        $this->authorize('create', OzonMarket::class);

        if (!UsersPermissionsService::checkOzonPermission(auth()->user())) {
            $this->js((new Toast('Не разрешено', 'Ваша подписка не позволяет добавлять ещё кабинеты'))->warning());
            return;
        }

        $this->form->store();

    }

    public function changeOpen($id): void
    {
        $market = OzonMarket::find($id);

        $this->authorize('update', $market);

        $market->open = ! $market->open;
        $market->save();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.ozon-market.ozon-market-index', [
            'markets' => auth()->user()->ozonMarkets
        ]);
    }
}
