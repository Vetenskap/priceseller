<?php

namespace Tests\Feature;

use App\Jobs\Supplier\MarketsEmailSupplierUnload;
use App\Models\Email;
use App\Models\EmailSupplier;
use App\Models\Item;
use App\Models\OzonItem;
use App\Models\OzonMarket;
use App\Models\Supplier;
use App\Models\SupplierWarehouse;
use App\Models\User;
use App\Services\OzonItemPriceService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use Tests\TestCase;

class MarketsEmailSupplierUnloadTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $email;
    protected $supplier;
    protected $emailSupplier;

    protected function setUp(): void
    {
        parent::setUp();

        // Создаём основные сущности для тестов
        $this->user = User::factory()->create();
        $this->email = Email::factory()->for($this->user)->create();
        $this->supplier = Supplier::factory()->for($this->user)->create();
        $this->emailSupplier = EmailSupplier::factory()->for($this->email)->for($this->supplier)->create();
    }

    public function test_job_is_dispatched_correctly()
    {
        Queue::fake();

        MarketsEmailSupplierUnload::dispatch($this->user, $this->emailSupplier);

        Queue::assertPushed(MarketsEmailSupplierUnload::class, function ($job) {
            return $job->user->id === $this->user->id && $job->emailSupplier->id === $this->emailSupplier->id;
        });
    }

    public function test_job_has_unique_id()
    {
        Queue::fake();

        $job = new MarketsEmailSupplierUnload($this->user, $this->emailSupplier);

        $this->assertEquals($this->emailSupplier->id . 'markets_unload', $job->uniqueId());
        $this->assertEquals(600, $job->uniqueFor);
    }

    public function test_correctly_ozon_markets()
    {
        Queue::fake();

        $ozonMarket = OzonMarket::factory()->state(['open' => true, 'close' => false])->for($this->user)->create();
        $item = Item::factory()->for($this->user)->for($this->supplier)->create();
        $ozonItem = OzonItem::factory()->for($item, 'itemable')->for($ozonMarket, 'market')->create();

        $mockService = \Mockery::mock(OzonItemPriceService::class);

        $mockService->shouldReceive('updateStock')
            ->once()
            ->andReturnUndefined()
            ->withNoArgs();
        $mockService->shouldReceive('updatePrice')
            ->once()
            ->andReturnUndefined()
            ->withNoArgs();
        $mockService->shouldReceive('unloadAllStocks')
            ->once()
            ->andReturnUndefined()
            ->withNoArgs();
        $mockService->shouldReceive('unloadAllPrices')
            ->once()
            ->andReturnUndefined()
            ->withNoArgs();

        $this->app->bind(OzonItemPriceService::class, function ($app) use ($mockService) {
            return $mockService;
        });

        $job = new MarketsEmailSupplierUnload($this->user, $this->emailSupplier);

        $job->handle();
    }
}
