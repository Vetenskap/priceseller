<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities\Product\Arrays;

use Illuminate\Support\Collection;
use Modules\Moysklad\HttpClient\Entities\Uom;

class Pack
{
    public string $id;
    public int $quantity;
    public Uom $uom;

    public function __construct(Collection $pack)
    {
        $this->id = $pack->get('id');
        $this->quantity = $pack->get('quantity');

        $uom = new Uom();
        $uom->setId(collect($pack->get('uom'))->toCollectionSpread()->get('meta')->get('href'));
        $this->uom = $uom;
    }
}
