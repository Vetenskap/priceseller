<?php

namespace App\HttpClient;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class WbExternalClient
{
    public PendingRequest $request;

    public function __construct()
    {
        $this->request = Http::retry(3, 2000, function (\Exception $exception, PendingRequest $request) {
            return ($exception instanceof RequestException && $exception->response->tooManyRequests()) || $exception instanceof ConnectionException;
        })
            ->timeout(60)
            ->connectTimeout(60)
            ->baseUrl('https://card.wb.ru');
    }

    public function getCardDetail(int $nm_id): Collection
    {
        return collect($this->request->get("/cards/detail?nm={$nm_id}")->throw()->collect('data.products')->first(fn (array $product) => $product['id'] === $nm_id));
    }
}
