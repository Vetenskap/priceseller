<?php

namespace Tests\Feature;

use App\Components\EmailClient\EmailHandlerLaravelImap;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmailHandlerLaravelImapTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_dont_found_file()
    {
        $handler = new EmailHandlerLaravelImap('vetenskap@bk.ru', 'WmGjJBFan0EGta6BtDUw', Storage::disk('public'));
        $path = $handler->getNewPrice('vetenskap2@yandex.ru', 'any');

        $this->assertEmpty($path);
    }

    public function test_found_file()
    {
        $handler = new EmailHandlerLaravelImap('vetenskap@bk.ru', 'WmGjJBFan0EGta6BtDUw', Storage::disk('public'));
        $path = $handler->getNewPrice('vetenskap2@yandex.ru', 'BERG_praice.xlsx', 'SEEN');

        $this->assertNotEmpty($path);

        Storage::disk('public')->delete($path);
    }

}
