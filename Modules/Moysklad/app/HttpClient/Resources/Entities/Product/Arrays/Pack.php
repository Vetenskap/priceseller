<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities\Product\Arrays;

use Illuminate\Support\Collection;
use Modules\Moysklad\HttpClient\Resources\Entities\Uom;

class Pack
{
    public ?string $id = null;
    public ?int $quantity = null;
    public ?Uom $uom = null;

    public function __construct(Collection $pack)
    {
        $this->id = $pack->get('id');
        $this->quantity = $pack->get('quantity');

        $uom = new Uom();
        $uom->setId(collect($pack->get('uom'))->toCollectionSpread()->get('meta')->get('href'));
        $this->uom = $uom;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'uom' => $this->uom?->toArray()
        ];
    }
}
