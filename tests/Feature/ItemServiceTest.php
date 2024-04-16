<?php

namespace Tests\Feature;

use App\Services\ItemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
        dd(get_class_methods(Excel::class));
    }
}
