<?php

namespace App\HttpClient;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class MoyskladClient
{
    public PendingRequest $request;

    public function __construct(string $apiKey)
    {
        $this->request = Http::retry(3, 2000, function (\Exception $exception, PendingRequest $request) {
            return ($exception instanceof RequestException && $exception->response->tooManyRequests()) || $exception instanceof ConnectionException;
        })
            ->timeout(60)
            ->connectTimeout(60)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept-Encoding' => 'application/gzip',
                'Content-Type' => 'application/json'
            ])->baseUrl('https://api-seller.ozon.ru');
    }

    public function getWarehouses(): Collection
    {
        return $this->request->get('https://api.moysklad.ru/api/remap/1.2/entity/store')->throw()->collect('rows');
    }

    public function getWarehouseStocks(string $uuid)
    {
        $stocks = $this->request->withQueryParameters([
            'filter' => 'store=https://api.moysklad.ru/api/remap/1.2/entity/store/' . $uuid
        ])->get('https://api.moysklad.ru/api/remap/1.2/report/stock/all')->throw()->collect();

        return $stocks;
    }
}
