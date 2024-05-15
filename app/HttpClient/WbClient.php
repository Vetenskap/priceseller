<?php

namespace App\HttpClient;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

class WbClient
{

    public PendingRequest $request;

    public function __construct(string $api_key)
    {
        $this->request = Http::retry(3, 2000, function (\Exception $exception, PendingRequest $request) {
            return ($exception instanceof RequestException && $exception->response->tooManyRequests()) || $exception instanceof ConnectionException;
        })
            ->timeout(60)
            ->connectTimeout(60)
            ->withToken($api_key, '')
            ->withHeader('Content-Type', 'application/json')
            ->baseUrl('https://suppliers-api.wildberries.ru');
    }

    public function getCardsList($updatedAt = '', $nmId = 0): Collection
    {
        $data = [
            "settings" => [
                "cursor" => [
                    "limit" => 100,
                ],
                "filter" => [
                    "withPhoto" => -1
                ]
            ]
        ];

        if ($updatedAt && $nmId) {
            $data['settings']['cursor']['nmID'] = $nmId;
            $data['settings']['cursor']['updatedAt'] = $updatedAt;
        }

        $response = RateLimiter::attempt(
            'wb_get_cards_list',
            100,
            fn () => $this->request->post('/content/v2/get/cards/list', $data)->throw()->collect()
        );

        $cards = collect($response->get('cards'));
        $cursor = collect($response->get('cursor'));

        return collect([
            'cards' => $cards,
            'cursor' => $cursor
        ]);
    }
}
