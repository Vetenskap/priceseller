<?php

namespace Tests\Feature;

use App\Jobs\Supplier\MarketsEmailSupplierUnload;
use App\Jobs\Supplier\PriceUnload;
use App\Models\Email;
use App\Models\EmailSupplier;
use App\Models\Supplier;
use App\Models\SupplierReport;
use App\Models\User;
use App\Services\EmailSupplierService;
use App\Services\SupplierReportService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;

class PriceUnloadTest extends TestCase
{
    use DatabaseTransactions;

    public function test_job_is_dispatched_correctly()
    {
        Queue::fake();

        $emailSupplierId = 1;
        $path = 'path/to/file.xlsx';

        PriceUnload::dispatch($emailSupplierId, $path);

        Queue::assertPushed(PriceUnload::class, function ($job) use ($emailSupplierId, $path) {
            return $job->emailSupplierId === $emailSupplierId && $job->path === $path;
        });
    }

    public function test_job_has_unique_id()
    {
        Queue::fake();

        $job = new PriceUnload(1, 'path/to/file.xlsx');

        $this->assertEquals('1price_unload', $job->uniqueId());
        $this->assertEquals(600, $job->uniqueFor);
    }

    public function test_job_exits_if_supplier_report_exists()
    {
        Queue::fake();

        $supplierReportServiceMock = $this->partialMock(SupplierReportService::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')
                ->once()
                ->withArgs(function ($supplier) {
                    return $supplier instanceof Supplier;
                })
                ->andReturn(new SupplierReport());
        });

        $user = User::factory()->create();
        $email = Email::factory()->for($user)->create();
        $supplier = Supplier::factory()->for($user)->create();
        $emailSupplier = EmailSupplier::factory()->for($email)->for($supplier)->create();

        $job = new PriceUnload($emailSupplier->id, 'path/to/file.xlsx');

        $job->handle();

        $supplierReportServiceMock->shouldNotHaveReceived('new');
    }
}
