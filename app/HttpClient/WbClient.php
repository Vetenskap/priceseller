<?php

namespace App\HttpClient;

use App\Models\Supplier;
use App\Services\SupplierReportService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

    public function putStocks(array $data, int $warehouseId, Supplier $supplier): void
    {
        $limits = 5;

        while (RateLimiter::attempts('wb_get_cards_list') >= 300) {
            SupplierReportService::addLog($supplier, 'Превышен лимит запрос, ожидаем 2 сек.');
            sleep(2);
        }

        while ($limits > 0) {

            if (!$data->count() > 0) return;

            $limits--;

            try {

                RateLimiter::attempt(
                    'wb_put_stocks',
                    300,
                    fn() => $this->request->put("/api/v3/stocks/{$warehouseId}", ['stocks' => $data])->throw()
                );

                return;

            } catch (RequestException $e) {
                $response = $e->response;

                // TODO: переделать
                if ($response->status() === 409) {
                    $errors = $response->collect();

                    $errors->each(function (array $error) use (&$data, $e, $supplier) {
                        $error = collect($error);

                        $badItems = collect($error->get('data'));

                        $badItems->each(function (array $badItem) use (&$data, $supplier, $error) {
                            $badItem = collect($badItem);

                            $data = $data->filter(fn (array $item) => $item['sku'] !== $badItem->get('sku'));

                            SupplierReportService::addLog($supplier, "sku: " . $badItem->get('sku') . " - " . $error->get('message'), 'warning');
                        });
                    });

                    continue;
                }

                $data->each(function (array $item) use ($supplier, $response) {
                    SupplierReportService::addLog($supplier, "sku: " . $item['sku'] . " - Статус: " . $response->status() . ", Тело: " . $response->body(), 'warning');
                });

                return;
            }

        }

    }

    public function putPrices(array $data, Supplier $supplier): void
    {

        while (RateLimiter::attempts('wb_get_cards_list') >= 10) {
            SupplierReportService::addLog($supplier, 'Превышен лимит запрос, ожидаем 2 сек.');
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

            $body = $response->collect();

            if (!$body) {
                Log::alert('Обновленеи цен вб: не найдено тело ошибки', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }

            $data->each(function (array $item) use ($supplier, $body) {
                SupplierReportService::addLog($supplier, "nmId: " . $item['nmId'] . " - " . $body->get('errorText'), 'warning');
            });

            return;

        }
    }
}
