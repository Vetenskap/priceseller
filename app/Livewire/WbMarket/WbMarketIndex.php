<?php

namespace App\Livewire\WbMarket;

use App\Livewire\BaseComponent;
use App\Livewire\Components\Toast;
use App\Livewire\Forms\WbMarket\WbMarketPostForm;
use App\Livewire\Traits\WithSort;
use App\Models\WbMarket;
use App\Services\UsersPermissionsService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Title('ВБ')]
class WbMarketIndex extends BaseComponent
{
    use WithSort, WithPagination;

    public WbMarketPostForm $form;

    public $dirtyMarkets= [];

    public function mount(): void
    {
        $this->dirtyMarkets = auth()->user()->wbMarkets->pluck(null, 'id')->toArray();
    }

    public function updatedDirtyMarkets(): void
    {
        collect($this->dirtyMarkets)->each(function ($market, $key) {
            $marketModel = WbMarket::findOrFail($key);
            $marketModel->update($market);
        });
    }

    public function destroy($id): void
    {
        $this->form->setMarket(WbMarket::findOrFail($id));
        $this->form->destroy();

        $this->addSuccessDeleteNotification();
    }

    #[Computed]
    public function markets()
    {
        return auth()->user()
            ->wbMarkets()
            ->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate();

    }

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

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.wb-market.wb-market-index');
    }
}
