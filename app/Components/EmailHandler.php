<?php

namespace App\Components;

use App\Exceptions\EmailClient\EmailClientException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use IMAP\Connection;

class EmailHandler
{
    public ?Connection $connection = null;
    public Filesystem $storage;

    public function __construct(string $address, string $password, ?Filesystem $storage = null)
    {
        Context::push('emailHandler', [
            'address' => $address,
            'password' => $password,
        ]);

        $this->connection = $this->connect($address, $password);
        $this->storage = $storage ?? Storage::disk('public');
    }

    public function getMails(string $criteria = 'UNSEEN'): array
    {
        if (!in_array($criteria, ['SEEN', 'UNSEEN'])) throw new EmailClientException('not found criteria');
        return imap_search($this->connection, $criteria);
    }

    public function getNewPrice(string $supplierEmail, string $supplierFilename, int $userId, $criteria = 'UNSEEN'): ?string
    {

        Context::push('emailHandler', [
            'supplierEmail' => $supplierEmail,
            'supplierFilename' => $supplierFilename
        ]);

        foreach ($this->getMails($criteria) as $mailId) {

            $mailbox = $this->getMailBox($mailId);

            if ($mailbox !== $supplierEmail) {
                continue;
            }

            $structure = $this->fetchStructure($mailId);

            Context::add('structure', json_encode($structure, JSON_UNESCAPED_UNICODE));

            $filePart = $this->findFilePart($structure);

            Context::add('filePart', json_encode($filePart, JSON_UNESCAPED_UNICODE));

            if (!$filePart) continue;

            $filename = $this->getFileName($filePart['name']);

            if (!Str::contains($supplierFilename, $filename)) {
                continue;
            }

            $messageEncrypt = $this->fetchBody($mailId, $filePart);

            Context::add('messageEncrypt', $messageEncrypt);

            if (!$messageEncrypt) continue;

            $message = $this->decodeMessage($messageEncrypt);

            return $this->saveFile($message, $filename, $userId);
        }

        return null;
    }

    public function fetchStructure($mailId)
    {
        return imap_fetchstructure($this->connection, $mailId);
    }

    public function fetchBody($mailId, $filePart)
    {
        return imap_fetchbody($this->connection, $mailId, $filePart['partNumber']);
    }

    public function decodeMessage($messageEncrypt): string
    {
        $message =imap_base64($messageEncrypt);
        if (!$message) $message = base64_decode($messageEncrypt);
        if (!$message) throw new EmailClientException('failed to base64 decode');

        return $message;
    }

    public function getMailBox(string $mailId): ?string
    {
        Context::add('mail', $mailId);

        $header = imap_headerinfo($this->connection, $mailId);

        if (!$header) return null;

        Context::add('header', $header);

        $header = json_decode(json_encode($header), true);

        if (!isset($header['from'][0]['mailbox']) || !isset($header['from'][0]['host'])) {

            throw new EmailClientException('not found header parameters');

        }

        return $header['from'][0]['mailbox'] . '@' . $header['from'][0]['host'];
    }

    public function getFileName($parameter): ?string
    {

        $fileName = $parameter->value;
        $decodedFileName = '';
        $charset = $parameter->charset ?? 'UTF-8';

        $mime_header = imap_mime_header_decode($fileName);

        if (!$mime_header) return null;

        foreach ($mime_header as $decodedPart) {
            $decodedFileName .= mb_convert_encoding($decodedPart->text, 'UTF-8', $charset);
        }

        return $decodedFileName;
    }

    public function saveFile($message, $filename, int $userId): ?string
    {
        $this->storage->put("users/user_{$userId}/prices/{$filename}", $message);

        if (pathinfo($filename, PATHINFO_EXTENSION) === 'zip') {

            $zip = new ZipArchive;
            $res = $zip->open($this->storage->path("users/user_{$userId}/prices/{$filename}"));

            if ($res === TRUE) {

                $bytes_file = $zip->getFromIndex(0);
                $filenameInZip = $zip->getNameIndex(0);

                $filenameInZip = mb_convert_encoding($filenameInZip, 'UTF-8', mb_detect_encoding($filenameInZip));

                $this->storage->put("users/user_{$userId}/prices/{$filenameInZip}", $bytes_file);

                $zip->close();

                $this->storage->delete($this->storage->path("users/user_{$userId}/prices/{$filename}"));

                return "users/user_{$userId}/prices/{$filenameInZip}";

            } else {

                throw new EmailClientException('error to load zip archive');

            }
        }

        return "users/user_{$userId}/prices/{$filename}";
    }

    public function connect(string $address, string $password): Connection
    {
        $imap = imap_open("{imap.mail.ru:993/imap/ssl}INBOX", $address, $password);

        if (!$imap) throw new EmailClientException('failed to connection');

        return $imap;
    }

    public function findFilePart($part, $partNumber = 1): ?array
    {
        if ($part->type == 3 && $part->encoding == 3) {
            return [
                'partNumber' => $partNumber,
                'name' => $this->getFileNamePart($part),
            ];
        } elseif ($part->type == 1) {
            if (isset($part->parts) && is_array($part->parts)) {
                foreach ($part->parts as $subPartNumber => $subPart) {
                    $result = $this->findFilePart($subPart, $partNumber++);
                    if ($result !== false) {
                        return $result;
                    }
                }
            }
        }

        return null;
    }

    public function getFileNamePart($part)
    {
        if (isset($part->dparameters) && is_array($part->dparameters)) {
            foreach ($part->dparameters as $param) {
                if ($param->attribute == 'filename') {
                    return $param;
                }
            }
        }

        return null;
    }

//    public static function check_supplier(Collection $emails, string $mailbox, string $filename): array
//    {
//
//        $report = array();
//
//        foreach ($emails as $email) {
//            [$status, $imap] = EmailService::connect($email->address, $email->password);
//            if (!$status) {
//                $report[$email->address] = [
//                    'message' => "Не удалось открыть почту",
//                    'status' => false,
//                ];
//                continue;
//            }
//            $report[$email->address] = [
//                'message' => "Почта успешно открыта",
//                'status' => true
//            ];
//
//            $mails = imap_search($imap, "UNSEEN");
//
//            if (gettype($mails) != 'array') {
//                $report[$email->address]['mails'] = [
//                    'message' => "Почта пуста",
//                    'status' => false,
//                ];
//                continue;
//            }
//            $report[$email->address]['mails'] = [
//                'message' => "Найдены непрочитанные письма",
//                'status' => true
//            ];
//
//            foreach ($mails as $key => $mail) {
//
//                [$isThisSupplier, $original_mailbox] = EmailService::searchSupplier($imap, $mail, $mailbox);
//
//                $report[$email->address]['mails'][$key]['original'] = $original_mailbox;
//                $report[$email->address]['mails'][$key]['mailbox'] = $mailbox;
//
//                if (!$isThisSupplier) {
//                    $report[$email->address]['mails'][$key]['message'] = "Неподходящий email отправителя";
//                    $report[$email->address]['mails'][$key]['status'] = false;
//                    continue;
//                }
//                $report[$email->address]['mails'][$key]['message'] = "Email отправителя совпал!";
//                $report[$email->address]['mails'][$key]['status'] = true;
//
//                $structure = imap_fetchstructure($imap, $mail);
//                $findFile = null;
//                $original_name = null;
//
//                // Поиск файла в структуре письма
//                $filePart = EmailService::findFilePart($structure);
//
//                // Если файл найден
//                if ($filePart !== false) {
//
//                    [$findFile, $original_name] = EmailService::searchFile($filePart['name'], $filename);
//
//                    $report[$email->address]['mails'][$key]['file']['original_name'] = $original_name;
//                    $report[$email->address]['mails'][$key]['file']['name'] = $filename;
//
//                    if (!$findFile) {
//                        $report[$email->address]['mails'][$key]['file']['message'] = "Файл с таким именем не найден!";
//                        $report[$email->address]['mails'][$key]['file']['status'] = false;
//                        continue;
//                    }
//                    $report[$email->address]['mails'][$key]['file']['message'] = "Имя файла совпало!";
//                    $report[$email->address]['mails'][$key]['file']['status'] = true;
//
//                    $message_encrypt = imap_fetchbody($imap, $mail, $filePart['partNumber']);
//
//                    $message = imap_base64($message_encrypt);
//                    if (!$message) $message = base64_decode($message_encrypt);
//
//                    if (!$message) {
//                        $report[$email->address]['mails'][$key]['body']['message'] = "Не удалось распарсировать файл!";
//                        $report[$email->address]['mails'][$key]['body']['status'] = false;
//                        continue;
//                    }
//                    $report[$email->address]['mails'][$key]['body']['message'] = "Файл успешно распарсирован. Все проверки пройдены!";
//                    $report[$email->address]['mails'][$key]['body']['status'] = true;
//
//                    return [true, $report];
//
//                } else {
//                    $report[$email->address]['mails'][$key]['file']['message'] = 'Не найден файл в теле письма';
//                    $report[$email->address]['mails'][$key]['file']['status'] = false;
//                }
//            }
//        }
//
//        return [false, $report];
//    }
}
