<?php

namespace App\Livewire\WbMarket;

use App\Livewire\BaseComponent;
use App\Livewire\Components\Toast;
use App\Livewire\Forms\WbMarket\WbMarketPostForm;
use App\Livewire\Traits\WithSort;
use App\Models\WbMarket;
use App\Services\UsersPermissionsService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Title('ВБ')]
class WbMarketIndex extends BaseComponent
{
    use WithSort, WithPagination;

    public WbMarketPostForm $form;

    public function destroy($id): void
    {
        $market = WbMarket::findOrFail($id);

        $this->authorizeForUser($this->user(), 'delete', $market);

        $market->delete();

        $this->addSuccessDeleteNotification();
    }

    #[Computed]
    public function markets(): LengthAwarePaginator
    {
        return $this->tapQuery($this->currentUser()->wbMarkets()->join('organizations', 'wb_markets.organization_id', '=', 'organizations.id'));

    }

    public function store(): void
    {
        $this->authorizeForUser($this->user(), 'create', WbMarket::class);

        if (!UsersPermissionsService::checkWbPremission($this->currentUser())) {
            $this->js((new Toast('Не разрешено', 'Ваша подписка не позволяет добавлять ещё кабинеты'))->warning());
            return;
        }

        $this->form->store();

        \Flux::modal('create-wb-market')->close();

    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        if (!$this->user()->can('view-wb')) {
            abort(403);
        }

        return view('livewire.wb-market.wb-market-index');
    }
}
