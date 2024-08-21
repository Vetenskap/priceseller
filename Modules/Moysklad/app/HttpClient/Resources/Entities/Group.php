<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities;

use Illuminate\Support\Collection;

class Group extends Entity
{
    const ENDPOINT = '/entity/group/';

    public function __construct(?Collection $group = null)
    {
        if ($group) {

            $this->set($group);
        }
    }

    protected function set(Collection $group): void
    {

    }

}
