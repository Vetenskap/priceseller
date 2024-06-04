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
        'application/excel'
    ];

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
     * @param string $supplierEmail
     * @param string $supplierFilename
     * @param string $criteria
     * @return string|null
     * @throws \Webklex\PHPIMAP\Exceptions\ConnectionFailedException
     * @throws \Webklex\PHPIMAP\Exceptions\EventNotFoundException
     * @throws \Webklex\PHPIMAP\Exceptions\FolderFetchingException
     * @throws \Webklex\PHPIMAP\Exceptions\GetMessagesFailedException
     * @throws \Webklex\PHPIMAP\Exceptions\MessageFlagException
     * @throws \Webklex\PHPIMAP\Exceptions\RuntimeException
     */
    public function getNewPrice(string $supplierEmail, string $supplierFilename, string $criteria = 'UNSEEN'): ?string
    {
        Context::forget('unload');
        Context::push('unload', [
            'Поставщик' => $supplierEmail,
        ]);

        /** @var Folder $folder */
        foreach ($this->connection->getFolders()->paginate()->getIterator() as $folder) {

            // TODO if ($folder->name)

            //Get all Messages of the current Mailbox $folder
            /** @var MessageCollection $messages */
            if ($criteria === 'UNSEEN') {
                $query = $folder->messages()->unseen()->from($supplierEmail)->fetchOrderDesc();
            } else if ($criteria === 'SEEN') {
                $query = $folder->messages()->seen()->from($supplierEmail)->fetchOrderDesc();
            }

            /** @var Message $message */
            foreach ($query->paginate()->getIterator() as $message) {
                if ($message->hasAttachments()) {

                    Context::push('unload', ['Информация' => 'Есть вложения']);

                    /** @var Attachment $file */
                    foreach ($message->getAttachments()->paginate()->getIterator() as $file) {

                        $name = $file->getName();

                        Context::push('unload', [
                            'Название файла' => $name,
                            'Наше название' => $supplierFilename
                        ]);

                        if (!Str::contains($name, $supplierFilename)) {
                            continue;
                        }

                        Context::push('unload', [
                            'Тип файла' => $file->getContentType(),
                        ]);

                        if (
                            !in_array($file->getContentType(), self::TABLE_TYPES) &&
                            !in_array($file->getContentType(), self::ZIP_TYPES)
                        ) continue;

                        $fullPath = SupplierService::PATH . uniqid() . '_';

                        $this->storage->put($fullPath . Str::ascii($name), $file->getContent());

                        Context::push('unload', [
                            'Информация' => 'Файл сохранен',
                            'Путь' => $fullPath . Str::ascii($name)
                        ]);

                        if (in_array($file->getContentType(), self::ZIP_TYPES)) {

                            $zip = new ZipArchive;

                            $res = $zip->open($this->storage->path($fullPath . Str::ascii($name)));

                            if ($res === TRUE) {

                                $nameZip = $zip->getNameIndex(0);

                                $this->storage->put($fullPath . Str::ascii($nameZip), $zip->getFromIndex(0));

                                $zip->close();

                                $this->storage->delete($fullPath . Str::ascii($name));

                                $fullPath = $fullPath . Str::ascii($nameZip);

                                Context::push('unload', [
                                    'Информация' => 'Архивный файл сохранен',
                                    'Путь' => $fullPath
                                ]);

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

        Log::debug('Прайс не найден');

        return null;
    }
}
