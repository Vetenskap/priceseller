<?php

namespace Modules\VoshodApi\Livewire\VoshodApi;

use App\Livewire\ModuleComponent;
use App\Livewire\Traits\WithJsNotifications;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Modules\VoshodApi\Models\VoshodApi;

class VoshodApiIndex extends ModuleComponent
{
    use WithJsNotifications;

    public \VoshodApiPostForm $form;

    public $page;

    public function mount($page = 'main'): void
    {
        $this->page = $page;
        $this->form->setVoshodApi(auth()->user()->voshodApi);
        if (!$this->form->voshodApi) {
            $this->page = 'main';
        }
    }

    public function store(): void
    {
        if ($this->form->voshodApi) {
            $this->authorize('update', $this->form->voshodApi);
        } else {
            $this->authorize('create', VoshodApi::class);
        }

        $this->form->store();

        $this->addSuccessSaveNotification();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        if ($this->form->voshodApi) {
            $this->authorize('view', $this->form->voshodApi);
        } else {
            $this->authorize('view', VoshodApi::class);
        }

        return view('voshodapi::livewire.voshod-api.voshod-api-index', [
            'modules' => $this->getEnabledModules()
        ]);
    }
}
