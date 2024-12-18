<?php

namespace Tests\Feature;

use App\Contracts\MarketItemStockContract;
use App\Enums\ReportStatus;
use App\Enums\TaskTypes;
use App\Models\Item;
use App\Models\ItemSupplierWarehouseStock;
use App\Models\OzonItem;
use App\Models\OzonMarket;
use App\Models\OzonWarehouse;
use App\Models\OzonWarehouseStock;
use App\Models\OzonWarehouseSupplier;
use App\Models\OzonWarehouseSupplierWarehouse;
use App\Models\Supplier;
use App\Models\SupplierWarehouse;
use App\Models\Task;
use App\Models\TaskLog;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
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
    }

    /**
     * A basic feature test example.
     */
    public function test_ozon_correct_update_stock()
    {
        $item = Item::factory()->for($this->user)->for($this->supplier)->create(['unload_ozon' => true]);
        $stock = ItemSupplierWarehouseStock::factory()->for($item)->for($this->supplierWarehouse, 'warehouse')->create(['stock' => 10]);
        $market = OzonMarket::factory()->for($this->user)->create();
        $ozonWarehouse = OzonWarehouse::factory()->for($market)->create();
        $ozonWarehouseSupplier = OzonWarehouseSupplier::factory()->for($ozonWarehouse, 'warehouse')->create();
        $ozonWarehouseSupplierWarehouse = OzonWarehouseSupplierWarehouse::factory()->for($ozonWarehouseSupplier, '')
        $ozonItem = OzonItem::factory()->for($item, 'itemable')->for($market, 'market')->create([
            'price_max' => 0,
            'price_min' => 0,
            'price' => 0,
            'shipping_processing' => 70,
            'direct_flow_trans' => 292,
            'sales_percent' => 18,
            'min_price' => 250,
            'min_price_percent' => 12,
        ]);
        $service = app(MarketItemStockContract::class);
        $service->make($this->supplier, $market, $this->log, $this->supplierWarehouseIds);
        $service->updateStock();

        $this->assertDatabaseHas(OzonWarehouseStock::class, [
            'stock' => 10
        ]);
    }
}
