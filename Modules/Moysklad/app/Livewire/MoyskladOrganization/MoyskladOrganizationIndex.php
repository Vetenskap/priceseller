<?php

namespace Modules\Moysklad\Livewire\MoyskladOrganization;

use App\Livewire\Traits\WithJsNotifications;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Moysklad\Livewire\Forms\MoyskladOrganization\MoyskladOrganizationPostForm;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Services\MoyskladService;

class MoyskladOrganizationIndex extends Component
{
    use WithJsNotifications;

    public MoyskladOrganizationPostForm $form;

    public Moysklad $moysklad;

    public function store(): void
    {
        $this->form->store();
    }

    #[On('delete-organization')]
    public function mount(): void
    {
        $this->form->setMoysklad($this->moysklad);
    }

    public function update(): void
    {
        $this->dispatch('update-organization')->component(MoyskladOrganizationEdit::class);
        $this->addSuccessSaveNotification();
    }

    public function render()
    {
        return view('moysklad::livewire.moysklad-organization.moysklad-organization-index', [
            'moyskladOrganizations' => (new MoyskladService($this->moysklad))->getAllOrganizations()
        ]);
    }
}
