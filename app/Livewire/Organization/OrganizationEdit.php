<?php

namespace App\Livewire\Organization;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\Organization\OrganizationPostForm;
use App\Models\Organization;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class OrganizationEdit extends BaseComponent
{
    public OrganizationPostForm $form;

    public Organization $organization;

    public function mount(): void
    {
        $this->form->setOrganization($this->organization);
    }

    public function update(): void
    {
        // TODO: add auth

        $this->form->update();

        $this->addSuccessSaveNotification();
    }

    public function destroy(): void
    {
        // TODO: add auth

        $this->form->destroy();

        $this->redirectRoute('organizations.index');
    }

    public function render(): Factory|Application|View|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.organization.organization-edit');
    }
}
