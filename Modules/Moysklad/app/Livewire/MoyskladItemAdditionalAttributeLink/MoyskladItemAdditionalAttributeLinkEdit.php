<?php

namespace Modules\Moysklad\Livewire\MoyskladItemAdditionalAttributeLink;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Moysklad\Livewire\Forms\MoyskladItemAdditionalAttributeLink\MoyskladItemAdditionalAttributeLinkPostForm;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladItemAdditionalAttributeLink;
use Modules\Moysklad\Services\MoyskladService;

class MoyskladItemAdditionalAttributeLinkEdit extends Component
{
    public MoyskladItemAdditionalAttributeLinkPostForm $form;
    public MoyskladItemAdditionalAttributeLink $moyskladItemLink;

    public Moysklad $moysklad;

    public $assortmentAttributes;

    public function mount(): void
    {
        $this->form->setMoyskladItemLink($this->moyskladItemLink);
        $this->form->setMoysklad($this->moysklad);
        $this->assortmentAttributes = (new MoyskladService($this->moysklad))->getAllAssortmentAttributes();
        $this->form->setAssortmentAttributes($this->assortmentAttributes);
    }

    #[On('update-link')]
    public function update(): void
    {
        $this->form->update();
    }

    public function destroy(): void
    {
        $this->form->destroy();
        $this->dispatch('delete-link')->component(MoyskladItemAdditionalAttributeLinkIndex::class);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('moysklad::livewire.moysklad-item-additional-attribute-link.moysklad-item-additional-attribute-link-edit');
    }
}
