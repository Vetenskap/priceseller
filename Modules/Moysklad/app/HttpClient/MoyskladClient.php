<?php

namespace Modules\Moysklad\HttpClient;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class MoyskladClient
{
    const BASEURL = 'https://api.moysklad.ru/api/remap/1.2';
    public PendingRequest $request;

    public function __construct(string $apiKey)
    {
        $this->request = Http::retry(3, 5000, function (\Exception $exception, PendingRequest $request) {
            return ($exception instanceof RequestException && $exception->response->tooManyRequests()) || $exception instanceof ConnectionException;
        })
            ->timeout(60)
            ->connectTimeout(60)
            ->withToken($apiKey)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept-Encoding' => 'application/gzip'
            ])->baseUrl(static::BASEURL);
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

}
