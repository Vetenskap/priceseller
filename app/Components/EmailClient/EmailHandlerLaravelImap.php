<?php

namespace App\Components\EmailClient;

use App\Services\SupplierService;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Webklex\IMAP\Facades\Client;
use Webklex\PHPIMAP\Attachment;
use Webklex\PHPIMAP\Exceptions\ConnectionFailedException;
use Webklex\PHPIMAP\Exceptions\FolderFetchingException;
use Webklex\PHPIMAP\Exceptions\GetMessagesFailedException;
use Webklex\PHPIMAP\Exceptions\RuntimeException;
use Webklex\PHPIMAP\Folder;
use Webklex\PHPIMAP\Message;
use Webklex\PHPIMAP\Support\MessageCollection;
use ZipArchive;

class EmailHandlerLaravelImap
{
    public $connection;
    public Filesystem $storage;
    const ZIP_TYPES = ['application/x-zip-compressed', 'application/zip'];
    const TABLE_TYPES = [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/octet-stream',
        'application/excel',
        'text/csv'
    ];

    /**
     * @throws ConnectionFailedException
     */
    public function __construct(string $address, string $password, ?Filesystem $storage = null)
    {
        $this->storage = $storage ?? Storage::disk('public');

        $this->connection = Client::make([
            'host' => 'imap.mail.ru',
            'port' => 993,
            'protocol' => 'imap',
            'encryption' => 'ssl',
            'validate_cert' => true,
            'username' => $address,
            'password' => $password,
            'authentication' => null,
            "timeout" => 30,
        ]);

        $this->connection->connect();

    }

    /**
     * @throws RuntimeException
     * @throws FolderFetchingException
     * @throws ConnectionFailedException
     */
    public function getFoldersIterator(): \Iterator
    {
        return $this->connection->getFolders()->paginate()->getIterator();
    }

    /**
     * @throws RuntimeException
     * @throws GetMessagesFailedException
     * @throws ConnectionFailedException
     */
    public function getUnseenMessagesFromIterator(Folder $folder, string $email): \Iterator
    {
        return $folder->messages()->unseen()->from($email)->fetchOrderDesc()->paginate()->getIterator();
    }


    /**
     * @param string $supplierEmail
     * @param string $supplierFilename
     * @return string|null
     * @throws \Webklex\PHPIMAP\Exceptions\ConnectionFailedException
     * @throws \Webklex\PHPIMAP\Exceptions\EventNotFoundException
     * @throws \Webklex\PHPIMAP\Exceptions\FolderFetchingException
     * @throws \Webklex\PHPIMAP\Exceptions\GetMessagesFailedException
     * @throws \Webklex\PHPIMAP\Exceptions\MessageFlagException
     * @throws \Webklex\PHPIMAP\Exceptions\RuntimeException
     */
    public function getNewPrice(string $supplierEmail, string $supplierFilename): ?string
    {

        /** @var Folder $folder */
        foreach ($this->getFoldersIterator() as $folder) {

            // TODO if ($folder->name)


            /** @var Message $message */
            foreach ($this->getUnseenMessagesFromIterator($folder, $supplierEmail) as $message) {
                if ($message->hasAttachments()) {

                    /** @var Attachment $file */
                    foreach ($message->getAttachments()->paginate()->getIterator() as $file) {

                        $name = $file->getName();

                        if (!Str::contains($name, $supplierFilename)) {
                            continue;
                        }

                        if (
                            !in_array($file->getContentType(), self::TABLE_TYPES) &&
                            !in_array($file->getContentType(), self::ZIP_TYPES)
                        ) continue;

                        $fullPath = SupplierService::PATH . uniqid() . '_';

                        $this->storage->put($fullPath . Str::ascii($name), $file->getContent());

                        if (in_array($file->getContentType(), self::ZIP_TYPES)) {

                            $zip = new ZipArchive;

                            $res = $zip->open($this->storage->path($fullPath . Str::ascii($name)));

                            if ($res === TRUE) {

                                $nameZip = $zip->getNameIndex(0);

                                $this->storage->put($fullPath . Str::ascii($nameZip), $zip->getFromIndex(0));

                                $zip->close();

                                $this->storage->delete($fullPath . Str::ascii($name));

                                $fullPath = $fullPath . Str::ascii($nameZip);

                            } else {
                                Context::push('unload', [
                                    'Ошибка' => 'Ошибка открытия архива',
                                ]);
                            }
                        } else {

                            $fullPath = $fullPath . Str::ascii($name);

                        }

                        $message->setFlag('Seen');

                        return $fullPath;
                    }

                }

                unset($message);
            }

            unset($folder);
        }

        return null;
    }
}
