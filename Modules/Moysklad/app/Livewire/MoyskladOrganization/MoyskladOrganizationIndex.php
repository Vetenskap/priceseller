<?php

namespace Modules\Moysklad\Livewire\MoyskladOrganization;

use App\Livewire\Traits\WithJsNotifications;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Moysklad\Livewire\Forms\MoyskladOrganization\MoyskladOrganizationPostForm;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Services\MoyskladService;

class MoyskladOrganizationIndex extends Component
{
    use WithJsNotifications, WithPagination;

    public MoyskladOrganizationPostForm $form;

    public Moysklad $moysklad;

    #[Computed]
    public function organizations(): LengthAwarePaginator
    {
        return $this->moysklad
            ->organizations()
            ->paginate();
    }

    public function store(): void
    {
        $this->form->store();

        \Flux::modal('create-moysklad-organization')->close();
    }

    public function mount(): void
    {
        $this->form->setMoysklad($this->moysklad);
    }

    public function destroy($id): void
    {
        $organization = $this->moysklad->organizations()->find($id);
        $organization->delete();
    }

    public function render()
    {
        return view('moysklad::livewire.moysklad-organization.moysklad-organization-index', [
            'moyskladOrganizations' => (new MoyskladService($this->moysklad))->getAllOrganizations()
        ]);
    }
}
