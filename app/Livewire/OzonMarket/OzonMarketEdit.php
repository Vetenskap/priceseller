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
use App\Services\OzonItemPriceService;
use App\Services\OzonMarketService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Title('ОЗОН')]
class OzonMarketEdit extends BaseComponent
{
    use WithFileUploads, WithJsNotifications, WithFilters;

    public $backRoute = 'ozon';

    public OzonMarketPostForm $form;

    public OzonMarket $market;

    public int $selectedWarehouse;

    public $page;

    /** @var TemporaryUploadedFile $file */
    public $file;

    #[Session('OzonMarketEdit.min_price_percent.{market.id}')]
    public $min_price_percent = null;

    #[Session('OzonMarketEdit.min_price.{market.id}')]
    public $min_price = null;

    #[Session('OzonMarketEdit.shipping_processing.{market.id}')]
    public $shipping_processing = null;

    public $directLink = false;

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
        $status = $this->checkTtlJob(Export::getUniqueId($this->market), Export::class);

        if ($status) Export::dispatch($this->market, OzonMarketService::class);
    }

    public function downloadTemplate(): BinaryFileResponse
    {
        return \Excel::download(new OzonItemsExport($this->market, true), "ОЗОН_шаблон.xlsx");
    }

    public function import(): void
    {
        if (!$this->file) $this->dispatch('livewire-upload-error');

        $uuid = Str::uuid();
        $ext = $this->file->getClientOriginalExtension();
        $path = $this->file->storeAs(OzonMarketService::PATH, $uuid . '.' . $ext);

        if (!Storage::exists($path)) {
            $this->dispatch('livewire-upload-error');
            return;
        }

        $status = $this->checkTtlJob(Import::getUniqueId($this->market), Import::class);

        if ($status) Import::dispatch($uuid, $ext, $this->market, OzonMarketService::class);
    }

    public function relationshipsAndCommissions(): void
    {
        MarketRelationshipsAndCommissions::dispatch(
            defaultFields: collect($this->only(['shipping_processing', 'min_price', 'min_price_percent'])),
            model: $this->market,
            service: OzonMarketService::class,
            directLink: $this->directLink
        );

        $this->addJobNotification();
    }

    public function clearRelationships(): void
    {
        $service = new OzonMarketService($this->market);
        $deleted = $service->clearRelationships();

        $this->addSuccessClearRelationshipsNotification($deleted);
    }

    public function mount($page = 'main'): void
    {
        $this->page = $page;
        $this->form->setMarket($this->market);

        if ($this->page === 'stocks_warehouses') $this->getWarehouses();

    }

    public function getWarehouses(): Collection
    {
        try {
            $service = new OzonMarketService($this->market);
            return $service->getWarehouses();
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
            return collect();
        }
    }

    public function update(): void
    {
        $this->authorize('update', $this->market);

        $this->form->update();

        $this->addSuccessSaveNotification();
    }

    public function destroy(): void
    {
        $this->authorize('delete', $this->market);

        $this->form->destroy();

        $this->redirectRoute($this->backRoute);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $this->authorize('view', $this->market);

        switch ($this->page) {
            case 'main':
                return view('livewire.ozon-market.pages.ozon-market-main-page');
            case 'prices':
                return view('livewire.ozon-market.pages.ozon-market-prices-page');
            case 'stocks_warehouses':
                return view('livewire.ozon-market.pages.ozon-market-stocks_warehouses-page', [
                    'apiWarehouses' => $this->getWarehouses()
                ]);
            case 'relationships_commissions':
                $items = $this->market->relationships()->orderByDesc('updated_at')->filters()->paginate(100);
                return view('livewire.ozon-market.pages.ozon-market-relationships_commissions-page', [
                    'items' => $items
                ]);
            case 'export':
                return view('livewire.ozon-market.pages.ozon-market-export-page');
            case 'actions':
                return view('livewire.ozon-market.pages.ozon-market-actions-page');
            default:
                return view('livewire.ozon-market.ozon-market-edit');
        }
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
