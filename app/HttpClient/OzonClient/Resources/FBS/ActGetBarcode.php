<?php

namespace App\HttpClient\OzonClient\Resources\FBS;

use App\HttpClient\OzonClient\OzonClient;
use Illuminate\Support\Collection;
use Livewire\Wireable;

class ActGetBarcode implements Wireable
{
    const ENDPOINT = '/v2/posting/fbs/act/get-barcode';
    protected ?string $file_content = null;
    protected ?string $text = null;

    public function __construct(Collection $actBarcode)
    {
        $this->file_content = $actBarcode->get('file_content');
        $this->text = $actBarcode->get('text');
    }

    public static function fetch(string $apiKey, int $clientId, int $carriageId): ActGetBarcode
    {
        $client = new OzonClient($apiKey, $clientId);
        $barcode = collect();
        $barcode = $barcode->put('file_content', $client->post(static::ENDPOINT, ['id' => $carriageId])->body());
        $barcode = $barcode->put('text', $client->post(static::ENDPOINT . '/text', ['id' => $carriageId])->collect('result'));
        return new self($barcode);
    }

    public function toLivewire(): array
    {
        return [
            'file_content' => $this->file_content,
            'text' => $this->text,
        ];
    }

    public static function fromLivewire($value)
    {
        return new self(collect($value)->toCollectionSpread());
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
