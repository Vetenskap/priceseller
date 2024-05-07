<?php

namespace Tests\Feature;

use App\Services\EmailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EmailServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
//    public function test_trast(): void
//    {
//        $service = new EmailService();
//        $service->allUnload(1);
//    }

    public function test_voshod(): void
    {
        $service = new EmailService();
        $service->unloadOneSupplier(2);
    }
}
