<?php

namespace App\Livewire\OzonMarket;

use App\Livewire\BaseComponent;
use App\Livewire\Components\Toast;
use App\Livewire\Forms\OzonMarket\OzonMarketPostForm;
use App\Livewire\Traits\WithSort;
use App\Models\OzonMarket;
use App\Services\UsersPermissionsService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Title('ОЗОН')]
class OzonMarketIndex extends BaseComponent
{
    use WithSort, WithPagination;

    public OzonMarketPostForm $form;

    public function mount()
    {
        $this->sortBy = 'ozon_markets.updated_at';
    }

    public function destroy($id): void
    {
        $market = OzonMarket::findOrFail($id);

        $this->authorizeForUser($this->user(), 'delete', $market);

        $market->delete();

        $this->addSuccessDeleteNotification();
    }

    #[Computed]
    public function markets(): LengthAwarePaginator
    {
        return $this->tapQuery($this->currentUser()->ozonMarkets()->select('ozon_markets.*', 'organizations.name AS organization_name')->join('organizations', 'ozon_markets.organization_id', '=', 'organizations.id'));

    }

    public function store(): void
    {
        $this->authorizeForUser($this->user(), 'create', OzonMarket::class);

        if (!UsersPermissionsService::checkOzonPermission($this->currentUser())) {
            $this->js((new Toast('Не разрешено', 'Ваша подписка не позволяет добавлять ещё кабинеты'))->warning());
            return;
        }

        $this->form->store();

        \Flux::modal('create-ozon-market')->close();

    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        if (!$this->user()->can('view-ozon')) {
            abort(403);
        }

        return view('livewire.ozon-market.ozon-market-index');
    }
}
