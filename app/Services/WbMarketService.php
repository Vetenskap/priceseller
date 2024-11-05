<?php

namespace App\Services;

use App\Exports\WbItemsExport;
use App\HttpClient\WbClient\Resources\Card\Card;
use App\HttpClient\WbClient\Resources\Card\CardList;
use App\HttpClient\WbClient\WbClient;
use App\HttpClient\WbExternalClient;
use App\Imports\WbItemsImport;
use App\Models\User;
use App\Models\WbItem;
use App\Models\WbMarket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class WbMarketService
{
    const PATH = "users/wb/";

    public function __construct(public WbMarket $market)
    {
    }

    public function importItems(string $uuid, string $ext): Collection
    {
        $import = new WbItemsImport($this->market);
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
        \Excel::store(new WbItemsExport($this->market), self::PATH . "$uuid.xlsx", 'public');
        return $uuid;
    }

    public function updateApiCommissions(array $defaultFields): Collection
    {
        $updated = 0;

        $list = new CardList($this->market->api_key);

        do {

            $cards = $list->next();

            $cards->each(function (Card $card) use (&$updated, $defaultFields) {

                $wbItem = $this->market->items()->where('nm_id', $card->getNmId())->first();

                if ($wbItem) {
                    $wbItem->update(array_merge([
                        'volume' => round($card->getDimensionsLength() * $card->getDimensionsWidth() * $card->getDimensionsHeight() / 1000, 2)
                    ], $defaultFields));
                    $updated++;
                }

            });

            ItemsImportReportService::flush($this->market, 0, 0, $updated);

        } while ($list->hasNext());

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

        $client = new WbClient($this->market->api_key);
        $externalClient = new WbExternalClient();

        $updatedAt = "";
        $nmId = 0;

        do {

            $result = Cache::tags(['wb', 'direct_relation'])
                ->remember(
                    $this->market->id . '_' . $updatedAt . '_' . $nmId,
                    now()->addHours(2),
                    fn() => $client->getCardsList($updatedAt, $nmId)
                );

            $updatedAt = $result->get('cursor')->get('updatedAt');
            $nmId = $result->get('cursor')->get('nmID');
            $total = $result->get('cursor')->get('total');

            $result->get('cards')->toCollectionSpread()->each(function (Collection $wbItem) use ($externalClient, $defaultFields, &$error, &$correct, &$updated, $directLink) {

                if ($directLink) {
                    try {
                        $item = $this->market->user->items()->where('code', $wbItem->get('vendorCode'))->first();
                        if (!$item) {
                            $item = $this->market->user->bundles()->where('code', $wbItem->get('vendorCode'))->firstOrFail();
                        }
                    } catch (ModelNotFoundException) {
                        $error++;

                        MarketItemRelationshipService::handleNotFoundItem($wbItem->get('vendorCode'), $this->market->id, 'App\Models\WbMarket');

                        return;
                    }
                } else {
                    try {
                        $item = $this->market->items()->where('vendor_code', $wbItem->get('vendorCode'))->firstOrFail()->wbitemable;
                    } catch (ModelNotFoundException) {

                        $error++;

                        MarketItemRelationshipService::handleNotFoundItem($wbItem->get('vendorCode'), $this->market->id, 'App\Models\WbMarket');

                        return;
                    }
                }

                $sku = $wbItem->get('sizes')->first(fn(Collection $size) => $size->get('skus'))->get('skus')->first();
                $dimensions = $wbItem->get('dimensions');


                MarketItemRelationshipService::handleFoundItem($wbItem->get('vendorCode'), $item->code, $this->market->id, 'App\Models\WbMarket');

                if (WbItem::where('vendor_code', $wbItem->get('vendorCode'))->where('wb_market_id', $this->market->id)->exists()) {
                    $updated++;
                } else {
                    $correct++;
                }

                WbItem::updateOrCreate([
                    'vendor_code' => $wbItem->get('vendorCode'),
                    'wb_market_id' => $this->market->id,
                ], array_merge([
                    'vendor_code' => $wbItem->get('vendorCode'),
                    'nm_id' => $wbItem->get('nmID'),
                    'sku' => $sku,
                    'wb_market_id' => $this->market->id,
                    'wbitemable_id' => $item->id,
                    'wbitemable_type' => $item->getMorphClass(),
                    'volume' => round($dimensions->get('length') * $dimensions->get('width') * $dimensions->get('height') / 1000, 2),
                ], $defaultFields->toArray()));
            });

            ItemsImportReportService::flush($this->market, $correct, $error, $updated);

        } while ($total === 100);

        return collect(['error' => $error, 'correct' => $correct, 'updated' => $updated]);
    }

    public function getWarehouses(): Collection
    {
        $client = new WbClient($this->market->api_key);

        return Cache::tags(['wb', 'warehouses'])
            ->remember(
                $this->market->id,
                now()->addHours(8),
                fn() => $client->getWarehouses()
            );

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

    public static function closeMarkets(User $user)
    {
        $count = $user->wbMarkets()->count();

        if ($count > 0 && !$user->isWbFiveSub() && !$user->isWbTenSub()) {

            $user->wbMarkets()->where('close', false)->orderBy('created_at')->get()->each(function (WbMarket $market) {
                $market->close = true;
                $market->open = false;
                $market->save();
            });

        } else {

            $user->wbMarkets()->orderBy('created_at')->get()->take(5)->where('close', true)->each(function (WbMarket $market) {
                $market->close = false;
                $market->save();
            });

        }

        if ($count > 5 && !$user->isWbTenSub()) {

            $user->wbMarkets()->orderBy('created_at')->get()->skip(5)->where('close', false)->each(function (WbMarket $market) {
                $market->close = true;
                $market->open = false;
                $market->save();
            });

        } else {

            $user->wbMarkets()->orderBy('created_at')->get()->skip(5)->where('close', true)->each(function (WbMarket $market) {
                $market->close = false;
                $market->save();
            });

        }
    }
}
