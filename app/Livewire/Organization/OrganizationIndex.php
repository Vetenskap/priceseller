<?php

namespace App\Livewire\Organization;

use App\Livewire\Forms\Organization\OrganizationPostForm;
use App\Livewire\Traits\WithSubscribeNotification;
use App\Models\Organization;
use Livewire\Component;

class OrganizationIndex extends Component
{
    use WithSubscribeNotification;

    public OrganizationPostForm $form;

    public $showCreateForm = false;

    public function add()
    {
        $this->showCreateForm = ! $this->showCreateForm;
    }

    public function create()
    {
        $this->form->store();

        $this->reset('showCreateForm');
    }

    public function destroy($organization)
    {
        $market = Organization::find($organization['id']);

        $market->delete();
    }


    public function render()
    {
        return view('livewire.organization.organization-index', [
            'organizations' => auth()->user()->organizations
        ]);
    }
}
