<?php

namespace Modules\BergApi\Livewire\BergApiItemAdditionalAttributeLink;

use BergApiItemAdditionalAttributeLink\BergApiItemAdditionalAttributeLinkPostForm;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\BergApi\Models\BergApi;

class BergApiItemAdditionalAttributeLinkIndex extends Component
{
    public BergApiItemAdditionalAttributeLinkPostForm $form;

    public BergApi $bergApi;

    #[On('delete-link')]
    public function mount(): void
    {
        $this->form->setBergApi($this->bergApi);
    }

    public function store(): void
    {
        $this->form->store();
    }

    public function update(): void
    {
        $this->dispatch('update-link')->component(BergApiItemAdditionalAttributeLinkEdit::class);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('bergapi::livewire.berg-api-item-additional-attribute-link.berg-api-item-additional-attribute-link-index');
    }
}
