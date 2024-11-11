<?php

namespace Modules\Order\Livewire\Order;

use App\Livewire\Components\Toast;
use App\Livewire\ModuleComponent;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\Organization;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Session;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use Modules\Order\Exports\NotChangeOzonStatesExport;
use Modules\Order\Exports\WriteOffItemWarehouseStockExport;
use Modules\Order\Imports\NotChangeOzonStatesImport;
use Modules\Order\Models\SupplierOrderReport;
use Modules\Order\Models\WriteOffItemWarehouseStock;
use Modules\Order\Services\OrderService;
use Modules\Order\Services\OzonOrderService;
use Modules\Order\Services\WbOrderService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OrderIndex extends ModuleComponent
{
    use WithJsNotifications, WithFileUploads;

    #[Url]
    public $organizationId;

    public ?Organization $organization = null;
    public ?Collection $orders = null;

    #[Session]
    public array $selectedWarehouses = [];

    public $page;

    public $file;

    public $automatic;

    public function updatedAutomatic(): void
    {
        $this->organization->automaticUnloadOrder()->updateOrCreate([
            'organization_id' => $this->organizationId
        ], [
            'automatic' => $this->automatic
        ]);

        $this->addSuccessSaveNotification();
    }

    public function updatedSelectedWarehouses(): void
    {
        $oldSelected = $this->organization->selectedOrdersWarehouses->pluck('id')->toArray();

        $this->organization->selectedOrdersWarehouses()->detach($oldSelected);

        $selectedWarehouses = collect($this->selectedWarehouses)->filter(fn ($value, $key) => $value)->keys()->toArray();

        $this->organization->selectedOrdersWarehouses()->attach($selectedWarehouses);

        $this->addSuccessSaveNotification();
    }

    public function export(): BinaryFileResponse
    {
        return \Excel::download(new NotChangeOzonStatesExport(Auth::id()), 'Не менять статус.xlsx');
    }

    public function import(): void
    {
        \Excel::import(new NotChangeOzonStatesImport($this->currentUser()), $this->file);
    }

    public function mount($page = 'main'): void
    {
        parent::mount();

        $this->page = $page;

        if ($this->organizationId) {
            $this->organization = $this->currentUser()->organizations()->findOrFail($this->organizationId);
            $this->orders = $this->organization
                ->orders()
                ->whereHas('orderable')
                ->with('orderable.itemable')
                ->where('state', 'new')
                ->get();
            $this->selectedWarehouses = $this->organization->selectedOrdersWarehouses->mapWithKeys(fn (Warehouse $selectedWarehouses) => [$selectedWarehouses->id => true])->toArray();
            $this->automatic = $this->organization->automaticUnloadOrder ? boolval($this->organization->automaticUnloadOrder->automatic) : false;
        }
    }

    public function getOrders(): void
    {
        $service = new OrderService($this->organizationId, auth()->user());
        $total = $service->getOrders();
        $this->js((new Toast('Успех', 'Успешно получено заказов: ' . $total))->success());
    }

    public function selectWarehouse(string $id): void
    {
        if (!in_array($id, $this->selectedWarehouses)) {
            $this->selectedWarehouses[] = $id;
            $this->organization->selectedOrdersWarehouses()->attach($id);
        } else {
            Arr::forget($this->selectedWarehouses, array_search($id, $this->selectedWarehouses));
            $this->organization->selectedOrdersWarehouses()->detach($id);
        }
    }

    public function writeOffBalance(): void
    {
        $service = new OrderService($this->organizationId, auth()->user());
        $total = $service->writeOffBalance($this->selectedWarehouses);
        $this->js((new Toast('Успех', 'Списано: ' . $total))->success());
    }

    public function downloadWriteOffBalance(): BinaryFileResponse
    {
        return \Excel::download(new WriteOffItemWarehouseStockExport($this->organizationId), 'Списание со складов ' . $this->organization->name . '.xlsx');
    }

    public function purchaseOrder(): void
    {
        $service = new OrderService($this->organizationId, auth()->user());
        $service->purchaseOrder();
    }

    public function downloadPurchaseOrder(array $order): BinaryFileResponse
    {
        $order = SupplierOrderReport::findOrFail($order['id']);

        $this->authorize('view', $order);

        return response()->download(Storage::disk('public')->path("users/orders/{$order->uuid}.xlsx"), 'Заказ поставщику ' . $this->organization->name . ' ' . $order->supplier->name . '.xlsx');
    }

    public function clear(): void
    {
        $service = new OrderService($this->organizationId, auth()->user());
        $service->clearAll();
    }

    public function writeOffBalanceRollback(): void
    {
        $service = new OrderService($this->organizationId, auth()->user());
        $service->writeOffBalanceRollback();
        $this->js((new Toast('Успех', 'Все остатки возвращены на склад'))->success());
    }

    public function writeOffMarketsStocks(): void
    {
        $service = new OzonOrderService($this->organization, auth()->user());
        $service->writeOffStocks();

        $service = new WbOrderService($this->organization, auth()->user());
        $service->writeOffStocks();

        $this->js((new Toast('Успех', 'Все остатки списаны с кабинетов'))->success());
    }

    public function setOrdersState(): void
    {
        $service = new OzonOrderService($this->organization, auth()->user());
        $total = $service->setStates();

        $this->js((new Toast('Успех', "Изменён статус у {$total} заказов на Озоне"))->success());
    }

    public function startAllActions(): void
    {
        $service = new OrderService($this->organizationId, auth()->user());
        $service->processOrders();
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
            })->count(),
            'modules' => $this->getEnabledModules()
        ]);
    }
}
