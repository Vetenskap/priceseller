<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities;

use Illuminate\Support\Collection;

class Organization extends Entity
{
    const ENDPOINT = '/entity/organization/';

    protected ?string $name = null;

    public function __construct(?Collection $organization = null)
    {
        if ($organization) {
            $this->set($organization);
        }
    }

    protected function set(Collection $organization): void
    {
        $this->data = $organization;
        $this->id = $organization->get('id');
        $this->name = $organization->get('name');
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}
