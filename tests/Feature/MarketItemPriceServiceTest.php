<?php

namespace Tests\Feature;

use App\Contracts\MarketItemPriceContract;
use App\Enums\ReportStatus;
use App\Enums\TaskTypes;
use App\Models\Bundle;
use App\Models\Item;
use App\Models\OzonItem;
use App\Models\OzonMarket;
use App\Models\Supplier;
use App\Models\SupplierReportLogMarket;
use App\Models\Task;
use App\Models\TaskLog;
use App\Models\User;
use App\Models\WbItem;
use App\Models\WbMarket;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MarketItemPriceServiceTest extends TestCase
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
        $this->report = Task::factory()->for($this->supplier, 'taskable')->create([
            'status' => ReportStatus::running,
            'type' => TaskTypes::SupplierUnload
        ]);
        $this->log = TaskLog::factory()->for($this->report)->create();
    }

    /**
     * A basic feature test example.
     */
    public function test_ozon_update_price_correct(): void
    {
        $item = Item::factory()->for($this->user)->for($this->supplier)->create([
            'price' => 1634.4,
            'multiplicity' => 1,
            'updated' => true
        ]);
        $market = OzonMarket::factory()->for($this->user)->create([
            'min_price_coefficient' => 1.1,
            'seller_price_percent' => 8,
            'max_price_percent' => 37,
            'acquiring' => 3,
            'last_mile' => 5.5,
            'max_mile' => 500,
            'seller_price' => false
        ]);
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

        $service = app(MarketItemPriceContract::class);
        $service->make($this->supplier, $market, $this->log);
        $service->updatePrice();

        $this->assertDatabaseHas(OzonItem::class, [
            'price_max' => 4729,
            'price_min' => 3452,
            'price' => 3950,
        ]);
    }

    public function test_ozon_update_price_bundle_correct(): void
    {
        $item = Item::factory()->for($this->user)->for($this->supplier)->create([
            'price' => 1634.4,
            'multiplicity' => 1,
            'updated' => true
        ]);
        $bundle = Bundle::factory()->for($this->user)->create();
        $bundle->items()->attach($item->id, ['multiplicity' => 1]);
        $market = OzonMarket::factory()->for($this->user)->create([
            'min_price_coefficient' => 1.01,
            'seller_price_percent' => 8,
            'max_price_percent' => 37,
            'acquiring' => 3,
            'last_mile' => 5.5,
            'max_mile' => 500,
            'seller_price' => false
        ]);
        $ozonItem = OzonItem::factory()->for($bundle, 'itemable')->for($market, 'market')->create([
            'price_max' => 0,
            'price_min' => 0,
            'price' => 0,
            'shipping_processing' => 70,
            'direct_flow_trans' => 292,
            'sales_percent' => 18,
            'min_price' => 250,
            'min_price_percent' => 12,
        ]);

        $service = app(MarketItemPriceContract::class);
        $service->make($this->supplier, $market, $this->log);
        $service->updatePrice();

        $this->assertDatabaseHas(OzonItem::class, [
            'price_max' => 4142,
            'price_min' => 3024,
            'price' => 3399,
        ]);
    }

    public function test_wb_update_price_correct(): void
    {
        $item = Item::factory()->for($this->user)->for($this->supplier)->create([
            'price' => 352.7,
            'multiplicity' => 1,
            'updated' => true
        ]);
        $market = WbMarket::factory()->for($this->user)->create([
            'coefficient' => 1.11,
            'basic_logistics' => 35,
            'price_one_liter' => 9,
            'volume' => 1,
        ]);
        $wbItem = WbItem::factory()->for($item, 'itemable')->for($market, 'market')->create([
            'sales_percent' => 16.5,
            'min_price' => 250,
            'retail_markup_percent' => 45,
            'package' => 20,
            'volume' => 1,
            'price' => 0
        ]);

        $service = app(MarketItemPriceContract::class);
        $service->make($this->supplier, $market, $this->log);
        $service->updatePrice();

        $this->assertDatabaseHas(WbItem::class, [
            'price' => 752,
        ]);
    }

    public function test_wb_update_price_bundle_correct(): void
    {
        $item = Item::factory()->for($this->user)->for($this->supplier)->create([
            'price' => 352.7,
            'multiplicity' => 1,
            'updated' => true
        ]);
        $bundle = Bundle::factory()->for($this->user)->create();
        $bundle->items()->attach($item->id, ['multiplicity' => 1]);
        $market = WbMarket::factory()->for($this->user)->create([
            'coefficient' => 1.11,
            'basic_logistics' => 35,
            'price_one_liter' => 9,
            'volume' => 1,
        ]);
        $wbItem = WbItem::factory()->for($bundle, 'itemable')->for($market, 'market')->create([
            'sales_percent' => 16.5,
            'min_price' => 250,
            'retail_markup_percent' => 45,
            'package' => 20,
            'volume' => 1,
            'price' => 0
        ]);

        $service = app(MarketItemPriceContract::class);
        $service->make($this->supplier, $market, $this->log);
        $service->updatePrice();

        $this->assertDatabaseHas(WbItem::class, [
            'price' => 752,
        ]);
    }

    public function test_ozon_unload_all_prices_correct()
    {
        $item = Item::factory()->for($this->user)->for($this->supplier)->create([
            'updated' => true
        ]);
        $bundle = Bundle::factory()->for($this->user)->create();
        $bundle->items()->attach($item->id, ['multiplicity' => 1]);
        $market = OzonMarket::factory()->for($this->user)->create([
            'enabled_price' => true,
        ]);
        $ozonItem = OzonItem::factory()->for($bundle, 'itemable')->for($market, 'market')->create([
            'price_max' => 4142,
            'price_min' => 3024,
            'price' => 3399,
            'shipping_processing' => 70,
            'direct_flow_trans' => 292,
            'sales_percent' => 18,
            'min_price' => 250,
            'min_price_percent' => 12,
            'offer_id' => 'test111',
            'product_id' => 111
        ]);

        $service = app(MarketItemPriceContract::class);
        $service->make($this->supplier, $market, $this->log);
        $service->unloadAllPrices();

        $this->assertDatabaseHas(SupplierReportLogMarket::class, [
            'task_log_id' => $this->log->id,
            'message' => "[{\"auto_action_enabled\":\"UNKNOWN\",\"currency_code\":\"RUB\",\"min_price\":\"3024\",\"offer_id\":\"test111\",\"old_price\":\"4142\",\"price\":\"3399\",\"product_id\":111}]"
        ]);
    }

    public function test_wb_unload_all_prices_correct()
    {
        $item = Item::factory()->for($this->user)->for($this->supplier)->create([
            'price' => 352.7,
            'multiplicity' => 1,
            'updated' => true
        ]);
        $bundle = Bundle::factory()->for($this->user)->create();
        $bundle->items()->attach($item->id, ['multiplicity' => 1]);
        $market = WbMarket::factory()->for($this->user)->create([
            'coefficient' => 1.11,
            'basic_logistics' => 35,
            'price_one_liter' => 9,
            'volume' => 1,
            'enabled_price' => true
        ]);
        $wbItem = WbItem::factory()->for($bundle, 'itemable')->for($market, 'market')->create([
            'sales_percent' => 16.5,
            'min_price' => 250,
            'retail_markup_percent' => 45,
            'package' => 20,
            'volume' => 1,
            'price' => 752,
            'nm_id' => 111
        ]);

        $service = app(MarketItemPriceContract::class);
        $service->make($this->supplier, $market, $this->log);
        $service->unloadAllPrices();

        $this->assertDatabaseHas(SupplierReportLogMarket::class, [
            'task_log_id' => $this->log->id,
            'message' => "[{\"nmId\":111,\"price\":752}]"
        ]);
    }

    public function test_ozon_update_price_with_seller_price_greater()
    {
        $item = Item::factory()->for($this->user)->for($this->supplier)->create([
            'price' => 1634.4,
            'multiplicity' => 1,
            'updated' => true
        ]);
        $market = OzonMarket::factory()->for($this->user)->create([
            'min_price_coefficient' => 1.01,
            'seller_price_percent' => 8,
            'max_price_percent' => 37,
            'acquiring' => 3,
            'last_mile' => 5.5,
            'max_mile' => 500,
            'seller_price' => true
        ]);
        $ozonItem = OzonItem::factory()->for($item, 'itemable')->for($market, 'market')->create([
            'price_max' => 0,
            'price_min' => 0,
            'price' => 0,
            'shipping_processing' => 70,
            'direct_flow_trans' => 292,
            'sales_percent' => 18,
            'min_price' => 250,
            'min_price_percent' => 12,
            'price_seller' => 4000
        ]);

        $service = app(MarketItemPriceContract::class);
        $service->make($this->supplier, $market, $this->log);
        $service->updatePrice();

        $this->assertDatabaseHas(OzonItem::class, [
            'price_max' => 4142,
            'price_min' => 3024,
            'price' => 3399,
        ]);
    }

    public function test_ozon_update_price_with_seller_price_less_than_set_min_price()
    {
        $item = Item::factory()->for($this->user)->for($this->supplier)->create([
            'price' => 1634.4,
            'multiplicity' => 1,
            'updated' => true
        ]);
        $market = OzonMarket::factory()->for($this->user)->create([
            'min_price_coefficient' => 1.01,
            'seller_price_percent' => 8,
            'max_price_percent' => 37,
            'acquiring' => 3,
            'last_mile' => 5.5,
            'max_mile' => 500,
            'seller_price' => true
        ]);
        $ozonItem = OzonItem::factory()->for($item, 'itemable')->for($market, 'market')->create([
            'price_max' => 0,
            'price_min' => 0,
            'price' => 0,
            'shipping_processing' => 70,
            'direct_flow_trans' => 292,
            'sales_percent' => 18,
            'min_price' => 250,
            'min_price_percent' => 12,
            'price_seller' => 3000
        ]);

        $service = app(MarketItemPriceContract::class);
        $service->make($this->supplier, $market, $this->log);
        $service->updatePrice();

        $this->assertDatabaseHas(OzonItem::class, [
            'price_max' => 4142,
            'price_min' => 3024,
            'price' => 3024,
        ]);
    }

    public function test_ozon_update_price_with_seller_price_less_than_set_second_formula()
    {
        $item = Item::factory()->for($this->user)->for($this->supplier)->create([
            'price' => 1634.4,
            'multiplicity' => 1,
            'updated' => true
        ]);
        $market = OzonMarket::factory()->for($this->user)->create([
            'min_price_coefficient' => 1.01,
            'seller_price_percent' => 8,
            'max_price_percent' => 37,
            'acquiring' => 3,
            'last_mile' => 5.5,
            'max_mile' => 500,
            'seller_price' => true
        ]);
        $ozonItem = OzonItem::factory()->for($item, 'itemable')->for($market, 'market')->create([
            'price_max' => 0,
            'price_min' => 0,
            'price' => 0,
            'shipping_processing' => 70,
            'direct_flow_trans' => 292,
            'sales_percent' => 18,
            'min_price' => 250,
            'min_price_percent' => 5,
            'price_seller' => 3000
        ]);

        $service = app(MarketItemPriceContract::class);
        $service->make($this->supplier, $market, $this->log);
        $service->updatePrice();

        $this->assertDatabaseHas(OzonItem::class, [
            'price_max' => 3926,
            'price_min' => 2866,
            'price' => 2970,
        ]);
    }
}
