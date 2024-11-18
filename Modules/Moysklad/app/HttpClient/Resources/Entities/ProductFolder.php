<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities;

use Illuminate\Support\Collection;

class ProductFolder extends Entity
{
    const ENDPOINT = '/entity/productfolder/';

    public function __construct(?Collection $productFolder = null)
    {
        if ($productFolder) {
            $this->set($productFolder);
        }
    }

    protected function set(Collection $productFolder): void
    {

    }

    public function toArray(): array
    {
        return [];
    }
}
