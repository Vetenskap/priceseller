<?php

namespace App\Services;

use App\Exports\OzonItemsExport;
use App\HttpClient\OzonClient;
use App\Imports\OzonItemsImport;
use App\Models\Item;
use App\Models\OzonItem;
use App\Models\OzonMarket;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Excel;
use function Filament\authorize;

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

    public function directRelationships(Collection $defaultFields): Collection
    {
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

            $result->get('items')->each(function (array $ozonItem) use ($defaultFields, &$correct, &$error, &$updated) {

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
                    'item_id' => $item->id,
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
        if (App::isLocal() || $user->isAdmin()) return;

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
