<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;
use Webklex\PHPIMAP\Attachment;
use Webklex\PHPIMAP\Folder;
use Webklex\PHPIMAP\Message;

class TestEmailUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email-user {userId}';

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
        $user = User::findOrFail($this->argument('userId'));
        $email = $user->emails()->first();

        $client = Client::make([
            'host' => 'imap.mail.ru',
            'port' => 993,
            'protocol' => 'imap',
            'encryption' => 'ssl',
            'validate_cert' => true,
            'username' => $email->address,
            'password' => $email->password,
            'authentication' => null,
            "timeout" => 30,
        ])->connect();

        /** @var Folder $folder */
        foreach ($client->getFolders()->paginate()->getIterator() as $folder) {

            /** @var Message $message */
            foreach ($folder->messages()->unseen()->fetchOrderDesc()->paginate()->getIterator() as $message) {

                $this->info($message->getFrom()->getName());

                if ($message->hasAttachments()) {

                    /** @var Attachment $file */
                    foreach ($message->getAttachments()->paginate()->getIterator() as $file) {

                        $name = $file->getName();
                        $this->info($name);

                    }

                }
            }
        }
    }
}
