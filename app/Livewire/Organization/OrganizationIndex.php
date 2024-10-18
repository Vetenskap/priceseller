<?php

namespace App\Livewire\Organization;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\Organization\OrganizationPostForm;
use App\Livewire\Traits\WithSort;
use App\Models\Organization;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Title('Организации')]
class OrganizationIndex extends BaseComponent
{
    use WithSort, WithPagination;

    public OrganizationPostForm $form;

    public function destroy($id): void
    {
        $this->form->setOrganization(Organization::findOrFail($id));
        $this->form->destroy();

        $this->addSuccessDeleteNotification();
    }

    #[Computed]
    public function organizations()
    {
        return $this->currentUser()
            ->organizations()
            ->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate();

    }

    public function store(): void
    {
        $this->authorizeForUser($this->user(), 'create', Organization::class);

        $this->form->store();

        \Flux::modal('create-organization')->close();

    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.organization.organization-index');
    }
}
