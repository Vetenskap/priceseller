<?php

namespace Modules\SamsonApi\Livewire\SamsonApiItemAdditionalAttributeLink;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\SamsonApi\Livewire\Forms\SamsonApiItemAdditionalAttributeLink\SamsonApiItemAdditionalAttributeLinkPostForm;
use Modules\SamsonApi\Models\SamsonApiItemAdditionalAttributeLink;

class SamsonApiItemAdditionalAttributeLinkEdit extends Component
{
    public SamsonApiItemAdditionalAttributeLinkPostForm $form;
    public SamsonApiItemAdditionalAttributeLink $samsonItemLink;

    public function destroy(): void
    {
        $this->form->destroy();
        $this->dispatch('delete-link')->component(SamsonApiItemAdditionalAttributeLinkIndex::class);
    }

    #[On('update-link')]
    public function update(): void
    {
        $this->form->update();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('samsonapi::livewire.samson-api-item-additional-attribute-link.samson-api-item-additional-attribute-link-edit');
    }
}
