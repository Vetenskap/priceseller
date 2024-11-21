<?php

namespace App\Services;

use App\Exports\WbItemsExport;
use App\HttpClient\WbClient\Resources\Card\Card;
use App\HttpClient\WbClient\Resources\Card\CardList;
use App\HttpClient\WbClient\Resources\Tariffs\Commission;
use App\HttpClient\WbClient\WbClient;
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

        $commissions = new Commission();
        $commissions->fetch($this->market->api_key);

        $list = new CardList($this->market->api_key);

        do {

            $cards = $list->next();

            $cards->each(function (Card $card) use (&$updated, $defaultFields, $commissions) {

                $wbItem = $this->market->items()->where('nm_id', $card->getNmId())->first();

                if ($wbItem) {
                    $wbItem->update(array_merge([
                        'volume' => round($card->getDimensionsLength() * $card->getDimensionsWidth() * $card->getDimensionsHeight() / 1000, 2),
                        'subject_id' => $card->getSubjectId(),
                        'sales_percent' => $commissions->getReport()->firstWhere('subjectID', $card->getSubjectId())?->get($this->market->tariff)
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

        $commissions = new Commission();
        $commissions->fetch($this->market->api_key);

        $defaultFields = $defaultFields->filter();

        $correct = 0;
        $error = 0;
        $updated = 0;

        $list = new CardList($this->market->api_key);

        do {

            $cards = $list->next();

            $cards->each(function (Card $card) use (&$updated, $defaultFields, $directLink, &$error, &$correct, $commissions) {

                if ($directLink) {
                    try {
                        $item = $this->market->user->items()->where('code', $card->getVendorCode())->first();
                        if (!$item) {
                            $item = $this->market->user->bundles()->where('code', $card->getVendorCode())->firstOrFail();
                        }
                    } catch (ModelNotFoundException) {
                        $error++;

                        MarketItemRelationshipService::handleNotFoundItem($card->getVendorCode(), $this->market->id, 'App\Models\WbMarket');

                        return;
                    }
                } else {
                    try {
                        $item = $this->market->items()->where('vendor_code', $card->getVendorCode())->firstOrFail()->itemable;
                    } catch (ModelNotFoundException) {

                        $error++;

                        MarketItemRelationshipService::handleNotFoundItem($card->getVendorCode(), $this->market->id, 'App\Models\WbMarket');

                        return;
                    }
                }

                $sku = $card->getSizes()->first(fn(Collection $size) => $size->get('skus'))->get('skus')->first();

                MarketItemRelationshipService::handleFoundItem($card->getVendorCode(), $item->code, $this->market->id, 'App\Models\WbMarket');

                if (WbItem::where('vendor_code', $card->getVendorCode())->where('wb_market_id', $this->market->id)->exists()) {
                    $updated++;
                } else {
                    $correct++;
                }

                WbItem::updateOrCreate([
                    'vendor_code' => $card->getVendorCode(),
                    'wb_market_id' => $this->market->id,
                ], array_merge([
                    'vendor_code' => $card->getVendorCode(),
                    'nm_id' => $card->getNmId(),
                    'sku' => $sku,
                    'wb_market_id' => $this->market->id,
                    'wbitemable_id' => $item->id,
                    'wbitemable_type' => $item->getMorphClass(),
                    'volume' => round($card->getDimensionsLength() * $card->getDimensionsWidth() * $card->getDimensionsHeight() / 1000, 2),
                    'subject_id' => $card->getSubjectId(),
                    'sales_percent' => $commissions->getReport()->firstWhere('subjectID', $card->getSubjectId())?->get($this->market->tariff)
                ], $defaultFields->toArray()));

            });

            ItemsImportReportService::flush($this->market, $correct, $error, $updated);

        } while ($list->hasNext());

        return collect(['error' => $error, 'correct' => $correct, 'updated' => $updated]);
    }

    public function getWarehouses(): Collection
    {
        $client = new WbClient($this->market->api_key);

        return $client->getWarehouses();

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
