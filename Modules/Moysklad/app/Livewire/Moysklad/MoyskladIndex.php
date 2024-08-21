<?php

namespace Modules\Moysklad\Livewire\Moysklad;

use App\Livewire\ModuleComponent;
use App\Livewire\Traits\WithJsNotifications;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\WithFileUploads;
use Modules\Moysklad\Models\Moysklad;
use MoyskladPostForm;

class MoyskladIndex extends ModuleComponent
{
    use WithJsNotifications, WithFileUploads;

    public MoyskladPostForm $form;

    public $page;

    public $file;

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
