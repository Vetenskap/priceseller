<?php

namespace Modules\EditorContent\Services;

use App\HttpClient\OzonClient\Resources\ProductInfoAttribute;
use App\Models\Bundle;
use App\Models\OzonItem;
use App\Models\User;
use Illuminate\Support\Collection;

class EditorContentService
{
    public function __construct(public User $user)
    {

    }

    public function getOzonProductsInfo(string $code, bool $bundle = false): Collection
    {
        if ($bundle) {
            $item = $this->user->bundles()->where('code', $code)->first();
        } else {
            $item = $this->user->items()->where('code', $code)->first();
        }

        return $item->ozonItems->map(function (OzonItem $ozonItem) {
            $productInfoAttribute = new ProductInfoAttribute();
            $productInfoAttribute->fetch($ozonItem->market, $ozonItem->product_id, $ozonItem->offer_id);
            return $productInfoAttribute;
        });
    }
}
