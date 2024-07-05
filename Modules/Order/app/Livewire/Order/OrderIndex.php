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
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Order\Exports\NotChangeOzonStatesExport;
use Modules\Order\Exports\SupplierOrderExport;
use Modules\Order\Exports\WriteOffItemWarehouseStockExport;
use Modules\Order\Imports\NotChangeOzonStatesImport;
use Modules\Order\Models\Order;
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
    public array $selectedWarehouses = [];

    #[Url]
    public $page = 'main';

    public $file;

    public function export()
    {
        return \Excel::download(new NotChangeOzonStatesExport(Auth::id()), 'Не менять статус.xlsx');
    }

    public function import()
    {
        \Excel::import(new NotChangeOzonStatesImport(\auth()->user()), $this->file);
    }


    #[On(['refresh'])]
    public function mount()
    {
        if ($this->organizationId) {
            $this->organization = auth()->user()->organizations()->findOrFail($this->organizationId);
            $this->orders = $this->organization->orders()->with('orderable.item')->where('state', 'new')->get();
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

    public function getOrders()
    {
        $service = new OrderService($this->organizationId, auth()->user());
        $total = $service->getOrders();
        $this->js((new Toast('Успех', 'Успешно получено заказов: ' . $total))->success());
        $this->dispatch('refresh')->self();
    }

    public function selectWarehouse(array $warehouse)
    {
        if (!in_array($warehouse['id'], $this->selectedWarehouses)) {
            $this->selectedWarehouses[] = $warehouse['id'];
        } else {
            Arr::forget($this->selectedWarehouses, array_search($warehouse['id'], $this->selectedWarehouses));
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
        $this->organization->supplierOrderReports()->delete();

        $this->organization
            ->orders()
            ->with('orderable.item')
            ->where('state', 'new')
            ->get()
            ->groupBy('orderable.item.supplier_id')->each(function (Collection $hh, string $supplierId) {

                $uuid = Str::uuid();

                \Excel::store(new SupplierOrderExport($this->organizationId, $supplierId), "users/orders/{$uuid}.xlsx", 'public');

                SupplierOrderReport::create([
                    'supplier_id' => $supplierId,
                    'organization_id' => $this->organizationId,
                    'uuid' => $uuid,
                ]);
            });
    }

    public function downloadPurchaseOrder(array $order)
    {
        $order = SupplierOrderReport::findOrFail($order['id']);

        $this->authorize('view', $order);

        return response()->download(Storage::disk('public')->path("users/orders/{$order->uuid}.xlsx"), 'Заказ поставщику ' . $this->organization->name . ' ' . $order->supplier->name . '.xlsx');
    }

    public function clear()
    {
        $this->organization->orders()->chunk(100, function (Collection $orders) {
            $orders->each(function (Order $order) {

                $this->authorize('delete', $order);

                $order->writeOffStocks()->delete();
            });
        });
        $this->organization->orders()->update(['state' => 'old']);
        $this->organization->supplierOrderReports()->delete();
        $this->dispatch('refresh')->self();
    }

    public function writeOffBalanceRollback()
    {
        WriteOffItemWarehouseStock::whereHas('order', function (Builder $query) {
            $query->where('organization_id', $this->organizationId);
        })
            ->chunk(100, function (Collection $writeOffStocks) {
                $writeOffStocks->each(function (WriteOffItemWarehouseStock $writeOffStock) {

                    $itemWarehouseStock = $writeOffStock->itemWarehouseStock;
                    $itemWarehouseStock->stock = $itemWarehouseStock->stock + $writeOffStock->stock;
                    $order = $writeOffStock->order;
                    $order->count += $writeOffStock->stock;

                    $this->authorize('update', $order);

                    $itemWarehouseStock->save();
                    $order->save();
                    $writeOffStock->delete();

                });
            });
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
}
