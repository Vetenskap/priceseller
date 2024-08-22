<?php

namespace Modules\BergApi\Livewire\BergApiItemAdditionalAttributeLink;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\BergApi\Livewire\Forms\BergApiItemAdditionalAttributeLink\BergApiItemAdditionalAttributeLinkPostForm;
use Modules\BergApi\Models\BergApiItemAdditionalAttributeLink;

class BergApiItemAdditionalAttributeLinkEdit extends Component
{
    public BergApiItemAdditionalAttributeLinkPostForm $form;
    public BergApiItemAdditionalAttributeLink $bergItemLink;

    public function destroy(): void
    {
        $this->form->destroy();
        $this->dispatch('delete-link')->component(BergApiItemAdditionalAttributeLinkIndex::class);
    }

    #[On('update-link')]
    public function update(): void
    {
        $this->form->update();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('bergapi::livewire.berg-api-item-additional-attribute-link.berg-api-item-additional-attribute-link-edit');
    }
}
