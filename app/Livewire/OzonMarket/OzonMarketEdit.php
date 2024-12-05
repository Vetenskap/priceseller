<?php

namespace App\Livewire\OzonMarket;

use App\Exports\OzonItemsExport;
use App\Jobs\Export;
use App\Jobs\Import;
use App\Jobs\MarketRelationshipsAndCommissions;
use App\Jobs\MarketUpdateApiCommissions;
use App\Jobs\Ozon\NullStocks;
use App\Jobs\Ozon\TestPrice;
use App\Jobs\Ozon\TestStock;
use App\Jobs\Ozon\UnloadPrices;
use App\Livewire\BaseComponent;
use App\Livewire\Forms\OzonMarket\OzonMarketPostForm;
use App\Livewire\Traits\WithFilters;
use App\Livewire\Traits\WithJsNotifications;
use App\Livewire\Traits\WithSaveButton;
use App\Livewire\Traits\WithSort;
use App\Models\OzonMarket;
use App\Models\OzonWarehouse;
use App\Models\Supplier;
use App\Services\OzonItemPriceService;
use App\Services\OzonMarketService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use LaravelIdea\Helper\App\Models\_IH_OzonItem_C;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Title('ОЗОН')]
class OzonMarketEdit extends BaseComponent
{
    use WithFileUploads, WithFilters, WithSort, WithPagination, WithSaveButton;

    public $backRoute = 'ozon';

    public OzonMarketPostForm $form;

    public OzonMarket $market;

    public int $selectedWarehouse;

    /** @var TemporaryUploadedFile $file */
    public $file;

    #[Session('OzonMarketEdit.min_price_percent.{market.id}')]
    public $min_price_percent = null;

    #[Session('OzonMarketEdit.min_price.{market.id}')]
    public $min_price = null;

    #[Session('OzonMarketEdit.shipping_processing.{market.id}')]
    public $shipping_processing = null;

    public $directLink = false;

    #[Session('OzonMarketEdit.testWarehouses.{market.id}')]
    public $testWarehouses = [];

    #[Session('OzonMarketEdit.exportExtItemFields.{market.id}')]
    public $exportExtItemFields = [];

    #[Computed]
    public function items(): _IH_OzonItem_C|LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator|array
    {
        return $this->tapQuery($this->market->items()->filters());

    }

    public function updateUserCommissions(): void
    {
        $this->validate([
            'min_price_percent' => 'nullable|numeric|min:0|max:100',
            'min_price' => 'nullable|numeric|min:0',
            'shipping_processing' => 'nullable|numeric|min:0',
        ]);

        collect($this->only('min_price_percent', 'min_price', 'shipping_processing'))
            ->filter()
            ->each(fn($value, $key) => $this->market->items()->update([$key => $value]));

        \Flux::toast('Все комиссии обновлены', 'Успех');
    }

    public function export(): void
    {
        $status = $this->checkTtlJob(Export::getUniqueId($this->market), Export::class);

        if ($status) Export::dispatch($this->market, OzonMarketService::class, $this->exportExtItemFields);
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

    public function updateApiCommissions(): void
    {
        MarketUpdateApiCommissions::dispatch(
            model: $this->market,
            service: OzonMarketService::class,
            defaultFields: collect($this->only(['shipping_processing', 'min_price', 'min_price_percent']))->filter()->toArray(),
        );

        $this->addJobNotification();
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

    public function mount(): void
    {
        $this->form->setMarket($this->market);

    }

    public function update(): void
    {
        $this->authorizeForUser($this->user(), 'update', $this->market);

        $this->form->update();

        $this->addSuccessSaveNotification();
        $this->hideSaveButton();
    }

    public function destroy(): void
    {
        $this->authorizeForUser($this->user(), 'delete', $this->market);

        $this->form->destroy();

        $this->redirectRoute($this->backRoute);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $this->authorizeForUser($this->user(), 'view', $this->market);

        return view('livewire.ozon-market.ozon-market-edit');
    }

    public function testPrice(): void
    {
        TestPrice::dispatch($this->currentUser(), $this->market);

        $this->addJobNotification();
    }

    public function testStocks(): void
    {
        if (!count($this->testWarehouses)) {
            \Flux::toast('Выберите склады', variant: 'danger');
            return;
        }

        TestStock::dispatch($this->currentUser(), $this->testWarehouses, $this->market);

        $this->addJobNotification();
    }

    public function nullStocks(): void
    {
        if (!count($this->testWarehouses)) {
            \Flux::toast('Выберите склады', variant: 'danger');
            return;
        }

        NullStocks::dispatch($this->currentUser(), $this->testWarehouses, $this->market);

        $this->addJobNotification();
    }

    public function unloadPrices(): void
    {
        UnloadPrices::dispatch($this->currentUser(), $this->market);

        $this->addJobNotification();
    }
}
