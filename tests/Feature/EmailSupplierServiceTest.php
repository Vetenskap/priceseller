<?php

namespace Tests\Feature;

use App\Models\EmailSupplier;
use App\Models\Supplier;
use App\Services\EmailSupplierService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmailSupplierServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_rossko(): void
    {
        $rossko = EmailSupplier::find(3);
        $path = Storage::disk('public')->path('test/rossko_price.xlsx');
        $service = new EmailSupplierService($rossko, $path);
        $service->unload();
        $this->assertTrue($rossko->supplier->items()->where('updated', 1)->count() === 4);
    }
}
