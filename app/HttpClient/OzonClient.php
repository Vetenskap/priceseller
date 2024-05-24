<?php

namespace App\HttpClient;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class OzonClient
{
    public PendingRequest $request;

    public function __construct(string $apiKey, int $clientId)
    {
        $this->request = Http::retry(3, 2000, function (\Exception $exception, PendingRequest $request) {
            return ($exception instanceof RequestException && $exception->response->tooManyRequests()) || $exception instanceof ConnectionException;
        })
            ->timeout(60)
            ->connectTimeout(60)
            ->withHeaders([
                'Client-Id' => $clientId,
                'Api-Key' => $apiKey,
                'Content-Type' => 'application/json'
            ])->baseUrl('https://api-seller.ozon.ru');
    }

    public function getProductList(string $lastId = "", array $offerIds = []): Collection
    {
        $data = [

            "filter" => [
                "offer_id" => $offerIds,
                "product_id" => [],
                "visibility" => "ALL"
            ],
            "last_id" => $lastId,
            "limit" => 1000

        ];

        try {
            $result = $this->request->post('/v4/product/info/prices', $data)->throw()->collect('result');
        } catch (RequestException $e) {

            $response = $e->response;

            if ($response->notFound()) {
                if ($data = $response->collect()) {
                    if ($data->get('code') === 5) {
                        return collect(['last_id' => "", 'items' => collect()]);
                    }
                }
            }

            throw $e;
        }

        $items = collect($result->get('items'));
        $lastId = $result->get('last_id');

        return collect(['last_id' => $lastId, 'items' => $items]);

    }

    public function getWarehouses(): Collection
    {
        return $this->request->post('/v1/warehouse/list', ['data' => []])->throw()->collect('result');
    }

    public function putPrices(array $data): Collection
    {
        return $this->request->post('/v1/product/import/prices', ['prices' => $data])->collect('result');
    }

    public function putStocks(array $data)
    {
        while (RateLimiter::attempts('ozon_put_stocks') >= 80) {
            sleep(2);
        }

        return RateLimiter::attempt(
            'ozon_put_stocks',
            80,
            fn() => $this->request->post('/v2/products/stocks', ['stocks' => $data])->collect('result')
        );

    }
}
