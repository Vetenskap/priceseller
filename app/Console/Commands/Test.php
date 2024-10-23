<?php

namespace App\Console\Commands;

use App\HttpClient\OzonClient\Resources\DescriptionCategory;
use App\HttpClient\OzonClient\Resources\DescriptionCategoryAttribute;
use App\HttpClient\OzonClient\Resources\DescriptionCategoryTree;
use App\HttpClient\OzonClient\Resources\FBS\PostingUnfulfilled\PostingUnfulfilledList;
use App\HttpClient\OzonClient\Resources\ProductInfoPrices;
use App\Models\Bundle;
use App\Models\EmailSupplier;
use App\Models\Item;
use App\Models\OzonItem;
use App\Models\OzonMarket;
use App\Models\Supplier;
use App\Models\SupplierWarehouse;
use App\Models\User;
use App\Models\WbItem;
use App\Models\WbMarket;
use App\Services\EmailSupplierService;
use App\Services\OzonItemPriceService;
use App\Services\WbItemPriceService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
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
        $list = new PostingUnfulfilledList();
        $list->setFilterCutoffFrom(now()->format('Y-m-d\TH:i:s\Z'));
        $list->setFilterCutoffTo(now()->addDays(2)->format('Y-m-d\TH:i:s\Z'));
        $list->setFilterStatus('awaiting_deliver');
        dd($list->next('14252280-8f09-4ac4-a814-4b1799c71d9d', 112234));
    }
}
