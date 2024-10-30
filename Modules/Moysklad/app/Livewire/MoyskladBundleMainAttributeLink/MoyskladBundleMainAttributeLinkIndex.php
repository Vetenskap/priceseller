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
use Modules\Moysklad\Models\MoyskladBundleMainAttributeLink;
use Modules\Moysklad\Services\MoyskladService;

class MoyskladBundleMainAttributeLinkIndex extends Component
{
    use WithJsNotifications;

    public MoyskladBundleMainAttributeLinkPostForm $form;
    public Moysklad $moysklad;
    public $bundleAttributes;

    public function mount(): void
    {
        $this->form->setMoysklad($this->moysklad);
        $this->bundleAttributes = (new MoyskladService($this->moysklad))->getAllBundleAttributes();
        $this->form->setBundleAttributes($this->bundleAttributes);
    }

    public function store(): void
    {
        $this->form->store();

        \Flux::modal('create-moysklad-bundle-main-attribute-link')->close();
    }

    public function destroy($id): void
    {
        $link = MoyskladBundleMainAttributeLink::find($id);

        // TODO: add authorization
//        $this->authorize('delete', $link);

        $link->delete();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('moysklad::livewire.moysklad-bundle-main-attribute-link.moysklad-bundle-main-attribute-link-index');
    }
}
