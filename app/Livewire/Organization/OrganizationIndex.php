<?php

namespace App\Livewire\Organization;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\Organization\OrganizationPostForm;
use App\Models\Organization;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Title;

#[Title('Организации')]
class OrganizationIndex extends BaseComponent
{

    public OrganizationPostForm $form;

    public function store(): void
    {
        $this->authorize('create', Organization::class);

        $this->form->store();

    }


    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.organization.organization-index', [
            'organizations' => auth()->user()->organizations
        ]);
    }
}
