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
use App\Services\OzonItemPriceService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Modules\EditorContent\Services\EditorContentService;
use Modules\Moysklad\HttpClient\Resources\Entities\Product\Product;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladQuarantine;
use Modules\Moysklad\Services\MoyskladItemOrderService;
use Modules\Moysklad\Services\MoyskladService;

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
        $item = Item::find('9cd5c120-59b0-4045-bc5d-3d1490053fdf');
        $moysklad = Moysklad::find(2);
        $product = new Product();
        $product->setId($item->ms_uuid);
        $product->fetch($moysklad->api_key);
        dd($product->getBuyPrice()->getValue());
    }
}
