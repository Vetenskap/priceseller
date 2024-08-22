<?php

namespace Modules\VoshodApi\Livewire\VoshodApiItemAdditionalAttributeLink;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\VoshodApi\Livewire\Forms\VoshodApiItemAdditionalAttributeLink\VoshodApiItemAdditionalAttributeLinkPostForm;
use Modules\VoshodApi\Models\VoshodApi;

class VoshodApiItemAdditionalAttributeLinkIndex extends Component
{
    public VoshodApiItemAdditionalAttributeLinkPostForm $form;

    public VoshodApi $voshodApi;

    #[On('delete-link')]
    public function mount(): void
    {
        $this->form->setVoshodApi($this->voshodApi);
    }

    public function store(): void
    {
        $this->form->store();
    }

    public function update(): void
    {
        $this->dispatch('update-link')->component(VoshodApiItemAdditionalAttributeLinkEdit::class);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('voshodapi::livewire.voshod-api-item-additional-attribute-link.voshod-api-item-additional-attribute-link-index');
    }
}
