<?php

namespace App\Console\Commands;

use App\Components\EmailClient\EmailHandlerLaravelImap;
use Illuminate\Console\Command;
use Webklex\PHPIMAP\Attachment;
use Webklex\PHPIMAP\Folder;
use Webklex\PHPIMAP\Message;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {address} {password} {supplier_email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $imap = new EmailHandlerLaravelImap($this->argument('address'), $this->argument('password'));

        /** @var Folder $folder */
        foreach ($imap->getFoldersIterator() as $folder) {

            dump("Папка: " . $folder->name);

            /** @var Message $message */
            foreach ($imap->getUnseenMessagesFromIterator($folder, $this->argument('supplier_email')) as $key => $message) {
                dump("Непрочитанное сообщение №" . $key);
                dump($message->getFrom());

                if ($message->hasAttachments()) {

                    dump("Есть вложения");

                    /** @var Attachment $file */
                    foreach ($message->getAttachments()->paginate()->getIterator() as $key2 => $file) {

                        dump("Вложение №" . $key2);

                        dump("Имя вложения: " . $file->getName());
                    }
                } else {

                    dump("Нет вложений");

                }
            }
        }
    }
}
