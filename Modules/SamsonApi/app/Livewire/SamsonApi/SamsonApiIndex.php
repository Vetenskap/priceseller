<?php

namespace Modules\SamsonApi\Livewire\SamsonApi;

use App\Livewire\ModuleComponent;
use App\Livewire\Traits\WithJsNotifications;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Modules\SamsonApi\Livewire\Forms\SamsonApi\SamsonApiPostForm;
use Modules\SamsonApi\Models\SamsonApi;

class SamsonApiIndex extends ModuleComponent
{
    use WithJsNotifications;

    public SamsonApiPostForm $form;

    public $page;

    public function mount($page = 'main'): void
    {
        parent::mount();

        $this->page = $page;
        $this->form->setSamsonApi(auth()->user()->samsonApi);
        if (!$this->form->samsonApi) {
            $this->page = 'main';
        }
    }

    public function store(): void
    {
        if ($this->form->samsonApi) {
            $this->authorize('update', $this->form->samsonApi);
        } else {
            $this->authorize('create', SamsonApi::class);
        }

        $this->form->store();

        $this->addSuccessSaveNotification();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        if ($this->form->samsonApi) {
            $this->authorize('view', $this->form->samsonApi);
        } else {
            $this->authorize('view', SamsonApi::class);
        }

        return view('samsonapi::livewire.samson-api.samson-api-index', [
            'modules' => $this->getEnabledModules()
        ]);
    }
}
