<?php

namespace Tests\Feature;

use App\Contracts\MarketItemStockContract;
use App\Enums\ReportStatus;
use App\Enums\TaskTypes;
use App\Models\Item;
use App\Models\ItemSupplierWarehouseStock;
use App\Models\ItemWarehouseStock;
use App\Models\Module;
use App\Models\OzonItem;
use App\Models\OzonMarket;
use App\Models\OzonWarehouse;
use App\Models\OzonWarehouseStock;
use App\Models\OzonWarehouseSupplier;
use App\Models\OzonWarehouseSupplierWarehouse;
use App\Models\OzonWarehouseUserWarehouse;
use App\Models\Permission;
use App\Models\Supplier;
use App\Models\SupplierReportLogMarket;
use App\Models\SupplierWarehouse;
use App\Models\Task;
use App\Models\TaskLog;
use App\Models\User;
use App\Models\UserModule;
use App\Models\Warehouse;
use App\Models\WbItem;
use App\Models\WbMarket;
use App\Models\WbWarehouse;
use App\Models\WbWarehouseStock;
use App\Models\WbWarehouseSupplier;
use App\Models\WbWarehouseSupplierWarehouse;
use App\Models\WbWarehouseUserWarehouse;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Modules\Moysklad\Database\Factories\MoyskladFactory;
use Modules\Moysklad\Database\Factories\MoyskladItemOrderFactory;
use Modules\Moysklad\Models\MoyskladItemOrder;
use Tests\TestCase;

class MarketItemStockServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->hasEmails(1, [
            'open' => true,
            'address' => 'email@gmail.com',
            'password' => 'qwerty1234'
        ])->create();
        $this->supplier = Supplier::factory()->for($this->user)->create();
        $this->supplierWarehouse = SupplierWarehouse::factory()->for($this->supplier)->create();
        $this->supplierWarehouseIds = $this->supplier->warehouses->pluck('id')->toArray();
        $this->report = Task::factory()->for($this->supplier, 'taskable')->create([
            'status' => ReportStatus::running,
            'type' => TaskTypes::SupplierUnload
        ]);
        $this->log = TaskLog::factory()->for($this->report)->create();
        $this->userWarehouse = Warehouse::factory()->for($this->user)->create();
        $this->moysklad = MoyskladFactory::new()->for($this->user)->create([
            'enabled_orders' => true,
            'clear_order_time' => 10
        ]);
        $userModule = UserModule::factory()->for($this->user)->for(Module::where('name', 'Moysklad')->first())->create(['enabled' => true]);
        $this->user->permissions()->attach(Permission::where('value', 'admin')->first('id'), [
            'expires' => now()->addDay()
        ]);
    }

    /**
     * A basic feature test example.
     */
    public function test_ozon_correct_update_stock()
    {
        $item = Item::factory()->for($this->user)->for($this->supplier)->create([
            'unload_ozon' => true,
            'multiplicity' => 1
        ]);
        $item2 = Item::factory()->for($this->user)->for($this->supplier)->create([
            'unload_ozon' => true,
            'multiplicity' => 1
        ]);
        $moyskladOrder = MoyskladItemOrderFactory::new()->for($this->moysklad)->for($item)->create([
            'new' => true,
            'orders' => 1,
            'updated_at' => now()->subMinutes(10)
        ]);
        $moyskladOrder = MoyskladItemOrderFactory::new()->for($this->moysklad)->for($item)->create([
            'new' => true,
            'orders' => 1,
            'updated_at' => now()->subMinutes(11)
        ]);
        $stock = ItemSupplierWarehouseStock::factory()->for($item)->for($this->supplierWarehouse, 'warehouse')->create([
            'stock' => 8
        ]);
        $stock2 = ItemSupplierWarehouseStock::factory()->for($item2)->for($this->supplierWarehouse, 'warehouse')->create([
            'stock' => 4
        ]);
        $userWarehouseStock = ItemWarehouseStock::factory()->for($this->userWarehouse, 'warehouse')->for($item2)->create([
            'stock' => 2
        ]);
        $market = OzonMarket::factory()->for($this->user)->create([
            'minus_stock' => 1,
            'min' => 4,
            'max' => 7,
            'enabled_orders' => true
        ]);
        $ozonWarehouse = OzonWarehouse::factory()->for($market, 'market')->create();
        $ozonUserWarehouse = OzonWarehouseUserWarehouse::factory()->for($ozonWarehouse, 'ozonWarehouse')->for($this->userWarehouse, 'warehouse')->create();
        $ozonWarehouseSupplier = OzonWarehouseSupplier::factory()->for($ozonWarehouse, 'warehouse')->for($this->supplier)->create();
        $ozonWarehouseSupplierWarehouse = OzonWarehouseSupplierWarehouse::factory()->for($ozonWarehouseSupplier, 'ozonWarehouseSupplier')->for($this->supplierWarehouse, 'supplierWarehouse')->create();
        $ozonItem = OzonItem::factory()->for($item, 'itemable')->for($market, 'market')->create();
        $ozonItem2 = OzonItem::factory()->for($item2, 'itemable')->for($market, 'market')->create();
        $service = app(MarketItemStockContract::class);
        $service->make($this->supplier, $market, $this->log, $this->supplierWarehouseIds);
        $service->updateStock();

        $this->assertDatabaseHas(OzonWarehouseStock::class, [
            'stock' => 0,
            'ozon_item_id' => $ozonItem->id,
            'ozon_warehouse_id' => $ozonWarehouse->id
        ]);
        $this->assertDatabaseHas(OzonWarehouseStock::class, [
            'stock' => 2,
            'ozon_item_id' => $ozonItem2->id,
            'ozon_warehouse_id' => $ozonWarehouse->id
        ]);

        $this->assertDatabaseHas(MoyskladItemOrder::class, [
            'item_id' => $item->id,
            'new' => 0,
            'orders' => 1
        ]);
        $this->assertDatabaseHas(MoyskladItemOrder::class, [
            'item_id' => $item->id,
            'new' => 1,
            'orders' => 1
        ]);
    }

    public function test_wb_correct_update_stock()
    {
        $item = Item::factory()->for($this->user)->for($this->supplier)->create([
            'unload_wb' => true,
            'multiplicity' => 1
        ]);
        $item2 = Item::factory()->for($this->user)->for($this->supplier)->create([
            'unload_wb' => true,
            'multiplicity' => 1
        ]);
        $moyskladOrder = MoyskladItemOrderFactory::new()->for($this->moysklad)->for($item)->create([
            'new' => true,
            'orders' => 1,
            'updated_at' => now()->subMinutes(10)
        ]);
        $moyskladOrder = MoyskladItemOrderFactory::new()->for($this->moysklad)->for($item)->create([
            'new' => true,
            'orders' => 1,
            'updated_at' => now()->subMinutes(11)
        ]);
        $stock = ItemSupplierWarehouseStock::factory()->for($item)->for($this->supplierWarehouse, 'warehouse')->create([
            'stock' => 8
        ]);
        $stock2 = ItemSupplierWarehouseStock::factory()->for($item2)->for($this->supplierWarehouse, 'warehouse')->create([
            'stock' => 4
        ]);
        $userWarehouseStock = ItemWarehouseStock::factory()->for($this->userWarehouse, 'warehouse')->for($item2)->create([
            'stock' => 2
        ]);
        $market = WbMarket::factory()->for($this->user)->create([
            'minus_stock' => 1,
            'min' => 4,
            'max' => 7,
            'enabled_orders' => true
        ]);
        $wbWarehouse = WbWarehouse::factory()->for($market, 'market')->create();
        $wbUserWarehouse = WbWarehouseUserWarehouse::factory()->for($wbWarehouse, 'wbWarehouse')->for($this->userWarehouse, 'warehouse')->create();
        $wbWarehouseSupplier = WbWarehouseSupplier::factory()->for($wbWarehouse, 'warehouse')->for($this->supplier)->create();
        $wbWarehouseSupplierWarehouse = WbWarehouseSupplierWarehouse::factory()->for($wbWarehouseSupplier, 'wbWarehouseSupplier')->for($this->supplierWarehouse, 'supplierWarehouse')->create();
        $wbItem = WbItem::factory()->for($item, 'itemable')->for($market, 'market')->create();
        $wbItem2 = WbItem::factory()->for($item2, 'itemable')->for($market, 'market')->create();
        $service = app(MarketItemStockContract::class);
        $service->make($this->supplier, $market, $this->log, $this->supplierWarehouseIds);
        $service->updateStock();

        $this->assertDatabaseHas(WbWarehouseStock::class, [
            'stock' => 0,
            'wb_item_id' => $wbItem->id,
            'wb_warehouse_id' => $wbWarehouse->id
        ]);
        $this->assertDatabaseHas(WbWarehouseStock::class, [
            'stock' => 2,
            'wb_item_id' => $wbItem2->id,
            'wb_warehouse_id' => $wbWarehouse->id
        ]);

        $this->assertDatabaseHas(MoyskladItemOrder::class, [
            'item_id' => $item->id,
            'new' => 0,
            'orders' => 1
        ]);
        $this->assertDatabaseHas(MoyskladItemOrder::class, [
            'item_id' => $item->id,
            'new' => 1,
            'orders' => 1
        ]);
    }

    public function test_ozon_unload_all_stocks()
    {
        $item = Item::factory()->for($this->user)->for($this->supplier)->create([
            'unload_ozon' => true,
            'multiplicity' => 1
        ]);
        $item2 = Item::factory()->for($this->user)->for($this->supplier)->create([
            'unload_ozon' => true,
            'multiplicity' => 1
        ]);
        $market = OzonMarket::factory()->for($this->user)->create([
            'enabled_stocks' => true
        ]);
        $ozonWarehouse = OzonWarehouse::factory()->for($market, 'market')->create();
        $ozonUserWarehouse = OzonWarehouseUserWarehouse::factory()->for($ozonWarehouse, 'ozonWarehouse')->for($this->userWarehouse, 'warehouse')->create();
        $ozonWarehouseSupplier = OzonWarehouseSupplier::factory()->for($ozonWarehouse, 'warehouse')->for($this->supplier)->create();
        $ozonWarehouseSupplierWarehouse = OzonWarehouseSupplierWarehouse::factory()->for($ozonWarehouseSupplier, 'ozonWarehouseSupplier')->for($this->supplierWarehouse, 'supplierWarehouse')->create();
        $ozonItem = OzonItem::factory()->for($item, 'itemable')->for($market, 'market')->create([
            'product_id' => 111,
            'offer_id' => 'offer111'
        ]);
        $ozonItem2 = OzonItem::factory()->for($item2, 'itemable')->for($market, 'market')->create([
            'product_id' => 222,
            'offer_id' => 'offer222'
        ]);
        OzonWarehouseStock::factory()->for($ozonWarehouse, 'warehouse')->for($ozonItem)->create([
            'stock' => 5
        ]);
        OzonWarehouseStock::factory()->for($ozonWarehouse, 'warehouse')->for($ozonItem2)->create([
            'stock' => 10
        ]);

        $service = app(MarketItemStockContract::class);
        $service->make($this->supplier, $market, $this->log, $this->supplierWarehouseIds);
        $service->unloadAllStocks();

        $this->assertDatabaseHas(SupplierReportLogMarket::class, [
            'message' => "[{\"offer_id\":\"offer111\",\"product_id\":111,\"stock\":5,\"warehouse_id\":0},{\"offer_id\":\"offer222\",\"product_id\":222,\"stock\":10,\"warehouse_id\":0}]"
        ]);
    }

    public function test_wb_unload_all_stocks()
    {
        $item = Item::factory()->for($this->user)->for($this->supplier)->create([
            'unload_wb' => true,
            'multiplicity' => 1
        ]);
        $item2 = Item::factory()->for($this->user)->for($this->supplier)->create([
            'unload_wb' => true,
            'multiplicity' => 1
        ]);
        $market = WbMarket::factory()->for($this->user)->create([
            'enabled_stocks' => true
        ]);
        $wbWarehouse = WbWarehouse::factory()->for($market, 'market')->create();
        $wbUserWarehouse = WbWarehouseUserWarehouse::factory()->for($wbWarehouse, 'wbWarehouse')->for($this->userWarehouse, 'warehouse')->create();
        $wbWarehouseSupplier = WbWarehouseSupplier::factory()->for($wbWarehouse, 'warehouse')->for($this->supplier)->create();
        $wbWarehouseSupplierWarehouse = WbWarehouseSupplierWarehouse::factory()->for($wbWarehouseSupplier, 'wbWarehouseSupplier')->for($this->supplierWarehouse, 'supplierWarehouse')->create();
        $wbItem = wbItem::factory()->for($item, 'itemable')->for($market, 'market')->create([
            'sku' => 'sku111'
        ]);
        $wbItem2 = wbItem::factory()->for($item2, 'itemable')->for($market, 'market')->create([
            'sku' => 'sku222'
        ]);
        WbWarehouseStock::factory()->for($wbWarehouse, 'warehouse')->for($wbItem)->create([
            'stock' => 5
        ]);
        WbWarehouseStock::factory()->for($wbWarehouse, 'warehouse')->for($wbItem2)->create([
            'stock' => 10
        ]);

        $service = app(MarketItemStockContract::class);
        $service->make($this->supplier, $market, $this->log, $this->supplierWarehouseIds);
        $service->unloadAllStocks();

        $this->assertDatabaseHas(SupplierReportLogMarket::class, [
            'message' => "[{\"sku\":\"sku111\",\"amount\":5},{\"sku\":\"sku222\",\"amount\":10}]"
        ]);
    }
}
