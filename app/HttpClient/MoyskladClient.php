<?php

namespace App\HttpClient;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

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
            ])->baseUrl('https://api.moysklad.ru/api/remap/1.2');
    }

    public function getWarehouses(): Collection
    {
        while (RateLimiter::attempts('moysklad_api') >= 45) {
            sleep(2);
        }

        return RateLimiter::attempt(
            'moysklad_api',
            45,
            fn() => $this->request->get('/entity/store')->throw()->collect('rows'),
            3
        );
    }

    public function getWarehouseStocks(string $uuid): Collection
    {
        while (RateLimiter::attempts('moysklad_api') >= 45) {
            sleep(2);
        }

        return RateLimiter::attempt(
            'moysklad_api',
            45,
            fn() => $this->request->withQueryParameters([
                'filter' => 'store=https://api.moysklad.ru/api/remap/1.2/entity/store/' . $uuid
            ])->get('/report/stock/all')->throw()->collect(),
            3
        );
    }

    public function getProductAttributes(): Collection
    {
        while (RateLimiter::attempts('moysklad_api') >= 45) {
            sleep(2);
        }

        return RateLimiter::attempt(
            'moysklad_api',
            45,
            fn() => $this->request->get('/entity/product/metadata/attributes')->throw()->collect('rows'),
            3
        );
    }

    public function getSuppliers(): Collection
    {
        while (RateLimiter::attempts('moysklad_api') >= 45) {
            sleep(2);
        }

        return RateLimiter::attempt(
            'moysklad_api',
            45,
            fn() => $this->request->get('/entity/counterparty')->throw()->collect('rows'),
            3
        );
    }

    public function getAssortment(int $offset, int $limit): Collection
    {
        while (RateLimiter::attempts('moysklad_api') >= 45) {
            sleep(2);
        }

        return RateLimiter::attempt(
            'moysklad_api',
            45,
            fn() => $this->request->get("/entity/assortment?limit=$limit&offset=$offset")->throw()->collect(),
            3
        );
    }
}
