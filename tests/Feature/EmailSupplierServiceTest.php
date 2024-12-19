<?php

namespace Tests\Feature;

use App\Contracts\MarketContract;
use App\Contracts\ReportContract;
use App\Enums\ReportStatus;
use App\Enums\TaskTypes;
use App\Exceptions\ReportCancelled;
use App\Models\EmailSupplier;
use App\Models\EmailSupplierWarehouse;
use App\Models\Item;
use App\Models\ItemSupplierWarehouseStock;
use App\Models\Supplier;
use App\Models\SupplierWarehouse;
use App\Models\TaskLog;
use App\Models\User;
use App\Services\EmailSupplierEmailService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmailSupplierServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     */
    public function test_unload_success_with_default_warehouse(): void
    {
        $user = User::factory()->hasEmails(1, [
            'open' => true,
            'address' => 'email@gmail.com',
            'password' => 'qwerty1234'
        ])->create();
        $supplier = Supplier::factory()->state(['unload_without_price' => false, 'open' => true, 'use_brand' => false])->for($user)->create();
        $warehouse = SupplierWarehouse::factory()->for($supplier)->create();
        $emailSupplier = EmailSupplier::factory()->state([
            'email' => 'supplier@example.com',
            'filename' => 'shate.txt',
            'header_article' => 8,
            'header_brand' => 1,
            'header_price' => 7,
            'header_count' => 4,
            'header_warehouse' => null
        ])->for($user->emails->first(), 'mainEmail')->for($supplier)->create();
        $emailSupplierWarehouse = EmailSupplierWarehouse::factory()->for($emailSupplier)->for($warehouse)->create();
        $report = app(ReportContract::class)->new(TaskTypes::SupplierUnload, [], $supplier);

        $item = Item::factory()->for($supplier)->for($user)->create([
            'article' => '000465',
            'multiplicity' => 1,
            'price' => 0
        ]);

        $mock = \Mockery::mock(MarketContract::class);

        $mock->shouldReceive('unload')
            ->once()
            ->andReturnUndefined();

        $this->app->instance(MarketContract::class, $mock);

        $service = app(EmailSupplierEmailService::class);
        $service->make($emailSupplier, Storage::disk('public')->path('tests/shate.txt'), $report);
        $service->unload();

        $this->assertDatabaseHas(Item::class, [
            'article' => '000465',
            'price' => 2458,
        ]);
        $this->assertDatabaseHas(ItemSupplierWarehouseStock::class, [
            'stock' => 4,
            'supplier_warehouse_id' => $warehouse->id,
            'item_id' => $item->id
        ]);

    }

    public function test_unload_success_with_default_warehouse_cancelled()
    {
        Queue::fake();

        $this->expectException(ReportCancelled::class);

        $user = User::factory()->hasEmails(1, [
            'open' => true,
            'address' => 'email@gmail.com',
            'password' => 'qwerty1234'
        ])->create();
        $supplier = Supplier::factory()->state(['unload_without_price' => false, 'open' => true, 'use_brand' => false])->for($user)->create();
        $warehouse = SupplierWarehouse::factory()->for($supplier)->create();
        $emailSupplier = EmailSupplier::factory()->state([
            'email' => 'supplier@example.com',
            'filename' => 'shate.txt',
            'header_article' => 8,
            'header_brand' => 1,
            'header_price' => 7,
            'header_count' => 4,
            'header_warehouse' => null
        ])->for($user->emails->first(), 'mainEmail')->for($supplier)->create();
        $emailSupplierWarehouse = EmailSupplierWarehouse::factory()->for($emailSupplier)->for($warehouse)->create();
        $report = app(ReportContract::class)->new(TaskTypes::SupplierUnload, [], $supplier);

        $item = Item::factory()->for($supplier)->for($user)->create([
            'article' => '000465',
            'multiplicity' => 1,
            'price' => 0
        ]);

        $mock = \Mockery::mock(MarketContract::class);

        $mock->shouldReceive('unload')
            ->never();

        $this->app->instance(MarketContract::class, $mock);

        $report->update(['status' => ReportStatus::cancelled]);

        $service = app(EmailSupplierEmailService::class);
        $service->make($emailSupplier, Storage::disk('public')->path('tests/shate.txt'), $report);
        $service->unload();
    }

    public function test_unload_success_with_warehouses()
    {
        $user = User::factory()->hasEmails(1, [
            'open' => true,
            'address' => 'email@gmail.com',
            'password' => 'qwerty1234'
        ])->create();
        $supplier = Supplier::factory()->state(['unload_without_price' => false, 'open' => true, 'use_brand' => false])->for($user)->create();
        $warehouseEKB = SupplierWarehouse::factory()->for($supplier)->create([
            'name' => 'Екатеринбург'
        ]);
        $warehouseTyumen = SupplierWarehouse::factory()->for($supplier)->create([
            'name' => 'Тюмень'
        ]);
        $emailSupplier = EmailSupplier::factory()->state([
            'email' => 'supplier@example.com',
            'filename' => 'shate.txt',
            'header_article' => 8,
            'header_brand' => 1,
            'header_price' => 7,
            'header_count' => 4,
            'header_warehouse' => 6
        ])->for($user->emails->first(), 'mainEmail')->for($supplier)->create();
        $emailSupplierWarehouseEKB = EmailSupplierWarehouse::factory()->for($emailSupplier)->for($warehouseEKB)->create([
            'value' => 'EKB'
        ]);
        $emailSupplierWarehouseTyumen = EmailSupplierWarehouse::factory()->for($emailSupplier)->for($warehouseTyumen)->create([
            'value' => 'Tyumen'
        ]);
        $report = app(ReportContract::class)->new(TaskTypes::SupplierUnload, [], $supplier);

        $item1 = Item::factory()->for($supplier)->for($user)->create([
            'article' => '000465',
            'multiplicity' => 1,
            'price' => 0
        ]);
        $item2 = Item::factory()->for($supplier)->for($user)->create([
            'article' => '000491',
            'multiplicity' => 1,
            'price' => 0
        ]);

        $mock = \Mockery::mock(MarketContract::class);

        $mock->shouldReceive('unload')
            ->once()
            ->andReturnUndefined();

        $this->app->instance(MarketContract::class, $mock);

        $service = app(EmailSupplierEmailService::class);
        $service->make($emailSupplier, Storage::disk('public')->path('tests/shate.txt'), $report);
        $service->unload();

        $this->assertDatabaseHas(Item::class, [
            'article' => '000465',
            'price' => 2458,
        ]);
        $this->assertDatabaseHas(ItemSupplierWarehouseStock::class, [
            'stock' => 4,
            'supplier_warehouse_id' => $warehouseTyumen->id,
            'item_id' => $item1->id
        ]);
        $this->assertDatabaseHas(ItemSupplierWarehouseStock::class, [
            'stock' => 2,
            'supplier_warehouse_id' => $warehouseEKB->id,
            'item_id' => $item2->id
        ]);
    }
}
