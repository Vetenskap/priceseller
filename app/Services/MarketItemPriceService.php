<?php

namespace App\Services;

use App\Contracts\MarketItemPriceContract;
use App\HttpClient\OzonClient\OzonClient;
use App\HttpClient\WbClient\WbClient;
use App\Models\Bundle;
use App\Models\Item;
use App\Models\OzonItem;
use App\Models\OzonMarket;
use App\Models\ReportLog;
use App\Models\Supplier;
use App\Models\User;
use App\Models\WbItem;
use App\Models\WbMarket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

class MarketItemPriceService implements MarketItemPriceContract
{
    public User $user;
    public WbMarket|OzonMarket $market;
    public Supplier $supplier;
    public ReportLog $log;

    public function make(Supplier $supplier, WbMarket|OzonMarket $market, ReportLog $log): void
    {
        $this->user = $market->user;
        $this->market = $market;
        $this->supplier = $supplier;
        $this->log = $log;
    }

    public function updatePrice(): void
    {
        $log = SupplierReportLogMarketService::new($this->log, 'Обновление цен');

        try {
            $this->market
                ->items()
                ->where(fn(Builder $query) => $this->filteredData($query))
                ->with(['itemable'])
                ->lazy()
                ->each(function (WbItem|OzonItem $item) {
                    $this->recountPrice($item);
                });
        } catch (\Throwable $th) {
            report($th);
            SupplierReportLogMarketService::failed($log);
            return;
        }

        SupplierReportLogMarketService::finished($log);
    }

    public function recountPrice(OzonItem|WbItem $item): void
    {
        [$price, $multiplicity] = $this->getPriceAndMultiplicity($item);
        $this->applyMarketSpecificPricing($item, $price, $multiplicity);
    }

    public function getPriceAndMultiplicity(OzonItem|WbItem $item): array
    {
        $type = $item instanceof WbItem ? $item->wbitemable_type : $item->ozonitemable_type;

        if ($type === 'App\Models\Item') {

            $multiplicity = $item->itemable->multiplicity;

            if ($this->user->baseSettings?->enabled_use_buy_price_reserve && !$item->itemable->price) {
                $price = $item->itemable->buy_price_reserve;
            } else {
                $price = $item->itemable->price;
            }

        } else {

            $multiplicity = 1;

            $price = $item->itemable->items->map(function (Item $item) {
                if ($this->user->baseSettings?->enabled_use_buy_price_reserve && !$item->price) {
                    return $item->buy_price_reserve * $item->pivot->multiplicity;
                } else {
                    return $item->price * $item->pivot->multiplicity;
                }
            })->sum();
        }

        return [$price, $multiplicity];
    }

    public function applyMarketSpecificPricing(WbItem|OzonItem $item, float $price, int $multiplicity): void
    {
        if ($this->market instanceof WbMarket) {
            $item->price = $this->calculateWbPrice($item, $price, $multiplicity);
        } else {
            [$priceMarket, $priceMin, $priceMax] = $this->calculateOzonPrices($item, $price, $multiplicity);
            $item->price = $priceMarket;
            $item->price_min = $priceMin;
            $item->price_max = $priceMax;
        }

        $item->save();
    }

    public function calculateWbPrice(WbItem $item, float $price, int $multiplicity): float
    {
        $coefficient = (float)$this->market->coefficient;
        $basicLogistics = (int)$this->market->basic_logistics;
        $priceOneLiter = (int)$this->market->price_one_liter;
        $volume = (int)$this->market->volume;

        $volumeColumn = $item->volume;
        $retailMarkupPercent = $item->retail_markup_percent / 100 + 1;
        $package = $item->package;
        $salesPercent = $item->sales_percent;
        $minPrice = $item->min_price;

        if ($volumeColumn < $volume) {
            $liter = $basicLogistics;
        } else {
            $liter = $basicLogistics + (($volumeColumn - $volume) * $priceOneLiter);
        }

        $formula = (((($price * $multiplicity * $retailMarkupPercent) + $package + $liter) * 100 / (100 - $salesPercent))) * $coefficient;

        return floor(max($formula, $minPrice));
    }

    public function calculateOzonPrices(OzonItem $item, float $price, int $multiplicity): array
    {
        $min_price_percent = ((float)$this->market->min_price_coefficient - 1) * 100;
        $seller_price_percent = (float)$this->market->seller_price_percent;
        $max_price_percent = (float)$this->market->max_price_percent / 100 + 1;
        $acquiring = (float)$this->market->acquiring;
        $lastMile = (float)$this->market->last_mile;
        $maxMile = (float)$this->market->max_mile;

        $shipping_processing = $item->shipping_processing;
        $direct_flow_trans = $item->direct_flow_trans;
        $sales_percent = $item->sales_percent;
        $min_price = $item->min_price;
        $min_price_percent_column = (float)$item->min_price_percent / 100 + 1;

        $newFormulaOzon = ($price * $multiplicity * $min_price_percent_column + $shipping_processing + $direct_flow_trans)
            * (100 / (100 - ($sales_percent + $acquiring + $lastMile + $min_price_percent)));

        $secondFormula = $newFormulaOzon;

        if ($newFormulaOzon * ($lastMile / 100) >= $maxMile) {
            $secondFormula = ($price * $multiplicity * $min_price_percent_column + $shipping_processing + $direct_flow_trans + $maxMile)
                * (100 / (100 - ($sales_percent + $acquiring) + $min_price_percent));
        }

        $priceMin = floor(max($secondFormula, $min_price));

        $newFormulaOzon = ($price * $multiplicity * $min_price_percent_column + $shipping_processing + $direct_flow_trans)
            * (100 / (100 - ($sales_percent + $acquiring + $lastMile + $seller_price_percent + $min_price_percent)));

        $secondFormula = $newFormulaOzon;

        if ($newFormulaOzon * ($lastMile / 100) >= $maxMile) {
            $secondFormula = ($price * $multiplicity * $min_price_percent_column + $shipping_processing + $direct_flow_trans + $maxMile)
                * (100 / (100 - ($sales_percent + $acquiring + $seller_price_percent + $min_price_percent)));
        }

        $priceMax = floor($priceMin * $max_price_percent);

        if ($this->market->seller_price && $item->price_seller > 0) {
            $formulaPriceSeller = $item->price_seller > $secondFormula
                ? $secondFormula
                : $item->price_seller - ($item->price_seller / 100);

            return [floor(max($formulaPriceSeller, $priceMin)), $priceMin, $priceMax];
        }

        return [floor(max($secondFormula, $min_price)), $priceMin, $priceMax];
    }

    public function unloadAllPrices(): void
    {
        if (!$this->market->enabled_price) {
            $log = SupplierReportLogMarketService::new($this->log, 'Пропускаем выгрузку цен');
            SupplierReportLogMarketService::failed($log);
            return;
        }

        $log = SupplierReportLogMarketService::new($this->log, 'Выгрузка цен');

        try {

            if ($this->market instanceof WbMarket) {
                $this->market
                    ->items()
                    ->with('itemable')
                    ->whereNotNull('volume')
                    ->whereNotNull('retail_markup_percent')
                    ->whereNotNull('package')
                    ->whereNotNull('sales_percent')
                    ->whereNotNull('min_price')
                    ->whereNotNull('price')
                    ->where('price', '>', 0)
                    ->whereNotNull('nm_id')
                    ->where(fn(Builder $query) => $this->filteredData($query))
                    ->chunk(1000, function (Collection $items) {

                        /** @var Collection $data */
                        $data = $items->map(function (WbItem $item) {

                            return [
                                "nmId" => (int)$item->nm_id,
                                "price" => (int)$item->price
                            ];
                        });

                        if ($data->isNotEmpty()) {
                            if (App::isProduction()) {
                                $wbClient = new WbClient($this->market->api_key);
                                $wbClient->putPrices($data->values(), $this->market, $this->log);
                            } else {
                                SupplierReportLogMarketService::new($this->log, $data->values()->toJson());
                            }
                        }

                    });

            } else {
                $this->market
                    ->items()
                    ->with('itemable')
                    ->whereNotNull('price_min')
                    ->whereNotNull('offer_id')
                    ->whereNotNull('price_max')
                    ->whereNotNull('price')
                    ->where('price', '>', 0)
                    ->whereNotNull('product_id')
                    ->whereNotNull('shipping_processing')
                    ->whereNotNull('direct_flow_trans')
                    ->whereNotNull('sales_percent')
                    ->whereNotNull('min_price')
                    ->whereNotNull('min_price_percent')
                    ->where(fn(Builder $query) => $this->filteredData($query))
                    ->chunk(1000, function (Collection $items) {

                        /** @var Collection $data */
                        $data = $items->map(function (OzonItem $item) {

                            return [
                                "auto_action_enabled" => "UNKNOWN",
                                "currency_code" => "RUB",
                                "min_price" => (string)$item->price_min,
                                "offer_id" => (string)$item->offer_id,
                                "old_price" => (string)$item->price_max,
                                "price" => (string)$item->price,
                                "product_id" => (int)$item->product_id
                            ];
                        });

                        if ($data->isNotEmpty()) {
                            if (App::isProduction()) {
                                $ozonClient = new OzonClient($this->market->api_key, $this->market->client_id);

                                try {
                                    $ozonClient->putPrices($data->values()->all(), $this->supplier);
                                } catch (\Throwable $th) {
                                    report($th);
                                    $log = SupplierReportLogMarketService::new($this->log, 'Ошибка при выгрузке 100 цен: ' . $th->getMessage());
                                    SupplierReportLogMarketService::failed($log);
                                }
                            } else {
                                SupplierReportLogMarketService::new($this->log, $data->values()->toJson());
                            }
                        }
                    });
            }

        } catch (\Throwable $th) {
            report($th);
            SupplierReportLogMarketService::failed($log);
            return;
        }

        SupplierReportLogMarketService::finished($log);
    }

    public function filteredData(Builder $query): Builder
    {
        return $query->whereHasMorph('itemable', [Bundle::class, Item::class], function (Builder $query, $type) {
            if ($type === Bundle::class) {
                $query->whereHas('items', function (Builder $query) {
                    $query
                        ->where('supplier_id', $this->supplier->id)
                        ->when(!$this->user->baseSettings()->exists() || !$this->user->baseSettings->enabled_use_buy_price_reserve, function (Builder $query) {
                            $query->where('updated', true);
                        });
                });
            } else {
                $query->where('supplier_id', $this->supplier->id)
                    ->when(!$this->user->baseSettings()->exists() || !$this->user->baseSettings->enabled_use_buy_price_reserve, function (Builder $query) {
                        $query->where('updated', true);
                    });
            }
        });
    }
}
