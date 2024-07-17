<?php

namespace App\Livewire\OzonMarket;

use App\Exports\OzonItemsExport;
use App\Jobs\Export;
use App\Jobs\Import;
use App\Jobs\MarketRelationshipsAndCommissions;
use App\Livewire\BaseComponent;
use App\Livewire\Forms\OzonMarket\OzonMarketPostForm;
use App\Livewire\Traits\WithFilters;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\OzonMarket;
use App\Models\OzonWarehouse;
use App\Models\Supplier;
use App\Services\ItemsExportReportService;
use App\Services\ItemsImportReportService;
use App\Services\OzonItemPriceService;
use App\Services\OzonMarketService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Livewire\Attributes\Session;
use Livewire\Attributes\Url;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class OzonMarketEdit extends BaseComponent
{
    use WithFileUploads, WithJsNotifications, WithFilters;

    public OzonMarketPostForm $form;

    public OzonMarket $market;

    public array $apiWarehouses = [];

    public int $selectedWarehouse;

    #[Url]
    public $page = null;

    /** @var TemporaryUploadedFile $file */
    public $file;

    #[Session('OzonMarketEdit.min_price_percent.{market.id}')]
    public $min_price_percent = null;

    #[Session('OzonMarketEdit.min_price.{market.id}')]
    public $min_price = null;

    #[Session('OzonMarketEdit.shipping_processing.{market.id}')]
    public $shipping_processing = null;

    public array $statusFilters = [
        [
            'status' => 1,
            'name' => 'Связь не создана'
        ],
        [
            'status' => 0,
            'name' => 'Связь создана'
        ],
    ];

    public function export(): void
    {
        if (ItemsExportReportService::get($this->market)) {
            $this->addJobAlready();
            return;
        }

        Export::dispatch($this->market, OzonMarketService::class);
        $this->addJobNotification();
    }

    public function downloadTemplate()
    {
        return \Excel::download(new OzonItemsExport($this->market, true), "ОЗОН_шаблон.xlsx");
    }

    public function import(): void
    {
        if (ItemsImportReportService::get($this->market)) {
            $this->addJobAlready();
            return;
        }

        $uuid = Str::uuid();
        $ext = $this->file->getClientOriginalExtension();
        $path = $this->file->storeAs(OzonMarketService::PATH, $uuid . '.' . $ext);

        if (!Storage::exists($path)) {
            $this->dispatch('livewire-upload-error');
            return;
        }

        Import::dispatch($uuid, $ext, $this->market, OzonMarketService::class);
        $this->addJobNotification();
    }

    public function relationshipsAndCommissions(): void
    {
        if (ItemsImportReportService::get($this->market)) {
            $this->addWarningImportNotification();
            return;
        }

        MarketRelationshipsAndCommissions::dispatch(
            defaultFields: collect($this->only(['shipping_processing', 'min_price', 'min_price_percent'])),
            model: $this->market,
            service: OzonMarketService::class
        );
    }

    public function clearRelationships()
    {
        $service = new OzonMarketService($this->market);
        $deleted = $service->clearRelationships();

        $this->addSuccessClearRelationshipsNotification($deleted);
    }

    public function mount(): void
    {
        $this->form->setMarket($this->market);

        if ($this->page === 'stocks_warehouses') $this->getWarehouses();

    }

    public function getWarehouses()
    {
        try {
            $service = new OzonMarketService($this->market);
            $warehouses = $service->getWarehouses();
            $this->apiWarehouses = $warehouses->all();
            $this->selectedWarehouse = $warehouses->first()['warehouse_id'];
        } catch (RequestException $e) {
            if ($e->response->unauthorized()) {
                $this->setErrorBag(new MessageBag([
                    'error' => 'Не верно ведён АПИ ключ или Идентификатор клиента',
                    'form.client_id' => 'Не верно ведён Идентификатор клиента',
                    'form.api_key' => 'Не верно ведён АПИ ключ',
                ]));
            } else {
                $this->setErrorBag(new MessageBag([
                    'error' => 'Неизвестная ошибка от сервера ОЗОН',
                ]));
            }
        }
    }

    public function save(): void
    {
        $this->authorize('update', $this->market);

        $this->form->update();

        $this->addSuccessSaveNotification();
    }

    public function destroy(): void
    {
        $this->authorize('delete', $this->market);

        $this->market->delete();

        $this->redirectRoute('ozon', navigate: true);
    }

    public function render()
    {
        $this->authorize('view', $this->market);

        $items = $this->market->relationships()->orderByDesc('updated_at')->filters()->paginate(100);

        return view('livewire.ozon-market.ozon-market-edit', [
            'items' => $items
        ]);
    }

    public function addWarehouse()
    {

        $this->authorize('create', OzonWarehouse::class);

        $name = collect($this->apiWarehouses)->firstWhere('warehouse_id', $this->selectedWarehouse)['name'];

        $this->market->warehouses()->updateOrCreate([
            'id' => $this->selectedWarehouse,
        ], [
            'id' => $this->selectedWarehouse,
            'name' => $name
        ]);
    }

    public function deleteWarehouse(OzonWarehouse $warehouse)
    {
        $this->authorize('delete', $warehouse);

        $warehouse->delete();
    }

    public function testPrice()
    {
        auth()->user()->suppliers->each(function (Supplier $supplier) {
            $service = new OzonItemPriceService($supplier, $this->market);
            $service->updatePriceTest();
        });

        $this->addSuccessTestPriceNotification();
    }

    public function nullStocks()
    {
        auth()->user()->suppliers->each(function (Supplier $supplier) {
            $service = new OzonItemPriceService($supplier, $this->market);
            $service->nullAllStocks();
            $service->unloadAllStocks();
        });

        $this->addSuccessNullStocksNotification();
    }
}
