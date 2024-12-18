<?php

namespace Tests\Feature;

use App\Contracts\MarketContract;
use App\Enums\ReportStatus;
use App\Enums\TaskTypes;
use App\Exceptions\ReportCancelled;
use App\Jobs\Ozon\PriceUnload;
use App\Models\EmailSupplier;
use App\Models\Item;
use App\Models\OzonItem;
use App\Models\OzonMarket;
use App\Models\Supplier;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MarketServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     */
    public function test_correct_unload_ozon(): void
    {
        Queue::fake();

        Bus::shouldReceive('batch')
            ->once()
            ->andReturn(
                \Mockery::mock()
                    ->shouldReceive('dispatch')->once()->andReturnSelf()
                    ->shouldReceive('onQueue')->once()->andReturnSelf()
                    ->shouldReceive('add')->once()->withArgs(fn($job) => $job instanceof PriceUnload)
                    ->getMock()
            );

        $user = User::factory()->hasEmails(1, [
            'open' => true,
            'address' => 'email@gmail.com',
            'password' => 'qwerty1234'
        ])->create();
        $supplier = Supplier::factory()->for($user)->create();
        $emailSupplier = EmailSupplier::factory()->for($user->emails->first(), 'mainEmail')->for($supplier)->create();
        $report = Task::factory()->for($supplier, 'taskable')->create([
            'status' => ReportStatus::running,
            'type' => TaskTypes::SupplierUnload
        ]);
        $item = Item::factory()->for($user)->for($supplier)->create();
        $market = OzonMarket::factory()->for($user)->create(['open' => true, 'close' => false]);
        $ozonItem = OzonItem::factory()->for($item, 'itemable')->for($market, 'market')->create();

        $service = app(MarketContract::class);
        $service->unload($emailSupplier, $report);
    }

    public function test_incorrect_unload_ozon(): void
    {
        Queue::fake();

        Bus::shouldReceive('batch')
            ->once()
            ->andReturn(
                \Mockery::mock()
                    ->shouldReceive('dispatch')->once()->andReturnSelf()
                    ->shouldReceive('onQueue')->once()->andReturnSelf()
                    ->shouldReceive('add')->never()->withArgs(fn($job) => $job instanceof PriceUnload)
                    ->getMock()
            );

        $user = User::factory()->hasEmails(1, [
            'open' => true,
            'address' => 'email@gmail.com',
            'password' => 'qwerty1234'
        ])->create();
        $supplier = Supplier::factory()->for($user)->create();
        $emailSupplier = EmailSupplier::factory()->for($user->emails->first(), 'mainEmail')->for($supplier)->create();
        $report = Task::factory()->for($supplier, 'taskable')->create([
            'status' => ReportStatus::running,
            'type' => TaskTypes::SupplierUnload
        ]);
        $item = Item::factory()->for($user)->for($supplier)->create();
        $market = OzonMarket::factory()->for($user)->create(['open' => false, 'close' => false]);
        $market2 = OzonMarket::factory()->for($user)->create(['open' => true, 'close' => true]);
        OzonItem::factory()->for($item, 'itemable')->for($market, 'market')->create();
        OzonItem::factory()->for($item, 'itemable')->for($market2, 'market')->create();

        $service = app(MarketContract::class);
        $service->unload($emailSupplier, $report);
    }

    public function test_correct_unload_ozon_cancelled()
    {
        Queue::fake();

        $this->expectException(ReportCancelled::class);

        $user = User::factory()->hasEmails(1, [
            'open' => true,
            'address' => 'email@gmail.com',
            'password' => 'qwerty1234'
        ])->create();
        $supplier = Supplier::factory()->for($user)->create();
        $emailSupplier = EmailSupplier::factory()->for($user->emails->first(), 'mainEmail')->for($supplier)->create();
        $report = Task::factory()->for($supplier, 'taskable')->create([
            'status' => ReportStatus::running,
            'type' => TaskTypes::SupplierUnload
        ]);
        $item = Item::factory()->for($user)->for($supplier)->create();
        $market = OzonMarket::factory()->for($user)->create(['open' => true, 'close' => false]);
        $ozonItem = OzonItem::factory()->for($item, 'itemable')->for($market, 'market')->create();

        $report->update(['status' => ReportStatus::cancelled]);

        $service = app(MarketContract::class);

        $service->unload($emailSupplier, $report);
    }
}
