<?php

namespace Modules\SamsonApi\HttpClient;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

class SamsonClient
{
    public PendingRequest $request;
    public string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->request = Http::retry(3, 2000, function (\Exception $exception, PendingRequest $request) {
            return ($exception instanceof RequestException && $exception->response->tooManyRequests()) || $exception instanceof ConnectionException;
        })
            ->timeout(60)
            ->connectTimeout(60)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])->baseUrl('https://api.samsonopt.ru/v1');

        $this->apiKey = $apiKey;
    }

    public function get(string $endpoint, array $queryParameters): Collection
    {
        while (RateLimiter::attempts('samson_get') >= 60) {
            sleep(2);
        }

        return RateLimiter::attempt(
            'samson_get',
            60,
            fn() => $this->request->withQueryParameters($queryParameters)->get($endpoint)->throw()->collect(),
            10
        );

    }
}
