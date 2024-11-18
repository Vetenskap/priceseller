<?php

namespace App\Livewire\Organization;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\Organization\OrganizationPostForm;
use App\Livewire\Traits\WithSaveButton;
use App\Models\Organization;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class OrganizationEdit extends BaseComponent
{
    use WithSaveButton;

    public OrganizationPostForm $form;

    public Organization $organization;

    public function mount(): void
    {
        $this->form->setOrganization($this->organization);
    }

    public function update(): void
    {
        $this->authorizeForUser($this->user(), 'update', $this->organization);

        $this->form->update();

        $this->addSuccessSaveNotification();
        $this->hideSaveButton();
    }

    public function destroy(): void
    {
        $this->authorizeForUser($this->user(), 'delete', $this->organization);

        $this->form->destroy();

        $this->redirectRoute('organizations.index');
    }

    public function render(): Factory|Application|View|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $this->authorizeForUser($this->user(), 'view', $this->organization);

        return view('livewire.organization.organization-edit');
    }
}
