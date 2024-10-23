<?php

namespace App\Services;

use App\Exports\OzonItemsExport;
use App\HttpClient\OzonClient\OzonClient;
use App\HttpClient\OzonClient\Resources\ProductInfoPrices;
use App\Imports\OzonItemsImport;
use App\Models\OzonItem;
use App\Models\OzonMarket;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class OzonMarketService
{

    const PATH = "users/ozon/";

    public function __construct(public OzonMarket $market)
    {
    }

    public function importItems(string $uuid, string $ext): Collection
    {
        $import = new OzonItemsImport($this->market);
        \Excel::import($import, self::PATH . $uuid . '.' . $ext, 'public');
        return collect([
            'correct' => $import->correct,
            'error' => $import->error,
            'updated' => $import->updated,
            'deleted' => $import->deleted,
        ]);
    }

    public function exportItems(): string
    {
        $uuid = Str::uuid();
        \Excel::store(new OzonItemsExport($this->market), self::PATH . "$uuid.xlsx", 'public');
        return $uuid;
    }

    public function clearRelationships()
    {
        $limit = 1000;
        $totalDeleted = 0;

        do {
            $deleted = $this->market->items()->limit($limit)->delete();
            $totalDeleted += $deleted;
        } while ($deleted > 0);

        return $totalDeleted;

    }

    public function updateApiCommissions(Collection $defaultFields): Collection
    {
        $defaultFields->filter()->each(fn($value, $key) => $this->market->items()->update([$key => $value]));;

        $updated = 0;

        $this->market->items()->chunk(1000, function (Collection $items) use (&$updated) {

            $productIdToOzonItem = $items->pluck(null, 'product_id');

            $productsInfoPrices = ProductInfoPrices::fetchAll($this->market, $productIdToOzonItem->keys()->toArray());

            $productsInfoPrices->each(function (ProductInfoPrices $productInfoPrices) use ($productIdToOzonItem, &$updated) {
                $ozonItem = $productIdToOzonItem->get($productInfoPrices->getProductId());
                if ($ozonItem) {
                    $ozonItem->update([
                        'direct_flow_trans' => (float)$productInfoPrices->getCommissions()->get('fbs_direct_flow_trans_max_amount'),
                        'deliv_to_customer' => (float)$productInfoPrices->getCommissions()->get('fbs_deliv_to_customer_amount'),
                        'sales_percent' => (int)$productInfoPrices->getCommissions()->get('sales_percent_fbs'),
                        'price_market' => (int)$productInfoPrices->getPrice()->get('price'),
                        'price_seller' => (int)$productInfoPrices->getPriceIndexes()->get('ozon_index_data')->get('minimal_price'),
                    ]);
                    $updated++;
                }
            });

            ItemsImportReportService::flush($this->market, 0, 0, $updated);

        });

        return collect([
            'correct' => 0,
            'error' => 0,
            'updated' => $updated,
        ]);

    }

    public function directRelationships(Collection $defaultFields, bool $directLink = false): Collection
    {
        $this->market->clearSuppliersCache();

        $defaultFields = $defaultFields->filter();

        $correct = 0;
        $error = 0;
        $updated = 0;

        $client = new OzonClient($this->market->api_key, $this->market->client_id);

        $lastId = "";

        do {

            $result = Cache::tags(['ozon', 'direct_relation'])->remember($this->market->id . '_' . $lastId, now()->addDay(), function () use ($client, $lastId) {
                return $client->getProductList($lastId);
            });

            $lastId = $result->get('last_id');

            $result->get('items')->each(function (array $ozonItem) use ($defaultFields, &$correct, &$error, &$updated, $directLink) {

                $commissions = collect($ozonItem['commissions']);
                $price = collect($ozonItem['price']);
                $priceIndexes = collect($ozonItem['price_indexes']);

                if ($directLink) {

                    try {
                        $item = $this->market->user->items()->where('code', $ozonItem['offer_id'])->first();

                        if (!$item) {
                            $item = $this->market->user->bundles()->where('code', $ozonItem['offer_id'])->firstOrFail();
                        }
                    } catch (ModelNotFoundException) {

                        $error++;

                        MarketItemRelationshipService::handleNotFoundItem($ozonItem['offer_id'], $this->market->id, 'App\Models\OzonMarket');

                        return;

                    }

                } else {

                    try {
                        $item = $this->market->items()->where('offer_id', $ozonItem['offer_id'])->firstOrFail()->ozonitemable;
                    } catch (ModelNotFoundException) {

                        $error++;

                        MarketItemRelationshipService::handleNotFoundItem($ozonItem['offer_id'], $this->market->id, 'App\Models\OzonMarket');

                        return;
                    }

                }

                MarketItemRelationshipService::handleFoundItem($ozonItem['offer_id'], $item->code, $this->market->id, 'App\Models\OzonMarket');

                if (OzonItem::where('offer_id', $ozonItem['offer_id'])->where('ozon_market_id', $this->market->id)->exists()) {
                    $updated++;
                } else {
                    $correct++;
                }

                $newOzonItem = OzonItem::updateOrCreate([
                    'offer_id' => $ozonItem['offer_id'],
                    'ozon_market_id' => $this->market->id,
                ], [
                    'offer_id' => $ozonItem['offer_id'],
                    'product_id' => $ozonItem['product_id'],
                    'direct_flow_trans' => (float)$commissions->get('fbs_direct_flow_trans_max_amount'),
                    'deliv_to_customer' => (float)$commissions->get('fbs_deliv_to_customer_amount'),
                    'sales_percent' => (int)$commissions->get('sales_percent_fbs'),
                    'price_market' => (int)$price->get('price'),
                    'price_seller' => (int)collect($priceIndexes->get('ozon_index_data'))->get('minimal_price'),
                    'ozon_market_id' => $this->market->id,
                    'ozonitemable_id' => $item->id,
                    'ozonitemable_type' => $item->getMorphClass(),
                ]);

                $defaultFields->each(function ($value, $key) use ($newOzonItem) {
                    $newOzonItem->{$key} = $value;
                });

                $newOzonItem->save();

            });

            ItemsImportReportService::flush($this->market, $correct, $error, $updated);

        } while ($lastId);

        return collect([
            'correct' => $correct,
            'error' => $error,
            'updated' => $updated,
        ]);

    }

    public function getWarehouses(): Collection
    {
        return Cache::tags(['ozon', 'warehouses'])->remember($this->market->id, now()->addHours(8), function (){
            $client = new OzonClient($this->market->api_key, $this->market->client_id);
            return $client->getWarehouses();
        });
    }

    public static function closeMarkets(User $user)
    {
        $count = $user->ozonMarkets()->count();

        if ($count > 0 && !$user->isOzonFiveSub() && !$user->isOzonTenSub()) {

            $user->ozonMarkets()->where('close', false)->orderBy('created_at')->get()->each(function (OzonMarket $market) {
                $market->close = true;
                $market->open = false;
                $market->save();
            });

        } else {

            $user->ozonMarkets()->orderBy('created_at')->get()->take(5)->where('close', true)->each(function (OzonMarket $market) {
                $market->close = false;
                $market->save();
            });

        }

        if ($count > 5 && !$user->isOzonTenSub()) {

            $user->ozonMarkets()->orderBy('created_at')->get()->skip(5)->where('close', false)->each(function (OzonMarket $market) {
                $market->close = true;
                $market->open = false;
                $market->save();
            });

        } else {

            $user->ozonMarkets()->orderBy('created_at')->get()->skip(5)->where('close', true)->each(function (OzonMarket $market) {
                $market->close = false;
                $market->save();
            });

        }
    }
}
