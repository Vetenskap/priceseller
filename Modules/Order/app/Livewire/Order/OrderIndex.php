<?php

namespace Modules\Order\Livewire\Order;

use App\Livewire\Components\Toast;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Order\Exports\NotChangeOzonStatesExport;
use Modules\Order\Exports\SupplierOrderExport;
use Modules\Order\Exports\WriteOffItemWarehouseStockExport;
use Modules\Order\Imports\NotChangeOzonStatesImport;
use Modules\Order\Models\SupplierOrderReport;
use Modules\Order\Models\WriteOffItemWarehouseStock;
use Modules\Order\Services\OrderService;
use Modules\Order\Services\OzonOrderService;
use Modules\Order\Services\WbOrderService;

class OrderIndex extends Component
{
    use WithJsNotifications, WithFileUploads;

    #[Url]
    public $organizationId;

    public ?Organization $organization = null;
    public ?Collection $orders = null;

    #[Session]
    public array $selectedWarehouses = [];

    #[Url]
    public $page = 'main';

    public $file;

    public $openSelectedWarehouses = false;

    public $automatic;

    public function updatedAutomatic()
    {
        $this->organization->automaticUnloadOrder()->updateOrCreate([
            'organization_id' => $this->organizationId
        ], [
            'automatic' => $this->automatic
        ]);
    }

    public function changeActive()
    {
        $this->openSelectedWarehouses = !$this->openSelectedWarehouses;
    }

    public function export()
    {
        return \Excel::download(new NotChangeOzonStatesExport(Auth::id()), 'Не менять статус.xlsx');
    }

    public function import()
    {
        \Excel::import(new NotChangeOzonStatesImport(\auth()->user()), $this->file);
    }

    #[On('refresh')]
    public function mount()
    {
        if ($this->organizationId) {
            $this->organization = auth()->user()->organizations()->findOrFail($this->organizationId);
            $this->orders = $this->organization->orders()->whereHas('orderable')->with('orderable.item')->where('state', 'new')->get();
            $this->selectedWarehouses = $this->organization->selectedOrdersWarehouses->pluck('id')->toArray();
            $this->automatic = $this->organization->automaticUnloadOrder ? $this->organization->automaticUnloadOrder->automatic : false;
        }
    }

    public function render()
    {
        if ($this->organization) {
            $this->authorize('view', $this->organization);
        }

        return view('order::livewire.order.order-index', [
            'organizations' => auth()->user()->organizations,
            'warehouses' => auth()->user()->warehouses,
            'writeOff' => WriteOffItemWarehouseStock::whereHas('order', function (Builder $query) {
                $query->where('organization_id', $this->organizationId);
            })->count()
        ]);
    }

    public function getOrders(): void
    {
        $service = new OrderService($this->organizationId, auth()->user());
        $total = $service->getOrders();
        $this->js((new Toast('Успех', 'Успешно получено заказов: ' . $total))->success());
        $this->dispatch('refresh')->self();
    }

    public function selectWarehouse(string $id)
    {
        if (!in_array($id, $this->selectedWarehouses)) {
            $this->selectedWarehouses[] = $id;
            $this->organization->selectedOrdersWarehouses()->attach($id);
        } else {
            Arr::forget($this->selectedWarehouses, array_search($id, $this->selectedWarehouses));
            $this->organization->selectedOrdersWarehouses()->detach($id);
        }
    }

    public function writeOffBalance()
    {
        $service = new OrderService($this->organizationId, auth()->user());
        $total = $service->writeOffBalance($this->selectedWarehouses);
        $this->js((new Toast('Успех', 'Списано: ' . $total))->success());
        $this->dispatch('refresh')->self();
    }

    public function downloadWriteOffBalance()
    {
        return \Excel::download(new WriteOffItemWarehouseStockExport($this->organizationId), 'Списание со складов ' . $this->organization->name . '.xlsx');
    }

    public function purchaseOrder()
    {
        $service = new OrderService($this->organizationId, auth()->user());
        $service->purchaseOrder();
        $this->dispatch('refresh')->self();
    }

    public function downloadPurchaseOrder(array $order)
    {
        $order = SupplierOrderReport::findOrFail($order['id']);

        $this->authorize('view', $order);

        return response()->download(Storage::disk('public')->path("users/orders/{$order->uuid}.xlsx"), 'Заказ поставщику ' . $this->organization->name . ' ' . $order->supplier->name . '.xlsx');
    }

    public function clear(): void
    {
        $service = new OrderService($this->organizationId, auth()->user());
        $service->clearAll();
        $this->dispatch('refresh')->self();
    }

    public function writeOffBalanceRollback()
    {
        $service = new OrderService($this->organizationId, auth()->user());
        $service->writeOffBalanceRollback();
        $this->js((new Toast('Успех', 'Все остатки возвращены на склад'))->success());
        $this->dispatch('refresh')->self();
    }

    public function writeOffMarketsStocks()
    {
        $service = new OzonOrderService($this->organization, auth()->user());
        $service->writeOffStocks();

        $service = new WbOrderService($this->organization, auth()->user());
        $service->writeOffStocks();

        $this->js((new Toast('Успех', 'Все остатки списаны с кабинетов'))->success());
    }

    public function setOrdersState()
    {
        $service = new OzonOrderService($this->organization, auth()->user());
        $total = $service->setStates();

        $this->js((new Toast('Успех', "Изменён статус у {$total} заказов на Озоне"))->success());
        $this->dispatch('refresh')->self();
    }

    public function startAllActions()
    {
        $service = new OrderService($this->organizationId, auth()->user());
        $service->processOrders();
        $this->dispatch('refresh')->self();
    }
}
