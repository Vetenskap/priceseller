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

        while (RateLimiter::attempts('wb_get_cards_list') >= 100) {
            sleep(2);
        }

        $response = RateLimiter::attempt(
            'wb_get_cards_list',
            100,
            fn() => $this->request->post('/content/v2/get/cards/list', $data)->throw()->collect()
        );

        $cards = collect($response->get('cards'));
        $cursor = collect($response->get('cursor'));

        return collect([
            'cards' => $cards,
            'cursor' => $cursor
        ]);
    }

    public function getWarehouses()
    {
        return $this->request->get('/api/v3/warehouses')->throw()->collect();
    }

    public function putStocks(Collection $data, int $warehouseId): void
    {
        $limits = 5;

        while (RateLimiter::attempts('wb_get_cards_list') >= 300) {
            sleep(2);
        }

        while ($limits > 0) {

            $limits--;

            try {

                RateLimiter::attempt(
                    'wb_put_stocks',
                    300,
                    fn() => $this->request->put("/api/v3/stocks/{$warehouseId}", ['stocks' => $data->values()->all()])->throw()
                );

                return;

            } catch (RequestException $e) {
                $response = $e->response;

                // TODO: переделать
                if ($response->status() === 409) {
                    $errors = $response->collect();

                    $errors->each(function (array $error) use (&$data, $e) {
                        $error = collect($error);

                        $badItems = collect($error->get('data'));

                        $badItems->each(function (array $badItem) use (&$data) {
                            $badItem = collect($badItem);

                            $data = $data->filter(fn (array $item) => $item['sku'] !== $badItem->get('sku'));
                        });
                    });

                    continue;
                }

                throw $e;
            }

        }

    }

    public function putPrices(array $data): void
    {

        while (RateLimiter::attempts('wb_get_cards_list') >= 10) {
            sleep(2);
        }


        try {
            RateLimiter::attempt(
                'wb_put_prices',
                10,
                fn() => $this->request->baseUrl('https://discounts-prices-api.wb.ru')->post("/api/v2/upload/task", ['data' => $data])->throw(),
                6
            );

            return;
        } catch (RequestException $e) {
            $response = $e->response;

            $data = $response->collect();

            if ($data->get('errorText') === 'No goods for process') return;

            throw $e;
        }
    }
}
