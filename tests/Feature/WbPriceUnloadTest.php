<?php

namespace Tests\Feature;

use App\Contracts\MarketItemPriceContract;
use App\Contracts\MarketItemStockContract;
use App\Enums\ReportStatus;
use App\Enums\TaskTypes;
use App\Jobs\Wb\PriceUnload;
use App\Models\Item;
use App\Models\ItemSupplierWarehouseStock;
use App\Models\Supplier;
use App\Models\SupplierWarehouse;
use App\Models\Task;
use App\Models\TaskLog;
use App\Models\User;
use App\Models\WbItem;
use App\Models\WbMarket;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WbPriceUnloadTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     */
    public function test_correct_job(): void
    {
        $user = User::factory()->hasEmails(1, [
            'open' => true,
            'address' => 'email@gmail.com',
            'password' => 'qwerty1234'
        ])->create();
        $supplier = Supplier::factory()->for($user)->create();
        $supplierWarehouse = SupplierWarehouse::factory()->for($supplier)->create();
        $report = Task::factory()->for($supplier, 'taskable')->create([
            'status' => ReportStatus::running,
            'type' => TaskTypes::SupplierUnload
        ]);
        $item = Item::factory()->for($user)->for($supplier)->create();
        $stock = ItemSupplierWarehouseStock::factory()->for($supplierWarehouse, 'warehouse')->for($item)->create([
            'stock' => 10
        ]);
        $market = WbMarket::factory()->for($user)->create(['open' => true, 'close' => false]);
        $wbItem = WbItem::factory()->for($item, 'itemable')->for($market, 'market')->create();

        $mockPrice = \Mockery::mock(MarketItemPriceContract::class);
        $mockPrice->shouldReceive('make')
            ->once();
        $mockPrice->shouldReceive('updatePrice')
            ->once();
        $mockPrice->shouldReceive('unloadAllPrices')
            ->once();

        $this->app->instance(MarketItemPriceContract::class, $mockPrice);

        $mockStock = \Mockery::mock(MarketItemStockContract::class);
        $mockStock->shouldReceive('make')
            ->once();
        $mockStock->shouldReceive('updateStock')
            ->once();
        $mockStock->shouldReceive('unloadAllStocks')
            ->once();

        $this->app->instance(MarketItemStockContract::class, $mockStock);

        $job = new PriceUnload($market, $supplier, $report);
        $job->handle();

        $this->assertDatabaseHas(TaskLog::class, [
            'status' => ReportStatus::finished,
            'message' => "Выгрузка кабинета: {$market->name}"
        ]);
    }
}
