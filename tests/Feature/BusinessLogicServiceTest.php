<?php

namespace Tests\Feature;

use App\Jobs\Email\CheckEmails;
use App\Models\Permission;
use App\Models\Supplier;
use App\Models\User;
use App\Services\BusinessLogicService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class BusinessLogicServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_correct_user_check_emails()
    {
        Queue::fake();

        $permissionAdmin = Permission::where('value', 'admin')->first();
        $permissionSub = Permission::where('value', 'ozon_five_markets')->first();
        $userAdmin = User::factory()->hasEmails(1, ['open' => true])->create();
        $userSub = User::factory()->hasEmails(1, ['open' => true])->create();
        $userAdmin->permissions()->attach($permissionAdmin->id, [
            'expires' => now()->addHour()
        ]);
        $userSub->permissions()->attach($permissionSub->id, [
            'expires' => now()->addHour()
        ]);

        BusinessLogicService::usersEmailsUnload();

        Queue::assertPushed(CheckEmails::class, 2);
    }

    public function test_permission_expired()
    {
        Queue::fake();

        $permissionAdmin = Permission::where('value', 'admin')->first();
        $permissionSub = Permission::where('value', 'ozon_five_markets')->first();
        $userAdmin = User::factory()->hasEmails(1, ['open' => true])->create();
        $userSub = User::factory()->hasEmails(1, ['open' => true])->create();
        $userAdmin->permissions()->attach($permissionAdmin->id, [
            'expires' => now()->addHour()
        ]);
        $userSub->permissions()->attach($permissionSub->id, [
            'expires' => now()->subHour()
        ]);

        BusinessLogicService::usersEmailsUnload();

        Queue::assertPushed(CheckEmails::class, 1);
    }

    public function test_permission_expired_and_email_closed()
    {
        Queue::fake();

        $permissionAdmin = Permission::where('value', 'admin')->first();
        $permissionSub = Permission::where('value', 'ozon_five_markets')->first();
        $userAdmin = User::factory()->hasEmails(1, ['open' => false])->create();
        $userSub = User::factory()->hasEmails(1, ['open' => true])->create();
        $userAdmin->permissions()->attach($permissionAdmin->id, [
            'expires' => now()->addHour()
        ]);
        $userSub->permissions()->attach($permissionSub->id, [
            'expires' => now()->subHour()
        ]);

        BusinessLogicService::usersEmailsUnload();

        Queue::assertPushed(CheckEmails::class, 0);
    }

    public function test_supplier_unload_without_price()
    {
        Artisan::shouldReceive('call')
            ->once();

        $userSub = User::factory()->create();
        $supplier = Supplier::factory()->state(['unload_without_price' => true])->for($userSub)->create();
        Carbon::setTestNow('2024-12-13 14:00:00');

        BusinessLogicService::usersEmailsUnload();
    }
}
