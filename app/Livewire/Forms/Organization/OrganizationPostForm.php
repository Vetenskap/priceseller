<?php

namespace App\Livewire\Forms\Organization;

use App\Models\Organization;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Livewire\Attributes\Validate;
use Livewire\Form;

class OrganizationPostForm extends Form
{

    public ?Organization $organization = null;

    #[Validate]
    public $name;

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                Rule::unique('organizations', 'name')
                    ->where('user_id', \auth()->user()->id)
                    ->when($this->organization, fn (Unique $unique) => $unique->ignore($this->organization->id, 'id'))
            ]
        ];
    }

    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;
        $this->name = $this->organization->name;
    }

    public function store(): void
    {
        $this->validate();

        auth()->user()->organizations()->create($this->except('organization'));

        $this->reset();

    }

    public function update(): void
    {
        $this->validate();

        $this->organization->update($this->except('organization'));
    }
}
