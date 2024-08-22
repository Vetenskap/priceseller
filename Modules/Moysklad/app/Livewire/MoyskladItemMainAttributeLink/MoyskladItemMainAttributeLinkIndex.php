<?php

namespace Modules\Moysklad\Livewire\MoyskladItemMainAttributeLink;

use App\Livewire\Traits\WithJsNotifications;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Moysklad\Livewire\Forms\MoyskladItemMainAttributeLink\MoyskladItemMainAttributeLinkPostForm;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Services\MoyskladService;

class MoyskladItemMainAttributeLinkIndex extends Component
{
    use WithJsNotifications;

    public MoyskladItemMainAttributeLinkPostForm $form;
    public Moysklad $moysklad;
    public $assortmentAttributes;

    #[On('delete-link')]
    public function mount(): void
    {
        $this->form->setMoysklad($this->moysklad);
        $this->assortmentAttributes = (new MoyskladService($this->moysklad))->getAllAssortmentAttributes();
        $this->form->setAssortmentAttributes($this->assortmentAttributes);
    }

    public function store(): void
    {
        $this->form->store();
    }

    public function update(): void
    {
        $this->dispatch('update-link')->component(MoyskladItemMainAttributeLinkEdit::class);
        $this->addSuccessSaveNotification();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('moysklad::livewire.moysklad-item-main-attribute-link.moysklad-item-main-attribute-link-index');
    }
}
