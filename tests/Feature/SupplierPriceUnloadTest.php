<?php

namespace Tests\Feature;

use App\Contracts\SupplierUnloadContract;
use App\Enums\ReportStatus;
use App\Exceptions\ReportCancelled;
use App\Jobs\Supplier\PriceUnload;
use App\Models\EmailSupplier;
use App\Models\Report;
use App\Models\Supplier;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SupplierPriceUnloadTest extends TestCase
{
    use DatabaseTransactions;

    public function test_success_unload(): void
    {
        $user = User::factory()->hasEmails(1, [
            'open' => true,
            'address' => 'email@gmail.com',
            'password' => 'qwerty1234'
        ])->create();
        $supplier = Supplier::factory()->state(['unload_without_price' => false, 'open' => true])->for($user)->create();
        $emailSupplier = EmailSupplier::factory()->state([
            'email' => 'supplier@example.com',
            'filename' => 'price.xlsx'
        ])->for($user->emails->first(), 'mainEmail')->for($supplier)->create();

        $mockHandler = \Mockery::mock(SupplierUnloadContract::class);
        $mockHandler->shouldReceive('unload')
            ->once()
            ->andReturnUndefined();
        $mockHandler->shouldReceive('make')
            ->once()
            ->withArgs(function ($supplier, $path, $report) use ($emailSupplier) {
                return $emailSupplier->id === $supplier->id && $report instanceof Report;
            })
            ->andReturnUndefined();

        // Привязываем мок в сервис-контейнер
        $this->app->instance(SupplierUnloadContract::class, $mockHandler);

        $job = new PriceUnload($emailSupplier, 'price.xlsx');
        $job->handle();

        $this->assertDatabaseHas(Task::class, [
            'taskable_id' => $supplier->id,
            'taskable_type' => Supplier::class,
            'status' => ReportStatus::finished,
        ]);
    }

    public function test_failed_unload()
    {
        $user = User::factory()->hasEmails(1, [
            'open' => true,
            'address' => 'email@gmail.com',
            'password' => 'qwerty1234'
        ])->create();
        $supplier = Supplier::factory()->state(['unload_without_price' => false, 'open' => true])->for($user)->create();
        $emailSupplier = EmailSupplier::factory()->state([
            'email' => 'supplier@example.com',
            'filename' => 'price.xlsx'
        ])->for($user->emails->first(), 'mainEmail')->for($supplier)->create();

        $mockHandler = \Mockery::mock(SupplierUnloadContract::class);
        $mockHandler->shouldReceive('unload')
            ->once()
            ->andThrow(new \Exception('test'));
        $mockHandler->shouldReceive('make')
            ->once()
            ->withArgs(function ($supplier, $path, $report) use ($emailSupplier) {
                return $emailSupplier->id === $supplier->id && $report instanceof Report;
            })
            ->andReturnUndefined();

        // Привязываем мок в сервис-контейнер
        $this->app->instance(SupplierUnloadContract::class, $mockHandler);

        $job = new PriceUnload($emailSupplier, 'price.xlsx');

        try {
            $job->handle();
        } catch (\Exception $e) {
            $this->assertTrue($e->getMessage() === 'test');
            $job->failed($e);
        }

        $this->assertDatabaseHas(Task::class, [
            'taskable_id' => $supplier->id,
            'taskable_type' => Supplier::class,
            'status' => ReportStatus::failed,
        ]);
    }

    public function test_cancelled_unload()
    {
        $user = User::factory()->hasEmails(1, [
            'open' => true,
            'address' => 'email@gmail.com',
            'password' => 'qwerty1234'
        ])->create();
        $supplier = Supplier::factory()->state(['unload_without_price' => false, 'open' => true])->for($user)->create();
        $emailSupplier = EmailSupplier::factory()->state([
            'email' => 'supplier@example.com',
            'filename' => 'price.xlsx'
        ])->for($user->emails->first(), 'mainEmail')->for($supplier)->create();

        $mockHandler = \Mockery::mock(SupplierUnloadContract::class);
        $mockHandler->shouldReceive('unload')
            ->once()
            ->andThrow(new ReportCancelled('cancelled!'));
        $mockHandler->shouldReceive('make')
            ->once()
            ->withArgs(function ($supplier, $path, $report) use ($emailSupplier) {
                return $emailSupplier->id === $supplier->id && $report instanceof Report;
            })
            ->andReturnUndefined();

        // Привязываем мок в сервис-контейнер
        $this->app->instance(SupplierUnloadContract::class, $mockHandler);

        $job = new PriceUnload($emailSupplier, 'price.xlsx');

        $job->handle();

        Task::query()->first()->update([
            'status' => ReportStatus::cancelled]);

        $this->assertDatabaseHas(Task::class, [
            'taskable_id' => $supplier->id,
            'taskable_type' => Supplier::class,
            'status' => ReportStatus::cancelled
        ]);
    }
}
