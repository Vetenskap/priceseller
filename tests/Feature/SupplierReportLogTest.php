<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\SupplierReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SupplierReportLogTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $supplier = User::where('email', 'vetenskap2@gmail.com')->first()->suppliers()->first();
        SupplierReportService::new($supplier, 'random_path');
        SupplierReportService::addLog($supplier, 'test');
    }
}
