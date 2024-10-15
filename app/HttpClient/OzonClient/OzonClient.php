<?php

namespace App\HttpClient\OzonClient;

use App\Models\Supplier;
use App\Services\SupplierReportService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

class OzonClient
{
    const BASEURL = 'https://api-seller.ozon.ru';
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
            ])->baseUrl(self::BASEURL);
    }

    public function get(string $endpoint, array $queryParameters = []): Collection
    {
        return $this->request->withQueryParameters($queryParameters)->get($endpoint)->throw()->collect();
    }

    public function delete(string $endpoint): bool
    {
        return $this->request->delete($endpoint)->throw()->successful();
    }

    public function post(string $endpoint, array $data): Collection
    {
        return $this->request->post($endpoint, $data)->throw()->collect();
    }

    public function put(string $endpoint, array $data): bool
    {
        return $this->request->put($endpoint, $data)->throw()->successful();
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

    public function putStocks(array $data, Supplier $supplier)
    {
        while (RateLimiter::attempts('ozon_put_stocks') >= 80) {
            SupplierReportService::addLog($supplier, 'Превышен лимит запросов, ожидаем 2 сек.');
            sleep(2);
        }

        return RateLimiter::attempt(
            'ozon_put_stocks',
            80,
            fn() => $this->request->post('/v2/products/stocks', ['stocks' => $data])->collect('result')
        );

    }
}
