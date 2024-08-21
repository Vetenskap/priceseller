<?php

namespace Modules\Moysklad\Livewire\MoyskladItemAdditionalAttributeLink;

use App\Livewire\Traits\WithJsNotifications;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Services\MoyskladService;
use MoyskladItemAdditionalAttributeLink\MoyskladItemAdditionalAttributeLinkPostForm;

class MoyskladItemAdditionalAttributeLinkIndex extends Component
{
    use WithJsNotifications;

    public MoyskladItemAdditionalAttributeLinkPostForm $form;
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
        $this->dispatch('update-link')->component(MoyskladItemAdditionalAttributeLinkEdit::class);
        $this->addSuccessSaveNotification();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('moysklad::livewire.moysklad-item-additional-attribute-link.moysklad-item-additional-attribute-link-index');
    }
}
