<?php

namespace Modules\VoshodApi\Livewire\VoshodApiItemAdditionalAttributeLink;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\VoshodApi\Models\VoshodApiItemAdditionalAttributeLink;
use VoshodApiItemAdditionalAttributeLink\VoshodApiItemAdditionalAttributeLinkPostForm;

class VoshodApiItemAdditionalAttributeLinkEdit extends Component
{
    public VoshodApiItemAdditionalAttributeLinkPostForm $form;
    public VoshodApiItemAdditionalAttributeLink $voshodItemLink;

    public function destroy(): void
    {
        $this->form->destroy();
        $this->dispatch('delete-link')->component(VoshodApiItemAdditionalAttributeLinkIndex::class);
    }

    #[On('update-link')]
    public function update(): void
    {
        $this->form->update();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('voshodapi::livewire.voshod-api-item-additional-attribute-link.voshod-api-item-additional-attribute-link-edit');
    }
}
