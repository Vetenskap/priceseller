<?php

namespace App\Livewire\OzonMarket;

use App\Livewire\BaseComponent;
use App\Livewire\Components\Toast;
use App\Livewire\Forms\OzonMarket\OzonMarketPostForm;
use App\Livewire\Traits\WithSort;
use App\Models\OzonMarket;
use App\Services\UsersPermissionsService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Title('ОЗОН')]
class OzonMarketIndex extends BaseComponent
{
    use WithSort, WithPagination;

    public OzonMarketPostForm $form;

    public $dirtyMarkets= [];

    public function mount(): void
    {
        $this->dirtyMarkets = auth()->user()->ozonMarkets->pluck(null, 'id')->toArray();
    }

    public function updatedDirtyMarkets(): void
    {
        collect($this->dirtyMarkets)->each(function ($market, $key) {
            $marketModel = OzonMarket::findOrFail($key);
            $marketModel->update($market);
        });
    }

    public function destroy($id): void
    {
        $this->form->setMarket(OzonMarket::findOrFail($id));
        $this->form->destroy();

        $this->addSuccessDeleteNotification();
    }

    #[Computed]
    public function markets()
    {
        return auth()->user()
            ->ozonMarkets()
            ->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate();

    }

    public function store(): void
    {
        $this->authorize('create', OzonMarket::class);

        if (!UsersPermissionsService::checkOzonPermission(auth()->user())) {
            $this->js((new Toast('Не разрешено', 'Ваша подписка не позволяет добавлять ещё кабинеты'))->warning());
            return;
        }

        $this->form->store();

        \Flux::modal('create-ozon-market')->close();

    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.ozon-market.ozon-market-index');
    }
}
