<?php

namespace Tests\Unit;

use App\Models\Item;
use App\Models\Organization;
use App\Models\Supplier;
use App\Models\User;
use App\Models\WbItem;
use App\Models\WbMarket;
use App\Models\WbWarehouse;
use App\Models\WbWarehouseSupplier;
use App\Services\WbItemPriceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Mockery;
use Tests\TestCase;

class WbItemPriceServiceTest extends TestCase
{
    protected $wbItemPriceService;
    protected $user;
    protected $supplier;
    protected $market;
    protected $organizaion;

    protected function setUp(): void
    {
        parent::setUp();

        // Создаём пользователя
        $this->user = User::factory()->create();

        // Создаём поставщика
        $this->supplier = Supplier::factory()->create(['user_id' => $this->user->id]);

        $this->organizaion = Organization::factory()->create(['user_id' => $this->user->id]);

        // Создаём маркет
        $this->market = WbMarket::factory()->create(['user_id' => $this->user->id, 'organization_id' => $this->organizaion->id]);

        // Создаём сервис
        $this->wbItemPriceService = new WbItemPriceService($this->supplier, $this->market);
    }

    /** @test */
    public function it_calculates_stock_for_item()
    {
        // Устанавливаем исходные данные
        $warehouse = WbWarehouse::factory()->create(['wb_market_id' => $this->market->id]);

        $item = Item::factory()->create([
            'supplier_id' => $this->supplier->id,
            'unload_wb' => false, // Убедимся, что выгрузка разрешена
            'user_id' => $this->user->id
        ]);

        $wbItem = WbItem::factory()->create([
            'wbitemable_type' => Item::class,
            'wbitemable_id' => $item->id, // Связываем с itemable
            'wb_market_id' => $this->market->id
        ]);

        // Связываем склады и поставщика
        $warehouse->suppliers()->attach($this->supplier->id);

        // Мокаем логику складских остатков
        $this->mockWarehouseStock($warehouse, $wbItem, 10);

        // Вызываем метод для перерасчёта остатков
        $updatedItem = $this->wbItemPriceService->recountStockWbItem($wbItem);

        // Проверяем, что итоговый остаток корректно пересчитан
        $this->assertEquals(10, $updatedItem->warehouseStock($warehouse)->stock);
    }

    protected function mockWarehouseStock(WbWarehouse $warehouse, $item, int $stock)
    {
        $warehouse->userWarehouses()->create([
            'stock' => $stock,
            'item_id' => $item->id,
        ]);
    }
}
