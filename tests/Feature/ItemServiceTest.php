<?php

namespace Tests\Feature;

use App\Imports\ItemsImport;
use App\Imports\SupplierImport;
use App\Services\ItemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ItemServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
//        $service = new ItemService();
//        $service->massCreateFromFile();
//        (new ItemsImport)->queue('test/my_store.xlsx', 'public');
        (new SupplierImport)->import('test/test_voshod.xlsx', 'public');
    }
}
