<?php

namespace App\Livewire\WbMarket;

use App\Jobs\Export;
use App\Jobs\Import;
use App\Jobs\MarketRelationshipsAndCommissions;
use App\Livewire\Forms\WbMarket\WbMarketPostForm;
use App\Livewire\Traits\WithFilters;
use App\Livewire\Traits\WithJsNotifications;
use App\Livewire\Traits\WithSubscribeNotification;
use App\Models\Supplier;
use App\Models\WbMarket;
use App\Models\WbWarehouse;
use App\Services\ItemsImportReportService;
use App\Services\WbItemPriceService;
use App\Services\WbMarketService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Livewire\Attributes\Session;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class WbMarketEdit extends Component
{
    use WithFileUploads, WithJsNotifications, WithFilters, WithSubscribeNotification;

    public WbMarketPostForm $form;

    public WbMarket $market;

    #[Url]
    public $page = 'main';

    /** @var TemporaryUploadedFile $file */
    public $file;

    #[Session('WbMarketEdit.sales_percent.{market.id}')]
    public $sales_percent = null;

    #[Session('WbMarketEdit.min_price.{market.id}')]
    public $min_price = null;

    #[Session('WbMarketEdit.retail_markup_percent.{market.id}')]
    public $retail_markup_percent = null;

    #[Session('WbMarketEdit.package.{market.id}')]
    public $package = null;

    public $apiWarehouses = [];

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


    public function mount()
    {
        $this->form->setMarket($this->market);

        if ($this->page === 'stocks_warehouses') $this->getWarehouses();
    }

    public function save()
    {
        $this->authorize('update', $this->market);

        $this->form->update();

        $this->addSuccessSaveNotification();
    }

    public function destroy()
    {
        $this->authorize('delete', $this->market);

        $this->market->delete();

        $this->redirectRoute('ozon', navigate: true);
    }

    public function export(): void
    {
        Export::dispatch($this->market, WbMarketService::class);

        $this->dispatch('items-export-report-created');

    }

    public function import(): void
    {
        if (!$this->file) $this->dispatch('livewire-upload-error');

        $uuid = Str::uuid();
        $ext = $this->file->getClientOriginalExtension();

        $path = $this->file->storeAs(WbMarketService::PATH, $uuid . '.' . $ext);

        if (!Storage::exists($path)) {
            $this->dispatch('livewire-upload-error');
            return;
        }

        Import::dispatch($uuid, $ext, $this->market, WbMarketService::class);

        $this->dispatch('items-import-report-created');
    }

    public function relationshipsAndCommissions(): void
    {
        if (ItemsImportReportService::get($this->market)) {
            $this->addWarningImportNotification();
            return;
        }

        MarketRelationshipsAndCommissions::dispatch(
            defaultFields: collect($this->only(['package', 'retail_markup_percent', 'min_price', 'sales_percent'])),
            model: $this->market,
            service: WbMarketService::class
        );

        $this->dispatch('items-import-report-created');
    }

    public function getWarehouses()
    {
        $service = new WbMarketService($this->market);

        try {
            $warehouses = $service->getWarehouses();
            $this->apiWarehouses = $warehouses->all();
        } catch (RequestException $e) {
            if ($e->response->unauthorized()) {
                $this->setErrorBag(new MessageBag([
                    'error' => 'Не верно ведён АПИ ключ',
                    'form.api_key' => 'Не верно ведён АПИ ключ'
                ]));
            } else {
                $this->setErrorBag(new MessageBag([
                    'error' => 'Неизвестная ошибка от сервера ВБ',
                ]));
            }
        }
    }

    public function clearRelationships()
    {
        $service = new WbMarketService($this->market);
        $deleted = $service->clearRelationships();

        $this->addSuccessClearRelationshipsNotification($deleted);
    }

    public function render()
    {
        $this->authorize('view', $this->market);

        $items = $this->market->relationships()->orderByDesc('updated_at')->filters()->paginate(100);

        return view('livewire.wb-market.wb-market-edit', [
            'items' => $items
        ]);
    }

    public function testPrice()
    {
        auth()->user()->suppliers->each(function (Supplier $supplier) {
            $service = new WbItemPriceService($supplier, $this->market);
            $service->updatePriceTest();
        });

        $this->addSuccessTestPriceNotification();
    }

    public function nullStocks()
    {
        auth()->user()->suppliers->each(function (Supplier $supplier) {
            $service = new WbItemPriceService($supplier, $this->market);
            $service->nullAllStocks();
            $service->unloadAllStocks();
        });

        $this->addSuccessNullStocksNotification();
    }

}
