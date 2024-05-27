<?php

namespace App\Services;

use App\Exports\OzonItemsExport;
use App\HttpClient\OzonClient;
use App\Imports\OzonItemsImport;
use App\Models\Item;
use App\Models\OzonItem;
use App\Models\OzonMarket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Excel;

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
        return collect(['correct' => $import->correct, 'error' => $import->error]);
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

    public function directRelationships(Collection $defaultFields): Collection
    {
        $defaultFields = $defaultFields->filter();

        $correct = 0;
        $error = 0;

        $client = new OzonClient($this->market->api_key, $this->market->client_id);

        $lastId = "";

        do {

            $result = Cache::tags(['ozon', 'direct_relation'])->remember($this->market->id . '_' . $lastId, now()->addDay(), function () use ($client, $lastId) {
                return $client->getProductList($lastId);
            });

            $lastId = $result->get('last_id');

            $result->get('items')->each(function (array $ozonItem) use ($defaultFields, &$correct, &$error) {

                $commissions = collect($ozonItem['commissions']);
                $price = collect($ozonItem['price']);
                $priceIndexes = collect($ozonItem['price_indexes']);

                try {
                    $item = Item::where('code', $ozonItem['offer_id'])->where('user_id', $this->market->user_id)->firstOrFail();
                } catch (ModelNotFoundException) {
                    try {
                        $item = OzonItem::where('offer_id', $ozonItem['offer_id'])->where('ozon_market_id', $this->market->id)->firstOrFail()->item;
                    } catch (ModelNotFoundException) {

                        $error++;

                        MarketItemRelationshipService::handleNotFoundItem($ozonItem['offer_id'], $this->market->id, 'App\Models\OzonMarket');

                        return;
                    }
                }

                $correct++;

                MarketItemRelationshipService::handleFoundItem($ozonItem['offer_id'], $item->code, $this->market->id, 'App\Models\OzonMarket');

                OzonItem::updateOrCreate([
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
                    'item_id' => $item->id,
                    'min_price_percent' => $defaultFields->get('min_price_percent'),
                    'min_price' => $defaultFields->get('min_price'),
                    'shipping_processing' => $defaultFields->get('shipping_processing'),
                ]);
            });

            ItemsImportReportService::flush($this->market, $correct, $error);

        } while ($lastId);

        return collect(['correct' => $correct, 'error' => $error]);

    }

    public function getWarehouses(): Collection
    {
        return Cache::tags(['ozon', 'warehouses'])->remember($this->market->id, now()->addHours(8), function (){
            $client = new OzonClient($this->market->api_key, $this->market->client_id);
            return $client->getWarehouses();
        });
    }
}
