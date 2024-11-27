<?php

namespace App\HttpClient\OzonClient\Resources\FBS;

use App\HttpClient\OzonClient\OzonClient;
use Illuminate\Support\Collection;
use Livewire\Wireable;

class ActGetBarcode implements Wireable
{
    const ENDPOINT = '/v2/posting/fbs/act/get-barcode';

    protected ?string $content_type = null;
    protected ?string $file_name = null;
    protected ?string $file_content = null;
    protected ?string $text = null;

    public function __construct(Collection $actBarcode)
    {
        $this->content_type = $actBarcode->get('content_type');
        $this->file_name = $actBarcode->get('file_name');
        $this->file_content = $actBarcode->get('file_content');
        $this->text = $actBarcode->get('result') ?? $actBarcode->get('text');
    }

    public static function fetch(string $apiKey, int $clientId, int $carriageId): ActGetBarcode
    {
        $client = new OzonClient($apiKey, $clientId);
        return new self($client->post(static::ENDPOINT, ['id' => $carriageId])->collect()->merge($client->post(static::ENDPOINT . '/text', ['id' => $carriageId])->collect()));
    }

    public function toLivewire(): array
    {
        return [
            'content_type' => $this->content_type,
            'file_name' => $this->file_name,
            'file_content' => $this->file_content,
            'text' => $this->text,
        ];
    }

    public static function fromLivewire($value)
    {
        return new self(collect($value)->toCollectionSpread());
    }

    public function getContentType(): ?string
    {
        return $this->content_type;
    }

    public function getFileName(): ?string
    {
        return $this->file_name;
    }

    public function getFileContent(): ?string
    {
        return base64_encode($this->file_content);
    }

    public function getText(): ?string
    {
        return $this->text;
    }


}
