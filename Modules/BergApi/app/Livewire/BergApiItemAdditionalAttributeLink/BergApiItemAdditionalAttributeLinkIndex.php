<?php

namespace Modules\BergApi\Livewire\BergApiItemAdditionalAttributeLink;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\BergApi\Livewire\Forms\BergApiItemAdditionalAttributeLink\BergApiItemAdditionalAttributeLinkPostForm;
use Modules\BergApi\Models\BergApi;

class BergApiItemAdditionalAttributeLinkIndex extends Component
{
    public BergApiItemAdditionalAttributeLinkPostForm $form;

    public BergApi $bergApi;

    public function mount(): void
    {
        $this->form->setBergApi($this->bergApi);
    }

    public function store(): void
    {
        $this->form->store();
    }

    public function destroy($id): void
    {
        $link = $this->bergApi->itemAdditionalAttributeLinks()->findOrFail($id);
        $link->delete();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('bergapi::livewire.berg-api-item-additional-attribute-link.berg-api-item-additional-attribute-link-index');
    }
}
