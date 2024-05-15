<?php

namespace App\Services;

use App\Exports\WbItemsExport;
use App\HttpClient\WbClient;
use App\HttpClient\WbExternalClient;
use App\Imports\WbItemsImport;
use App\Models\Item;
use App\Models\WbItem;
use App\Models\WbMarket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WbMarketService
{
    public function __construct(public WbMarket $market)
    {
    }

    public function importItems(string $path): Collection
    {
        $import = new WbItemsImport($this->market);
        \Excel::import($import, $path, 'public');

        return collect(['correct' => $import->correct, 'error' => $import->error]);
    }

    public function exportItems(): string
    {
        $uuid = Str::uuid();
        \Excel::store(new WbItemsExport($this->market), "users/wb/$uuid.xlsx", 'public');
        return "users/wb/$uuid.xlsx";
    }

    public function directRelationships(Collection $defaultFields): Collection
    {

        return DB::transaction(function () use ($defaultFields) {

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

                    $correct++;

                    MarketItemRelationshipService::handleFoundItem($wbItem['vendorCode'], $item->code, $this->market->id, 'App\Models\WbMarket');

                    $sku = collect(collect($wbItem['sizes'])->first(fn(array $size) => isset($size['skus'])))->first();

//                    /** @var Collection $info */
//                    $info = Cache::tags(['wb', 'external_card'])
//                        ->remember(
//                            $wbItem['nmID'],
//                            now()->addDay(),
//                            fn() => $externalClient->getCardDetail($wbItem['nmID'])
//                        );

                    WbItem::updateOrCreate([
                        'vendor_code' => $wbItem['vendorCode'],
                        'wb_market_id' => $this->market->id,
                    ], [
                        'vendor_code' => $wbItem['vendorCode'],
                        'nm_id' => $wbItem['nmID'],
                        'sku' => $sku,
                        'wb_market_id' => $this->market->id,
                        'item_id' => $item->id,
//                        'volume' => $info->get('volume') / 10,
//                        'sales_percent' => $info->get('sale'),
                        'min_price' => $defaultFields->get('min_price'),
                        'retail_markup_percent' => $defaultFields->get('retail_markup_percent'),
                        'package' => $defaultFields->get('package'),
//                        'price_market' => $info->get('priceU') / 100
                    ]);
                });

            } while ($total === 100);

            return collect(['error' => $error, 'correct' => $correct]);

        }, 3);
    }
}
