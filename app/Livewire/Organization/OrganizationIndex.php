<?php

namespace App\Livewire\Organization;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\Organization\OrganizationPostForm;
use App\Models\Organization;

class OrganizationIndex extends BaseComponent
{

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
