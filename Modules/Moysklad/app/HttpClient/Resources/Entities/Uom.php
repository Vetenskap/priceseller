<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities;

use Illuminate\Support\Collection;

class Uom extends Entity
{
    const ENDPOINT = '/entity/uom/';

    protected string $accountId;
    protected ?string $code = null;
    protected ?string $description = null;
    protected string $externalCode;
    protected string $name;
    protected bool $shared;
    protected string $updated;

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

}
