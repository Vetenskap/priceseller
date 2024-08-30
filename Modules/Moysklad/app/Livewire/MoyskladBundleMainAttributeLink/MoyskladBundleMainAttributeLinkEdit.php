<?php

namespace Modules\Moysklad\Livewire\MoyskladBundleMainAttributeLink;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Moysklad\Livewire\Forms\MoyskladBundleMainAttributeLink\MoyskladBundleMainAttributeLinkPostForm;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladBundleMainAttributeLink;
use Modules\Moysklad\Services\MoyskladService;

class MoyskladBundleMainAttributeLinkEdit extends Component
{
    public MoyskladBundleMainAttributeLinkPostForm $form;
    public ?MoyskladBundleMainAttributeLink $moyskladBundleLink = null;

    public Moysklad $moysklad;

    public $bundleAttributes;

    public function mount(): void
    {
        $this->form->setMoyskladBundleLink($this->moyskladBundleLink);
        $this->form->setMoysklad($this->moysklad);
        $this->bundleAttributes = (new MoyskladService($this->moysklad))->getAllBundleAttributes();
        $this->form->setBundleAttributes($this->bundleAttributes);
    }

    #[On('update-link')]
    public function update(): void
    {
        $this->form->update();
    }

    public function destroy(): void
    {
        $this->form->destroy();
        $this->dispatch('delete-link')->component(MoyskladBundleMainAttributeLinkIndex::class);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('moysklad::livewire.moysklad-bundle-main-attribute-link.moysklad-bundle-main-attribute-link-edit');
    }
}
