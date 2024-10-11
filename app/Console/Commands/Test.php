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
        $bundle = Bundle::find('9d078d27-2ae4-43ae-8c8c-75716d9bc91c');

        dd($bundle->items()->where('ms_uuid', 'ec499525-8dc1-11ec-0a80-08a5003abf32')->first()->pivot);
    }
}
