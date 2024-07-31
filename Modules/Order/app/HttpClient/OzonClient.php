<?php

namespace Modules\Order\HttpClient;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

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

    public function getNewOrders(int $limit, int $offset = 0): Collection
    {
        $data = array(
            "dir" => "ASC",
            "filter" => array(
                "cutoff_from" => '2024-06-27' . 'T00:00:00Z',
                "cutoff_to" => '3000-01-01' . 'T23:59:59Z',
                "status" => "awaiting_packaging",
                "warehouse_id" => array()
            ),
            "limit" => $limit,
            "offset" => $offset,
        );

        return $this->request->post('/v3/posting/fbs/unfulfilled/list', $data)->throw()->collect('result');
    }

    public function setState(array $product, string $postingNumber)
    {
        $data = array(
            "packages" => array(
                array(
                    "products" => array($product)
                )
            ),
            "posting_number" => $postingNumber
        );

        return $this->request->post('/v4/posting/fbs/ship', $data)->throw()->collect('result');
    }
}
