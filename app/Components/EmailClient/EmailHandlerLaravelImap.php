<?php

namespace App\Components\EmailClient;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Webklex\PHPIMAP\Address;
use Webklex\PHPIMAP\Attachment;
use Webklex\PHPIMAP\Client;
use Webklex\PHPIMAP\Folder;
use Webklex\PHPIMAP\Message;
use Webklex\PHPIMAP\Support\FolderCollection;
use Webklex\PHPIMAP\Support\MessageCollection;

class EmailHandlerLaravelImap
{
    public $connection;
    public Filesystem $storage;
    const SAVE_PATH = "/users/prices/";

    public function __construct(string $address, string $password, ?Filesystem $storage = null)
    {

        Context::push('emailHandler', [
            'address' => $address,
            'password' => $password,
        ]);

        $this->storage = $storage ?? Storage::disk('public');

        $this->connection = \Webklex\IMAP\Facades\Client::make([
            'host'  => 'imap.mail.ru',
            'port'  => 993,
            'protocol'  => 'imap',
            'encryption'    => 'ssl',
            'validate_cert' => true,
            'username' => $address,
            'password' => $password,
            'authentication' => null,
            "timeout" => 30,
        ]);

        $this->connection->connect();

    }

    public function getNewPrice(string $supplierEmail, string $supplierFilename, $criteria = 'UNSEEN'): ?string
    {

        Context::push('emailHandler', [
            'supplierEmail' => $supplierEmail,
            'supplierFilename' => $supplierFilename
        ]);

        /** @var FolderCollection $folders */
        $folders = $this->connection->getFolders();

        /** @var Folder $folder */
        foreach($folders as $folder){

            // TODO if ($folder->name)

            //Get all Messages of the current Mailbox $folder
            /** @var MessageCollection $messages */
            if ($criteria === 'UNSEEN') {
                $messages = $folder->messages()->unseen()->get();
            } else if ($criteria === 'SEEN') {
                $messages = $folder->messages()->seen()->get();
            }

            /** @var Message $message */
            foreach($messages as $message){

                /** @var Address $from */
                $from = $message->getFrom()->get()[0];

                if (!Str::contains($from->mail, $supplierEmail)) {
                    continue;
                }

                if ($message->hasAttachments()) {

                    /** @var Attachment $file */
                    $file = $message->getAttachments()->first();

                    if (!Str::contains($file->getName(), $supplierFilename)) {
                        continue;
                    }

                    $fullPath = self::SAVE_PATH . uniqid() . '_' . $file->getName();

                    $this->storage->put($fullPath, $file->getContent());

                    return $fullPath;

                }
            }
        }

        return null;
    }
}
