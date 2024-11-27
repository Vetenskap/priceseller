<?php

namespace App\Livewire\WbMarket;

use App\Exports\WbItemsExport;
use App\Jobs\Export;
use App\Jobs\Import;
use App\Jobs\MarketRelationshipsAndCommissions;
use App\Jobs\MarketUpdateApiCommissions;
use App\Jobs\Wb\NullStocks;
use App\Jobs\Wb\TestPrice;
use App\Jobs\Wb\TestStock;
use App\Livewire\BaseComponent;
use App\Livewire\Forms\WbMarket\WbMarketPostForm;
use App\Livewire\Traits\WithFilters;
use App\Livewire\Traits\WithJsNotifications;
use App\Livewire\Traits\WithSaveButton;
use App\Livewire\Traits\WithSort;
use App\Models\Supplier;
use App\Models\WbMarket;
use App\Services\WbItemPriceService;
use App\Services\WbMarketService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Title('ВБ')]
class WbMarketEdit extends BaseComponent
{
    use WithFileUploads, WithFilters, WithSort, WithPagination, WithSaveButton;

    public $backRoute = 'wb';

    public WbMarketPostForm $form;

    public WbMarket $market;

    /** @var TemporaryUploadedFile $file */
    public $file;

    #[Session('WbMarketEdit.min_price.{market.id}')]
    public $min_price = null;

    #[Session('WbMarketEdit.retail_markup_percent.{market.id}')]
    public $retail_markup_percent = null;

    #[Session('WbMarketEdit.package.{market.id}')]
    public $package = null;

    public $directLink = false;

    #[Session('WbMarketEdit.testWarehouses.{market.id}')]
    public $testWarehouses = [];

    #[Computed]
    public function items(): LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator|array
    {
        return $this->tapQuery($this->market->items()->filters());

    }

    public function updateUserCommissions(): void
    {
        $this->validate([
            'min_price' => 'nullable|numeric|min:0',
            'retail_markup_percent' => 'nullable|numeric|min:0',
            'package' => 'nullable|numeric|min:0',
        ]);

        collect($this->only('min_price', 'retail_markup_percent', 'package'))
            ->filter()
            ->each(fn($value, $key) => $this->market->items()->update([$key => $value]));

        \Flux::toast('Все комиссии обновлены', 'Успех');
    }

    public function updateApiCommissions(): void
    {
        MarketUpdateApiCommissions::dispatch(
            model: $this->market,
            service: WbMarketService::class,
            defaultFields: collect($this->only('sales_percent', 'min_price', 'retail_markup_percent', 'package'))->filter()->toArray(),
        );

        $this->addJobNotification();
    }


    public function mount(): void
    {
        $this->form->setMarket($this->market);
    }

    public function downloadTemplate(): BinaryFileResponse
    {
        return \Excel::download(new WbItemsExport($this->market, true), "ВБ_шаблон.xlsx");
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

    public function export(): void
    {
        $status = $this->checkTtlJob(Export::getUniqueId($this->market), Export::class);

        if ($status) Export::dispatch($this->market, WbMarketService::class);
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

        $status = $this->checkTtlJob(Import::getUniqueId($this->market), Import::class);

        if ($status) Import::dispatch($uuid, $ext, $this->market, WbMarketService::class);
    }

    public function relationshipsAndCommissions(): void
    {
        $status = $this->checkTtlJob(MarketRelationshipsAndCommissions::getUniqueId($this->market), MarketRelationshipsAndCommissions::class);

        if ($status) MarketRelationshipsAndCommissions::dispatch(
            defaultFields: collect($this->only(['package', 'retail_markup_percent', 'min_price', 'sales_percent'])),
            model: $this->market,
            service: WbMarketService::class,
            directLink: $this->directLink
        );
    }

    public function clearRelationships(): void
    {
        $service = new WbMarketService($this->market);
        $deleted = $service->clearRelationships();

        $this->addSuccessClearRelationshipsNotification($deleted);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $this->authorizeForUser($this->user(), 'view', $this->market);

        return view('livewire.wb-market.wb-market-edit');
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

}
