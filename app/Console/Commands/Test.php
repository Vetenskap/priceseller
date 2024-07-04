<?php

namespace App\Console\Commands;

use App\Events\NotificationEvent;
use App\Events\TestBroadcast;
use App\Models\EmailSupplier;
use App\Models\Item;
use App\Models\Organization;
use App\Models\OzonItem;
use App\Models\OzonMarket;
use App\Models\OzonWarehouse;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WbItem;
use App\Models\WbMarket;
use App\Models\WbWarehouse;
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
//        $supplier = Supplier::find('9c5482cc-4a4a-4806-a5ca-c67ba8a41078');
//        $market = WbMarket::find('9c4e4462-6a3a-40f7-b9a2-dae9d0a1554a');
//        $service = new WbItemPriceService($supplier, $market);
//        $service->recountStockWbItem(WbItem::find('9c62b22f-9e2f-45a2-b68a-91336a4cc01e'));

        $item = WbItem::find('9c62b22f-9e2f-45a2-b68a-91336a4cc01e');
        $warehouse = WbWarehouse::find(5);

        dd($item->warehouseStock($warehouse)->stock);
    }
}
