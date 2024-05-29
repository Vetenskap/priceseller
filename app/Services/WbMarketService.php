<?php

namespace App\Services;

use App\Exports\WbItemsExport;
use App\HttpClient\WbClient;
use App\HttpClient\WbExternalClient;
use App\Imports\WbItemsImport;
use App\Models\Item;
use App\Models\WbItem;
use App\Models\WbMarket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        return collect(['correct' => $import->correct, 'error' => $import->error]);
    }

    public function exportItems(): string
    {
        $uuid = Str::uuid();
        \Excel::store(new WbItemsExport($this->market), self::PATH . "$uuid.xlsx", 'public');
        return $uuid;
    }

    public function directRelationships(Collection $defaultFields): Collection
    {
        $defaultFields = $defaultFields->filter();

        $correct = 0;
        $error = 0;

        $client = new WbClient($this->market->api_key);
        $externalClient = new WbExternalClient();

        $updatedAt = "";
        $nmId = 0;

        do {

            $result = Cache::tags(['wb', 'direct_relation'])
                ->remember(
                    $this->market->id . '_' . $updatedAt . '_' . $nmId,
                    now()->addDay(),
                    fn() => $client->getCardsList($updatedAt, $nmId)
                );

            $updatedAt = $result->get('cursor')->get('updatedAt');
            $nmId = $result->get('cursor')->get('nmID');
            $total = $result->get('cursor')->get('total');

            $result->get('cards')->each(function (array $wbItem) use ($externalClient, $defaultFields, &$error, &$correct) {

                try {
                    $item = Item::where('code', $wbItem['vendorCode'])->where('user_id', $this->market->user_id)->firstOrFail();
                } catch (ModelNotFoundException) {
                    try {
                        $item = WbItem::where('vendor_code', $wbItem['vendorCode'])->where('wb_market_id', $this->market->id)->firstOrFail()->item;
                    } catch (ModelNotFoundException) {

                        $error++;

                        MarketItemRelationshipService::handleNotFoundItem($wbItem['vendorCode'], $this->market->id, 'App\Models\WbMarket');

                        return;
                    }
                }

                $sku = collect(collect($wbItem['sizes'])->first(fn(array $size) => isset($size['skus'])))->first();

//                    /** @var Collection $info */
//                    $info = Cache::tags(['wb', 'external_card'])
//                        ->remember(
//                            $wbItem['nmID'],
//                            now()->addDay(),
//                            fn() => $externalClient->getCardDetail($wbItem['nmID'])
//                        );

                if (
                    WbItem::where(function (Builder $query) use ($wbItem, $sku) {
                        $query->where('nm_id', $wbItem['nmID'])
                            ->orWhere('sku', $sku);

                    })
                        ->whereNot('vendor_code', $wbItem['vendorCode'])
                        ->whereNot('wb_market_id', $this->market->id)
                        ->exists()
                ) {
                    MarketItemRelationshipService::handleItemWithMessage(
                        externalCode: $wbItem['vendorCode'],
                        marketId: $this->market->id,
                        marketType: 'App\Models\WbMarket',
                        code: $wbItem['vendorCode'],
                        message: "Уже существует такой nmID или sku"
                    );

                    $error++;

                    return;
                }

                $correct++;

                MarketItemRelationshipService::handleFoundItem($wbItem['vendorCode'], $item->code, $this->market->id, 'App\Models\WbMarket');

                $newWbItem = WbItem::updateOrCreate([
                    'vendor_code' => $wbItem['vendorCode'],
                    'wb_market_id' => $this->market->id,
                ], [
                    'vendor_code' => $wbItem['vendorCode'],
                    'nm_id' => $wbItem['nmID'],
                    'sku' => $sku,
                    'wb_market_id' => $this->market->id,
                    'item_id' => $item->id,
                ]);

                $defaultFields->each(function ($value, $key) use ($newWbItem) {
                    $newWbItem->{$key} = $value;
                });

                $newWbItem->save();
            });

            ItemsImportReportService::flush($this->market, $correct, $error);

        } while ($total === 100);

        return collect(['error' => $error, 'correct' => $correct]);
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
}
