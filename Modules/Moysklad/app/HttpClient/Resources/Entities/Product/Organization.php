<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities\Product;

use Illuminate\Support\Collection;
use Modules\Moysklad\HttpClient\Resources\Entities\Entity;

class Organization extends Entity
{
    const ENDPOINT = '/entity/organization/';

    protected string $name;

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
}
