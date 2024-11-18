<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities;

use Illuminate\Support\Collection;

class Employee extends Entity
{
    const ENDPOINT = '/entity/employee/';

    public function __construct(?Collection $employee = null)
    {
        if ($employee) {

            $this->set($employee);
        }
    }

    protected function set(Collection $employee): void
    {

    }

    public function toArray(): array
    {
        return [];
    }

}
