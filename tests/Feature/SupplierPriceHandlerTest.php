<?php

namespace Tests\Feature;

use App\Components\SupplierPriceHandler;
use App\Exceptions\Components\SupplierPriceHandler\SupplierPriceHandlerException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SupplierPriceHandlerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_exception(): void
    {
        $this->expectException(SupplierPriceHandlerException::class);

        $handler = new SupplierPriceHandler('other', 1, 'disk');
    }

    public function test_xlsx_handle()
    {
        $mock = $this->getMockBuilder(SupplierPriceHandler::class)
            ->setConstructorArgs(['test/test_trast.xlsx', '9bd1f334-9270-429e-b225-8382d3f16ba9', 'public'])
            ->onlyMethods(['xlsxHandle', 'otherHandle'])
            ->getMock();

        $mock->expects($this->once())->method('xlsxHandle')->willReturnSelf();

        $mock->handle();
    }

    public function test_other_handle()
    {
        $mock = $this->getMockBuilder(SupplierPriceHandler::class)
            ->setConstructorArgs(['test/test_trast.txt', '9bd1f334-9270-429e-b225-8382d3f16ba9', 'public'])
            ->onlyMethods(['otherHandle', 'xlsxHandle'])
            ->getMock();

        $mock->expects($this->once())->method('otherHandle')->willReturnSelf();

        $mock->handle();
    }
}
