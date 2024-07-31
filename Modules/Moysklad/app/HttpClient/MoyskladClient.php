<?php

namespace Modules\Moysklad\HttpClient;

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
            ->withToken($apiKey)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept-Encoding' => 'application/gzip'
            ])->baseUrl('https://api.moysklad.ru/api/remap/1.2');
    }

    public function getAllWarehouses(): Collection
    {
        return $this->request->get('/entity/store')->throw()->collect('rows');
    }

    public function getAllStocksFromWarehouse(string $warehouseId): Collection
    {
        return $this->request->withQueryParameters(['filter' => "storeId=$warehouseId"])->get('/report/stock/all/current')->throw()->collect();
    }
}
