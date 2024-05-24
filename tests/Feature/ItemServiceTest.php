<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Item\ItemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $service = new ItemService(User::find(7));
        $service->importItems('fa8614fb-0124-48ee-864d-b6726338d3ce', 'xlsx');
    }
}
