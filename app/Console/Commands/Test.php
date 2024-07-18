<?php

namespace App\Console\Commands;

use App\Events\NotificationEvent;
use App\Events\TestBroadcast;
use App\Helpers\Helpers;
use App\Models\EmailSupplier;
use App\Models\Item;
use App\Models\Organization;
use App\Models\OzonItem;
use App\Models\OzonMarket;
use App\Models\OzonWarehouse;
use App\Models\Permission;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WbItem;
use App\Models\WbMarket;
use App\Models\WbWarehouse;
use App\Notifications\SubscriptionExpires;
use App\Services\EmailPriceItemService;
use App\Services\EmailSupplierService;
use App\Services\OzonItemPriceService;
use App\Services\OzonMarketService;
use App\Services\SupplierReportService;
use App\Services\WbItemPriceService;
use App\Services\WbMarketService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Maatwebsite\Excel\ChunkReader;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Order\Services\OrderService;

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
        $wbItem = WbItem::find('9c62b39e-61e2-499e-83f9-a75b750b8520');
        dd($wbItem->orders()->where('state', 'new')->sum('count') * 2);
    }
}
