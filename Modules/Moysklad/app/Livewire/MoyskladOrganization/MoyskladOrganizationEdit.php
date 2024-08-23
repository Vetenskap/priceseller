<?php

namespace Modules\Moysklad\Livewire\MoyskladOrganization;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Moysklad\Livewire\Forms\MoyskladOrganization\MoyskladOrganizationPostForm;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladOrganizationOrganization;
use Modules\Moysklad\Services\MoyskladService;

class MoyskladOrganizationEdit extends Component
{
    public MoyskladOrganizationPostForm $form;

    public MoyskladOrganizationOrganization $organization;

    public Moysklad $moysklad;

    public function mount(): void
    {
        $this->form->setMoyskladOrganization($this->organization);
        $this->moysklad = $this->organization->moysklad;
    }

    #[On('update-organization')]
    public function update(): void
    {
        $this->form->update();
    }

    public function destroy(): void
    {
        $this->form->destroy();
        $this->dispatch('delete-organization')->component(MoyskladOrganizationIndex::class);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('moysklad::livewire.moysklad-organization.moysklad-organization-edit', [
            'moyskladOrganizations' => (new MoyskladService($this->moysklad))->getAllOrganizations()
        ]);
    }
}
