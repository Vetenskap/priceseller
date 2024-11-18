<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities;

use Illuminate\Support\Collection;

class Counterparty extends Entity
{
    const ENDPOINT = '/entity/counterparty/';

    protected ?string $name = null;

    public function __construct(?Collection $counterparty = null)
    {
        if ($counterparty) {
            $this->set($counterparty);
        }
    }

    protected function set(Collection $counterparty): void
    {
        $this->data = $counterparty;
        $this->id = $counterparty->get('id');
        $this->name = $counterparty->get('name');
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name
        ];
    }


}
