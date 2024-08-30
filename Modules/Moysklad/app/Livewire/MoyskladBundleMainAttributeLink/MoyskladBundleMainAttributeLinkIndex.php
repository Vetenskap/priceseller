<?php

namespace Modules\Moysklad\Livewire\MoyskladBundleMainAttributeLink;

use App\Livewire\Traits\WithJsNotifications;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Moysklad\Livewire\Forms\MoyskladBundleMainAttributeLink\MoyskladBundleMainAttributeLinkPostForm;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Services\MoyskladService;

class MoyskladBundleMainAttributeLinkIndex extends Component
{
    use WithJsNotifications;

    public MoyskladBundleMainAttributeLinkPostForm $form;
    public Moysklad $moysklad;
    public $bundleAttributes;

    #[On('delete-link')]
    public function mount(): void
    {
        $this->form->setMoysklad($this->moysklad);
        $this->bundleAttributes = (new MoyskladService($this->moysklad))->getAllBundleAttributes();
        $this->form->setBundleAttributes($this->bundleAttributes);
    }

    public function store(): void
    {
        $this->form->store();
    }

    public function update(): void
    {
        $this->dispatch('update-link')->component(MoyskladBundleMainAttributeLinkEdit::class);
        $this->addSuccessSaveNotification();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('moysklad::livewire.moysklad-bundle-main-attribute-link.moysklad-bundle-main-attribute-link-index');
    }
}
