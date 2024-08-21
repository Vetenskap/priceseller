<?php

namespace Modules\Moysklad\Livewire\MoyskladItemMainAttributeLink;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladItemMainAttributeLink;
use Modules\Moysklad\Services\MoyskladService;
use MoyskladItemMainAttributeLink\MoyskladItemMainAttributeLinkPostForm;

class MoyskladItemMainAttributeLinkEdit extends Component
{

    public MoyskladItemMainAttributeLinkPostForm $form;
    public MoyskladItemMainAttributeLink $moyskladItemLink;

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
        $this->dispatch('delete-link')->component(MoyskladItemMainAttributeLinkIndex::class);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('moysklad::livewire.moysklad-item-main-attribute-link.moysklad-item-main-attribute-link-edit');
    }
}
