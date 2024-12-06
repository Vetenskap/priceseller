<?php

namespace Tests\Feature;

use App\Helpers\Helpers;
use App\Models\Email;
use App\Models\EmailSupplier;
use App\Models\EmailSupplierWarehouse;
use App\Models\Item;
use App\Models\ItemSupplierWarehouseStock;
use App\Models\Supplier;
use App\Models\SupplierWarehouse;
use App\Models\User;
use App\Services\EmailSupplierService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Tests\TestCase;

class EmailSupplierServiceClassTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $email;
    protected $supplier;
    protected $data;
    protected $items;

    protected function setUp(): void
    {
        parent::setUp();

        // Создаём основные сущности для тестов
        $this->user = User::factory()->create();
        $this->email = Email::factory()->for($this->user)->create();
        $this->supplier = Supplier::factory()->for($this->user)->create();
        $this->data = [
            [7 => '000465', 6 => 2458, 3 => 4, 9 => 'test'],
            [7 => '000491', 6 => 2744.17, 3 => 2, 9 => 'test'],
            [7 => '000615', 6 => 3737.46, 3 => 6, 9 => 'test2'],
            [7 => '000700', 6 => 7020.37, 3 => 10, 9 => 'test2'],
        ];
    }

    protected function createItems(): void
    {
        $this->items =  Item::factory()->for($this->supplier)->for($this->user)->count(4)->sequence(
            ['article' => '000465', 'price' => 2458],
            ['article' => '000491', 'price' => 2744.17],
            ['article' => '000615', 'price' => 3737.46],
            ['article' => '000700', 'price' => 7020.37],
        )->create();
    }

    protected function createEmailSupplierWithStockValues(array $stockValues = [])
    {
        $emailSupplier = EmailSupplier::factory()->for($this->email)->for($this->supplier)->create();

        if ($stockValues) {
            $emailSupplier->stockValues()->createMany($stockValues);
        }

        return $emailSupplier;
    }

    protected function createEmailSupplierService(array $stockValues = [], string $filePath = 'path/to/file.xlsx'): EmailSupplierService
    {
        $emailSupplier = $this->createEmailSupplierWithStockValues($stockValues);
        return new EmailSupplierService($emailSupplier, $filePath);
    }

    public function test_stock_values_are_correctly_created()
    {
        $service = $this->createEmailSupplierService([
            ['name' => 'InStock', 'value' => 10],
        ]);

        $expectedValues = $service->supplier->stockValues->pluck('value', 'name');
        $this->assertEquals($expectedValues->toArray(), $service->stockValues->toArray());
    }

    public function test_prepare_stock_uses_stock_values_collection()
    {
        $service = $this->createEmailSupplierService([
            ['name' => 'InStock', 'value' => 10],
            ['name' => 'OutOfStock', 'value' => 0],
            ['name' => '<400', 'value' => 100],
        ]);

        // Проверяем, что метод возвращает преобразованные значения
        $this->assertEquals(10, $service->prepareStock('InStock'));    // Использует коллекцию
        $this->assertEquals(0, $service->prepareStock('OutOfStock')); // Использует коллекцию
        $this->assertEquals(50, $service->prepareStock('50'));        // Не найдено в коллекции, парсит число
        $this->assertEquals(100, $service->prepareStock('<400'));        // Не найдено в коллекции, парсит число
    }

    public function test_prepare_price_converts_to_float()
    {
        $service = $this->createEmailSupplierService();

        $this->assertEquals(100.5, $service->preparePrice('100,5'));
        $this->assertEquals(150.99, $service->preparePrice('150.99'));
        $this->assertEquals(0.0, $service->preparePrice(''));
    }

    public function test_shate_txt_unload()
    {
        Queue::fake();

        $this->partialMock(Helpers::class, function (MockInterface $mock) {
            $mock->shouldReceive('toBatch')
                ->once();
        });

        $service = $this->createEmailSupplierService(filePath: Storage::disk('test')->path('prices/Shate.txt'));
        $service->unload();

    }

    public function test_trast_xlsx_exception_unload()
    {
        $this->partialMock(Helpers::class, function (MockInterface $mock) {
            $mock->shouldReceive('toBatch')
                ->once();
        });

        $service = $this->createEmailSupplierService(filePath: Storage::disk('test')->path('prices/Trast.xlsx'));
        $service->unload();
    }

    public function test_voshod_xlsx_unload()
    {
        $this->partialMock(Helpers::class, function (MockInterface $mock) {
            $mock->shouldReceive('toBatch')
                ->once();
        });

        $service = $this->createEmailSupplierService(filePath: Storage::disk('test')->path('prices/Voshod.xlsx'));
        $service->unload();
    }

    public function test_found_article()
    {
        $this->createItems();
        $emailSupplier = EmailSupplier::factory()->for($this->email)->for($this->supplier)->create([
            'header_article' => 8,
            'header_warehouse' => 10,
            'header_brand' => 1,
            'header_count' => 4,
            'header_price' => 7
        ]);

        $service = new EmailSupplierService($emailSupplier, '');
        $service->processData(collect($this->data[0]));

        $this->assertDatabaseHas(Item::class, [
            'article' => '000465',
            'price' => 2458
        ]);
    }

}
