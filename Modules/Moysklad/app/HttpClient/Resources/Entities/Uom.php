<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities;

use Illuminate\Support\Collection;

class Uom extends Entity
{
    const ENDPOINT = '/entity/uom/';

    protected ?string $accountId = null;
    protected ?string $code = null;
    protected ?string $description = null;
    protected ?string $externalCode = null;
    protected ?string $name = null;
    protected ?bool $shared = null;
    protected ?string $updated = null;

    public function __construct(?Collection $uom = null)
    {
        if ($uom) {
            $this->set($uom);
        }
    }

    protected function set(Collection $uom): void
    {
        $this->accountId = $uom->get('accountId');
        $this->code = $uom->get('code');
        $this->description = $uom->get('description');
        $this->externalCode = $uom->get('externalCode');
        $this->name = $uom->get('name');
        $this->shared = $uom->get('shared');
        $this->updated = $uom->get('updated');
    }

    public function toArray(): array
    {
        return [
            'accountId' => $this->accountId,
            'code' => $this->code,
            'description' => $this->description,
            'externalCode' => $this->externalCode,
            'name' => $this->name,
            'shared' => $this->shared,
            'updated' => $this->updated
        ];
    }

}
