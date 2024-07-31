<?php

namespace Modules\Order\HttpClient;

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

    public function getNewOrders()
    {
        return $this->request->get('/api/v3/orders/new')->throw()->collect('orders');
    }
}
