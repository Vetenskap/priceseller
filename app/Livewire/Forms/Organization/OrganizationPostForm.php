<?php

namespace App\Livewire\Forms\Organization;

use App\Models\Organization;
use Illuminate\Support\Arr;
use Livewire\Form;

class OrganizationPostForm extends Form
{

    public ?Organization $organization;

    public $name;

    public function setOrganization(?Organization $organization = null)
    {
        $this->organization = $organization;
        $this->name = $this->organization->name;
    }

    public function store()
    {
        $organization = Organization::create(Arr::add($this->except('organization'), 'user_id', auth()->user()->id));

        $organization->refresh();

        $this->reset();

        return $organization;
    }

    public function update()
    {
        $this->organization->update($this->except('organization'));
    }
}
