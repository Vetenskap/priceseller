<?php

namespace Modules\SamsonApi\Livewire\SamsonApiItemAdditionalAttributeLink;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\SamsonApi\Livewire\Forms\SamsonApiItemAdditionalAttributeLink\SamsonApiItemAdditionalAttributeLinkPostForm;
use Modules\SamsonApi\Models\SamsonApi;

class SamsonApiItemAdditionalAttributeLinkIndex extends Component
{
    public SamsonApiItemAdditionalAttributeLinkPostForm $form;

    public SamsonApi $samsonApi;

    public function mount(): void
    {
        $this->form->setSamsonApi($this->samsonApi);
    }

    public function store(): void
    {
        $this->form->store();
    }

    public function destroy($id): void
    {
        $link = $this->samsonApi->itemAdditionalAttributeLinks()->findOrFail($id);
        $link->delete();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('samsonapi::livewire.samson-api-item-additional-attribute-link.samson-api-item-additional-attribute-link-index');
    }
}
