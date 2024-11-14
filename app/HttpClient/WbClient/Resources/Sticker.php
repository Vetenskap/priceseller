<?php

namespace App\HttpClient\WbClient\Resources;

use App\HttpClient\WbClient\WbClient;
use Illuminate\Support\Collection;
use Livewire\Wireable;

class Sticker implements Wireable
{
    const ENDPOINT = 'https://marketplace-api.wildberries.ru/api/v3/orders/stickers';

    protected int $orderId;
    protected int $partA;
    protected int $partB;
    protected string $barcode;
    protected string $file;

    public function __construct(Collection $sticker = null)
    {
        $this->orderId = $sticker->get('orderId');
        $this->partA = $sticker->get('partA');
        $this->partB = $sticker->get('partB');
        $this->barcode = $sticker->get('barcode');
        $this->file = $sticker->get('file');
    }

    public static function getFromOrderIds(array $orderIds, string $api_key, string $type = 'png'): Collection
    {
        $stickers = collect();

        $chunks = array_chunk($orderIds, 100);

        foreach ($chunks as $chunk) {
            $data = ['orders' => $chunk];

            $client = new WbClient($api_key);
            $response = $client->post(self::ENDPOINT, $data, [
                "type" => $type,
                "width" => 58,
                "height" => 40
            ]);
            $response->collect()->toCollectionSpread()->get('stickers')->each(fn (Collection $sticker) => $stickers->push(new self($sticker)));
        }


        return $stickers;
    }

    public function toLivewire(): array
    {
        return [
            'orderId' => $this->orderId,
            'partA' => $this->partA,
            'partB' => $this->partB,
            'barcode' => $this->barcode,
            'file' => $this->file
        ];
    }

    public static function fromLivewire($value): Sticker
    {
        return new static(collect($value)->toCollectionSpread());
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getPartA(): int
    {
        return $this->partA;
    }

    public function getPartB(): int
    {
        return $this->partB;
    }

    public function getBarcode(): string
    {
        return $this->barcode;
    }

    public function getFile(): string
    {
        return $this->file;
    }

}
