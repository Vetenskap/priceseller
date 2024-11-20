<?php

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Order\Models\OrderOzonWebhook;

class OrderOzonWebhookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, OrderOzonWebhook $webhook)
    {
        Log::info('Ozon webhook received', [
            'request' => $request->all(),
            'webhook' => $webhook->toArray(),
            'market' => $webhook->market->toArray()
        ]);

        return response()->json(['version' => '2.0.0', 'name' => 'Priceseller', 'time' => now()->toRfc3339String()]);
    }
}
