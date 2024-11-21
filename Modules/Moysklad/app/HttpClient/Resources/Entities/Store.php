<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities;

use Illuminate\Support\Collection;

class Store extends Entity
{
    const ENDPOINT = '/entity/store/';

    public ?string $name = null;

    public function __construct(?Collection $store = null)
    {
        if ($store) {
            $this->set($store);
        }
    }

    public function set(Collection $store): void
    {
        $this->data = $store;
        $this->id = $store->get('id');
        $this->name = $store->get('name');
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
