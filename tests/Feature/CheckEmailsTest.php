<?php

namespace Tests\Feature;

use App\Contracts\EmailHandlerContract;
use App\Jobs\Email\CheckEmails;
use App\Jobs\Supplier\PriceUnload;
use App\Models\EmailSupplier;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CheckEmailsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_correct_user_supplier_price_unload()
    {
        Queue::fake();

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

        $mockHandler = \Mockery::mock(EmailHandlerContract::class);
        $mockHandler->shouldReceive('getNewPrice')
            ->once()
            ->with('supplier@example.com', 'price.xlsx', 'email@gmail.com', 'qwerty1234')
            ->andReturn('path/to/price/file.xlsx');

        // Привязываем мок в сервис-контейнер
        $this->app->instance(EmailHandlerContract::class, $mockHandler);

        $job = new CheckEmails($user);
        $job->handle();

        $this->assertTrue($job->uniqueId() === $user->id . 'check_emails');

        Queue::assertPushed(PriceUnload::class, function ($job) use ($emailSupplier) {
            return $job->emailSupplier->id === $emailSupplier->id
                && $job->path === 'path/to/price/file.xlsx';
        });
    }
}
