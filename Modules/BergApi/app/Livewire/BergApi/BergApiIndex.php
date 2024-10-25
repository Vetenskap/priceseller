<?php

namespace Modules\BergApi\Livewire\BergApi;

use App\Livewire\ModuleComponent;
use App\Livewire\Traits\WithJsNotifications;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Modules\BergApi\Livewire\Forms\BergApi\BergApiPostForm;
use Modules\BergApi\Models\BergApi;

class BergApiIndex extends ModuleComponent
{
    use WithJsNotifications;

    public BergApiPostForm $form;

    public $page;

    public function mount($page = 'main'): void
    {
        parent::mount();

        $this->page = $page;
        $this->form->setBergApi(auth()->user()->bergApi);
        if (!$this->form->bergApi) {
            $this->page = 'main';
        }
    }

    public function store(): void
    {
        if ($this->form->bergApi) {
            $this->authorize('update', $this->form->bergApi);
        } else {
            $this->authorize('create', BergApi::class);
        }

        $this->form->store();

        $this->addSuccessSaveNotification();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        if ($this->form->bergApi) {
            $this->authorize('view', $this->form->bergApi);
        } else {
            $this->authorize('view', BergApi::class);
        }

        return view('bergapi::livewire.berg-api.berg-api-index', [
            'modules' => $this->getEnabledModules()
        ]);
    }
}
