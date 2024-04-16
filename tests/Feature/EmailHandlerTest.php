<?php

namespace Tests\Feature;

use App\Components\EmailHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmailHandlerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_connect(): void
    {
        $login = env('TEST_EMAIL_LOGIN');
        $password = env('TEST_EMAIL_PASSWORD');
        $handler = new EmailHandler($login, $password);

        $this->assertTrue($handler->connection instanceof \IMAP\Connection);

//        $mails = $handler->getMails();
//
//        dd($mails);
    }

    public function test_unseen_without_file()
    {
        $login = env('TEST_EMAIL_LOGIN');
        $password = env('TEST_EMAIL_PASSWORD');
        $handler = new EmailHandler($login, $password);
        $mails = $handler->getMails();

        $this->assertNotEmpty($mails);

        $mailbox = $handler->getMailBox($mails[0]);

        $this->assertTrue($mailbox === 'vetenskap2@yandex.ru');

        $structure = $handler->fetchStructure($mails[0]);

        $this->assertNotEmpty($structure);

        $filePart = $handler->findFilePart($structure);

        $this->assertEmpty($filePart);
    }

    public function test_seen_with_file()
    {
        $login = env('TEST_EMAIL_LOGIN');
        $password = env('TEST_EMAIL_PASSWORD');
        $handler = new EmailHandler($login, $password, Storage::disk('public'));
        $mails = $handler->getMails('SEEN');

        $this->assertNotEmpty($mails);

        $mailbox = $handler->getMailBox($mails[0]);

        $this->assertTrue($mailbox === 'vetenskap2@yandex.ru');

        $structure = $handler->fetchStructure($mails[0]);

        $this->assertNotEmpty($structure);

        $filePart = $handler->findFilePart($structure);

        $this->assertNotEmpty($filePart);

        $filename = $handler->getFileName($filePart['name']);

        $this->assertTrue($filename === 'BERG_praice.xlsx');

        $messageEncrypt = $handler->fetchBody($mails[0], $filePart);

        $this->assertNotEmpty($messageEncrypt);

        $message = $handler->decodeMessage($messageEncrypt);

        $this->assertNotEmpty($message);

        $path = $handler->saveFile($message, $filename, 1);

        $this->assertNotEmpty($path);
        $this->assertTrue(Storage::disk('public')->exists($path));

        Storage::disk('public')->delete($path);
    }

    public function test_dont_found_email_supplier()
    {
        $login = env('TEST_EMAIL_LOGIN');
        $password = env('TEST_EMAIL_PASSWORD');
        $handler = new EmailHandler($login, $password, Storage::disk('public'));
        $path = $handler->getNewPrice('anything supplier', 'anything filename', 1);

        $this->assertEmpty($path);
    }

    public function test_dont_found_file()
    {
        $login = env('TEST_EMAIL_LOGIN');
        $password = env('TEST_EMAIL_PASSWORD');

        $handler = new EmailHandler($login, $password);
        $path = $handler->getNewPrice('vetenskap2@yandex.ru', 'anything filename', 1);

        $this->assertEmpty($path);
    }

    public function test_found_file()
    {
        $login = env('TEST_EMAIL_LOGIN');
        $password = env('TEST_EMAIL_PASSWORD');

        $handler = new EmailHandler($login, $password);
        $path = $handler->getNewPrice('vetenskap2@yandex.ru', 'BERG_praice.xlsx', 1, 'SEEN');

        $this->assertNotEmpty($path);
        $this->assertTrue(Storage::disk('public')->exists($path));

        Storage::disk('public')->delete($path);
    }
}
