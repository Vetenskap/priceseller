<?php

namespace Modules\VoshodApi\HttpClient;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

class VoshodClient
{
    public PendingRequest $request;
    public string $proxyIp;

    public function __construct(string $apiKey, string $loginProxy, string $passwordProxy, string $proxyIp, string $proxyPort)
    {
        $this->request = Http::retry(3, 2000, function (\Exception $exception, PendingRequest $request) {
            return ($exception instanceof RequestException && $exception->response->tooManyRequests()) || $exception instanceof ConnectionException;
        })
            ->timeout(60)
            ->connectTimeout(60)
            ->withOptions([
                'proxy' => "http://$loginProxy:$passwordProxy@$proxyIp:$proxyPort",
            ])
            ->withHeaders([
                'Content-Type' => 'application/json',
                'X-Voshod-API-KEY' => $apiKey
            ])->baseUrl('https://api.v-avto.ru/v1');

        $this->proxyIp = $proxyIp;
    }

    public function get($endpoint, $queryParameters): Collection
    {
        while (RateLimiter::attempts('voshod_get_' . $this->proxyIp) >= 5) {
            sleep(2);
        }

        return RateLimiter::attempt(
            'voshod_get_' . $this->proxyIp,
            5,
            fn() => $this->request->withQueryParameters($queryParameters)->get($endpoint)->throw()->collect()->toCollectionSpread(),
            1
        );

    }
}
