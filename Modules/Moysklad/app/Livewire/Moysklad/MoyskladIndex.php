<?php

namespace Modules\Moysklad\Livewire\Moysklad;

use App\Livewire\ModuleComponent;
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
    use WithFileUploads, WithSort, WithPagination;

    public MoyskladPostForm $form;

    public $page;

    public $file;

    #[Computed]
    public function quarantine(): LengthAwarePaginator|array|\Illuminate\Pagination\LengthAwarePaginator|_IH_MoyskladQuarantine_C
    {
        return $this->form->moysklad
            ->quarantine()
            ->with('item')
            ->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate();
    }

    public function unloadQuarantine(): void
    {
        $service = new MoyskladService($this->form->moysklad);
        $this->form->moysklad->quarantine()->chunk(1000, function (Collection $items) use ($service) {
            $items->each(function (MoyskladQuarantine $quarantine) use ($service) {
                $service->setBuyPriceFromQuarantine($quarantine);
            });
        });
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
