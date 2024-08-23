<?php

namespace Modules\Moysklad\Livewire\Forms\MoyskladOrganization;

use Livewire\Attributes\Validate;
use Livewire\Form;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladOrganizationOrganization;

class MoyskladOrganizationPostForm extends Form
{
    public Moysklad $moysklad;
    public ?MoyskladOrganizationOrganization $moyskladOrganization = null;

    #[Validate]
    public $moysklad_organization_uuid;
    #[Validate]
    public $organization_id;

    public function setMoysklad(Moysklad $moysklad): void
    {
        $this->moysklad = $moysklad;
    }

    public function setMoyskladOrganization(MoyskladOrganizationOrganization $moyskladOrganization): void
    {
        $this->moyskladOrganization = $moyskladOrganization;
        $this->moysklad_organization_uuid = $moyskladOrganization->moysklad_organization_uuid;
        $this->organization_id = $moyskladOrganization->organization_id;
    }

    public function rules(): array
    {
        return [
            'moysklad_organization_uuid' => ['required', 'uuid'],
            'organization_id' => ['required', 'uuid'],
        ];
    }

    public function store(): void
    {
        $this->validate();

        $this->moysklad->organizations()->create($this->except(['moysklad', 'moyskladOrganization']));

        $this->reset(['moysklad_organization_uuid', 'organization_id']);
    }

    public function update(): void
    {
        $this->validate();

        $this->moyskladOrganization->update($this->except(['moysklad', 'moyskladOrganization']));
    }

    public function destroy(): void
    {
        $this->moyskladOrganization->delete();
    }

}
