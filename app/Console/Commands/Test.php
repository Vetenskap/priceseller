<?php

namespace App\Console\Commands;

use App\HttpClient\OzonClient\Resources\DescriptionCategory;
use App\HttpClient\OzonClient\Resources\DescriptionCategoryAttribute;
use App\HttpClient\OzonClient\Resources\DescriptionCategoryTree;
use App\HttpClient\OzonClient\Resources\ProductInfoPrices;
use App\Models\Bundle;
use App\Models\Item;
use App\Models\OzonItem;
use App\Models\OzonMarket;
use App\Models\Supplier;
use App\Models\User;
use App\Models\WbItem;
use App\Models\WbMarket;
use App\Services\OzonItemPriceService;
use App\Services\WbItemPriceService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Modules\EditorContent\Services\EditorContentService;
use Modules\Moysklad\HttpClient\Resources\Entities\Product\Product;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladQuarantine;
use Modules\Moysklad\Services\MoyskladItemOrderService;
use Modules\Moysklad\Services\MoyskladService;
use Modules\VoshodApi\Jobs\VoshodUserProcess;
use Modules\VoshodApi\Models\VoshodApi;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::find(10);
        $supplier = Supplier::find('9cd53316-34f9-4197-906b-19f6f426fac6');
        $market = WbMarket::find('9c28a4ad-0153-4984-8ade-26067194793c');

        $wbItems = $market
            ->items()
            ->whereHasMorph('wbitemable', [Item::class, Bundle::class], function (Builder $query, $type) use ($supplier, $user) {
                if ($type === Item::class) {
                    $query
                        ->where('supplier_id', $supplier->id)
                        ->when(!$user->baseSettings()->exists() || !$user->baseSettings->enabled_use_buy_price_reserve, function (Builder $query) {
                            $query->where('updated', true);
                        });
                } elseif ($type === Bundle::class) {

                }
            })->get();
        $first = $wbItems->first(fn (WbItem $item) => $item->wbitemable->items->isNotEmpty());
        dd($first);
//        $supplier = Supplier::find('9cd53316-34f9-4197-906b-19f6f426fac6');
//        $wbItem = WbItem::find('00671b8c-c7f4-45a9-a88a-28e6cadeb44c');
//        $market = WbMarket::find('9d31b409-0bbf-4ac6-a353-289d2e71df11');
//        dd($wbItem->wbitemable->items->first());
//        $service = new WbItemPriceService($supplier, $market);
//        $wbItem = $service->recountPriceWbItem($wbItem);
//        $wbItem->save();
    }
}
