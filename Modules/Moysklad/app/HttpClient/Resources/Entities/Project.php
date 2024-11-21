<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities;

use Illuminate\Support\Collection;

class Project extends Entity
{
    const ENDPOINT = '/entity/project/';

    public function toArray(): array
    {
        return [
            'id' => $this->id,
        ];
    }
}
