<?php

namespace App\Console\Commands;

use App\Events\NotificationEvent;
use App\Events\TestBroadcast;
use App\Models\EmailSupplier;
use App\Models\Item;
use App\Models\OzonMarket;
use App\Models\Supplier;
use App\Models\User;
use App\Models\WbItem;
use App\Models\WbMarket;
use App\Models\WbWarehouse;
use App\Services\EmailPriceItemService;
use App\Services\EmailSupplierService;
use App\Services\SupplierReportService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

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
        $market = WbMarket::find('9c2879df-980f-4fc1-b7c4-11c0bd4b3427');
        $supplier = Supplier::where('name', 'Берг')->first();

        $market->warehouses()
            ->whereHas('suppliers', function (Builder $query) use ($supplier) {
                $query->where('supplier_id', $supplier->id);
            })
            ->get()
            ->map(function (WbWarehouse $warehouse) {
                dd($warehouse);
            });
    }
}
