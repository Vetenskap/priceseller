<?php

namespace Modules\BergApi\HttpClient;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class BergApiClient
{
    const BASEURL = 'https://api.berg.ru';
    public PendingRequest $request;

    public function __construct()
    {
        $this->request = Http::retry(3, 2000, function (\Exception $exception, PendingRequest $request) {
            return ($exception instanceof RequestException && $exception->response->tooManyRequests()) || $exception instanceof ConnectionException;
        })
            ->timeout(60)
            ->connectTimeout(60)
            ->baseUrl(static::BASEURL);
    }

    public function get(string $endpoint, array $queryParameters = []): Collection
    {
        return $this->request->withQueryParameters($queryParameters)->get($endpoint)->throw()->collect();
    }
}
