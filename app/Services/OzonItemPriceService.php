<?php

namespace App\Services;

use App\Models\OzonItem;
use App\Models\OzonMarket;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;

class OzonItemPriceService
{

    public function __construct(public Supplier $supplier, public OzonMarket $market)
    {
    }

    public function updatePrice(): void
    {
        SupplierReportService::changeMessage($this->supplier, "Кабинет ОЗОН {$this->market->name}: перерасчёт цен");

        OzonItem::query()
            ->whereHas('item', function (Builder $query) {
                $query
                    ->where('updated', true)
                    ->where('supplier_id', $this->supplier->id);
            })
            ->chunk(1000, function ($items){
                $items->each(function (OzonItem $ozonItem) {
                    $ozonItem = $this->recountPriceOzonItem($ozonItem);
                    $ozonItem->save();
                });
            });
    }

    public function recountPriceOzonItem(OzonItem $ozonItem): OzonItem
    {
        $min_price_percent = (float)$this->market->min_price_percent;
        $seller_price_percent = (float)$this->market->seller_price_percent;
        $max_price_percent = (float)$this->market->max_price_percent / 100 + 1;
        $acquiring = (float)$this->market->acquiring;
        $lastMile = (float)$this->market->last_mile;
        $maxMile = (float)$this->market->max_mile;

        $price = $ozonItem->item->price;
        $multiplicity = $ozonItem->item->multiplicity;
        $shipping_processing = $ozonItem->shipping_processing;
        $direct_flow_trans = $ozonItem->direct_flow_trans;
        $sales_percent = $ozonItem->sales_percent;
        $min_price = $ozonItem->min_price;
        $min_price_percent_column = (float)$ozonItem->min_price_percent / 100 + 1;

        $newFormulaOzon = ($price * $multiplicity * $min_price_percent_column + $shipping_processing + $direct_flow_trans)
            * (100 / (100 - ($sales_percent + $acquiring + $lastMile + $min_price_percent)));

        $secondFormula = $newFormulaOzon;

        if ($newFormulaOzon * ($lastMile / 100) >= $maxMile) {
            $secondFormula = ($price * $multiplicity * $min_price_percent_column + $shipping_processing + $direct_flow_trans + $maxMile)
                * (100 / (100 - ($sales_percent + $acquiring + $min_price_percent)));
        }

        $ozonItem->price_min = floor(max($secondFormula, $min_price));

        $newFormulaOzon = ($price * $multiplicity * $min_price_percent_column + $shipping_processing + $direct_flow_trans)
            * (100 / (100 - ($sales_percent + $acquiring + $lastMile + $min_price_percent + $seller_price_percent)));

        $secondFormula = $newFormulaOzon;

        if ($newFormulaOzon * ($lastMile / 100) >= $maxMile) {
            $secondFormula = ($price * $multiplicity * $min_price_percent_column + $shipping_processing + $direct_flow_trans + $maxMile)
                * (100 / (100 - ($sales_percent + $acquiring + $min_price_percent + $seller_price_percent)));
        }

        $ozonItem->price = floor(max($secondFormula, $min_price));

        $ozonItem->price_max = floor($ozonItem->price_min * $max_price_percent);

        if ($this->market->seller_price && $ozonItem->price_seller > 0) {
            $formulaPriceSeller = $ozonItem->price_seller > $price
                ? $ozonItem->price
                : $ozonItem->price_seller - ($ozonItem->price_seller / 100);

            $ozonItem->price = floor(max($formulaPriceSeller, $ozonItem->price_min));
        }

        return $ozonItem;
    }

    public function updateStock(): void
    {
        SupplierReportService::changeMessage($this->supplier, "Кабинет ОЗОН {$this->market->name}: перерасчёт остатков");

        OzonItem::query()
            ->whereHas('item', function (Builder $query) {
                $query
                    ->where('updated', true)
                    ->where('supplier_id', $this->supplier->id);
            })
            ->chunk(1000, function ($items){
                $items->each(function (OzonItem $ozonItem) {
                    $ozonItem = $this->recountStockOzonItem($ozonItem);
                    $ozonItem->save();
                });
            });

        $this->nullNotUpdatedStocks();
    }

    public function recountStockOzonItem(OzonItem $ozonItem): OzonItem
    {
        $new_count = $ozonItem->item->count < $this->market->min ? 0 : $ozonItem->item->count;
        $new_count = ($new_count >= $this->market->min && $new_count <= $this->market->max && $ozonItem->item->multiplicity = 1) ? 1 : $new_count;
        $new_count = $new_count / $ozonItem->item->multiplicity;
        $new_count = $new_count > $this->market->max_count ? $this->market->max_count : $new_count;
        $new_count = (int) max($new_count, 0);

        $ozonItem->count = $new_count;

        return $ozonItem;
    }

    public function nullNotUpdatedStocks(): void
    {
        OzonItem::query()
            ->whereHas('item', function (Builder $query) {
                $query
                    ->where('updated', false)
                    ->where('supplier_id', $this->supplier->id);
            })
            ->update(['count' => 0]);
    }
}
