<?php

namespace Modules\Moysklad\Livewire\Moysklad;

use App\Livewire\ModuleComponent;
use App\Livewire\Traits\WithFilters;
use App\Livewire\Traits\WithJsNotifications;
use App\Livewire\Traits\WithSort;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use LaravelIdea\Helper\Modules\Moysklad\Models\_IH_MoyskladQuarantine_C;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Modules\Moysklad\Livewire\Forms\Moysklad\MoyskladPostForm;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladQuarantine;
use Modules\Moysklad\Services\MoyskladService;

class MoyskladIndex extends ModuleComponent
{
    use WithFileUploads, WithSort, WithPagination, WithFilters;

    public MoyskladPostForm $form;

    public $page;

    public $file;

    #[Computed]
    public function quarantine(): LengthAwarePaginator|array|\Illuminate\Pagination\LengthAwarePaginator|_IH_MoyskladQuarantine_C
    {
        return $this->tapQuery($this->form->moysklad
            ->quarantine()
            ->join('items', 'moysklad_quarantines.item_id', '=', 'items.id') // Присоединяем таблицу items
            ->selectRaw('moysklad_quarantines.*, items.buy_price_reserve,
            ((items.buy_price_reserve - moysklad_quarantines.supplier_buy_price)
            / ((items.buy_price_reserve + moysklad_quarantines.supplier_buy_price) / 2) * 100) AS price_difference')
            ->filters());
    }

    public function unloadQuarantine(): void
    {
        $query = $this->form->moysklad
            ->quarantine()
            ->join('items', 'moysklad_quarantines.item_id', '=', 'items.id') // Присоединяем таблицу items
            ->selectRaw('moysklad_quarantines.*, items.buy_price_reserve,
            ((items.buy_price_reserve - moysklad_quarantines.supplier_buy_price)
            / ((items.buy_price_reserve + moysklad_quarantines.supplier_buy_price) / 2) * 100) AS price_difference')
            ->filters($this->filters['price_difference_from'] ?? null, $this->filters['price_difference_to'] ?? null);

        $service = new MoyskladService($this->form->moysklad);
        $service->setBuyPriceAllQuarantine($query);
        \Flux::toast('Все цены установлены');
    }

    public function setBuyPriceFromQuarantine($id): void
    {
        $quarantine = MoyskladQuarantine::find($id);
        $service = new MoyskladService($this->form->moysklad);
        $status = $service->setBuyPriceFromQuarantine($quarantine);
        if ($status) \Flux::toast('Цена установлена');
        else \Flux::toast('Цена не установлена');
    }

    public function store(): void
    {
        if ($this->form->moysklad) {
            $this->authorize('update', $this->form->moysklad);
        } else {
            $this->authorize('create', Moysklad::class);
        }

        $this->form->store();

        $this->addSuccessSaveNotification();
    }

    public function mount($page = 'main'): void
    {
        parent::mount();

        $this->page = $page;
        $this->form->setMoysklad(auth()->user()->moysklad);
        if (!$this->form->moysklad) {
            $this->page = 'main';
        }
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        if ($this->form->moysklad) {
            $this->authorize('view', $this->form->moysklad);
        } else {
            $this->authorize('view', Moysklad::class);
        }

        return view('moysklad::livewire.moysklad.moysklad-index', [
            'modules' => $this->getEnabledModules()
        ]);
    }
}
