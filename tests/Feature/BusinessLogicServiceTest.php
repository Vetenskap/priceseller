<?php

namespace Tests\Feature;

use App\Jobs\Supplier\MarketsUnload;
use App\Models\Permission;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;
use App\Jobs\Email\CheckEmails;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

class BusinessLogicServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_usersEmailsUnload_dispatches_CheckEmails_for_correct_users()
    {
        Queue::fake();

        $ozonFiveMarketsPermission = Permission::where('value', 'ozon_five_markets')->firstOrFail();
        $adminPermission = Permission::where('value', 'admin')->firstOrFail();

        $subUser = User::factory()->hasEmails(1, ['open' => true])->create();

        $adminUser = User::factory()->hasEmails(1, ['open' => true])->create();

        $subUser->permissions()->attach($ozonFiveMarketsPermission->id, [
            'expires' => now()->addDay(),
        ]);

        $adminUser->permissions()->attach($adminPermission->id, [
            'expires' => now()->addDay(),
        ]);

        \App\Services\BusinessLogicService::usersEmailsUnload();

        Queue::assertPushed(CheckEmails::class, 2);
    }

    public function test_usersEmailsUnload_dispatches_CheckEmails_for_incorrect_users()
    {
        Queue::fake();

        $ozonFiveMarketsPermission = Permission::where('value', 'ozon_five_markets')->firstOrFail();
        $adminPermission = Permission::where('value', 'admin')->firstOrFail();

        $subUser = User::factory()->hasEmails(1, ['open' => false])->create();
        $adminUser = User::factory()->hasEmails(1, ['open' => false])->create();

        $unsubUser = User::factory()->hasEmails(1, ['open' => true])->create();

        $subUser->permissions()->attach($ozonFiveMarketsPermission->id, [
            'expires' => now()->addDay(),
        ]);

        $adminUser->permissions()->attach($adminPermission->id, [
            'expires' => now()->addDay(),
        ]);

        \App\Services\BusinessLogicService::usersEmailsUnload();

        Queue::assertPushed(CheckEmails::class, 0);
    }

    public function test_usersEmailsUnload_dispatches_MarketsUnload_for_correct_suppliers()
    {
        Queue::fake();

        $this->partialMock(\App\Services\SupplierService::class, function (MockInterface $mock) {
            $mock->shouldReceive('setAllItemsUpdated')
                ->once()
                ->withArgs(function ($supplier) {
                    return $supplier instanceof \App\Models\Supplier;
                });
        });

        // Получаем разрешения
        $ozonFiveMarketsPermission = Permission::where('value', 'ozon_five_markets')->firstOrFail();

        // Создаем пользователя с разрешением
        $user = User::factory()->create();
        $user->permissions()->attach($ozonFiveMarketsPermission, ['expires' => now()->addDay()]);

        // Создаем поставщиков, связанных с пользователем
        Supplier::factory()->for($user)->create([
            'open' => true,
            'unload_without_price' => true,
        ]); // Подходящий поставщик

        Supplier::factory()->for($user)->create([
            'open' => true,
            'unload_without_price' => false,
        ]); // Неподходящий поставщик

        // Устанавливаем тестовую дату
        now()->setTestNow('2023-01-01 00:00:00'); // Set time to "00:00"

        // Вызываем метод
        \App\Services\BusinessLogicService::usersEmailsUnload();

        // Проверяем, что задача была отправлена только для подходящего поставщика
        Queue::assertPushed(MarketsUnload::class, 1);
    }

    public function test_usersEmailsUnload_calls_setAllItemsUpdated()
    {
        Queue::fake();

        $this->partialMock(\App\Services\SupplierService::class, function (MockInterface $mock) {
            $mock->shouldReceive('setAllItemsUpdated')
                ->once()
                ->withArgs(function ($supplier) {
                    return $supplier instanceof \App\Models\Supplier;
                });
        });

        // Получаем разрешения
        $ozonFiveMarketsPermission = Permission::where('value', 'ozon_five_markets')->firstOrFail();

        // Создаем пользователя с разрешением
        $user = User::factory()->create();
        $user->permissions()->attach($ozonFiveMarketsPermission->id, ['expires' => now()->addDay()]);

        Supplier::factory()->for($user)->create([
            'open' => true,
            'unload_without_price' => true,
        ]);

        Supplier::factory()->for($user)->create([
            'open' => true,
            'unload_without_price' => false,
        ]);

        Supplier::factory()->for($user)->create([
            'open' => false,
            'unload_without_price' => true,
        ]);

        now()->setTestNow('2023-01-01 00:00:00'); // Set time to "00:00"
        \App\Services\BusinessLogicService::usersEmailsUnload();
        Queue::assertPushed(MarketsUnload::class, 1);
    }
}
