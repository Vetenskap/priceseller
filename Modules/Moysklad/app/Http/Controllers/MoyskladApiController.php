<?php

namespace Modules\Moysklad\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ModuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Moysklad\Jobs\MoyskladWebhookProcess;
use Modules\Moysklad\Models\MoyskladWebhook;

class MoyskladApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(MoyskladWebhook $webhook, Request $request)
    {
        Log::info('Moysklad webhook received', [
            'webhook' => $webhook->toArray(),
            'request' => $request->all(),
        ]);

        if (ModuleService::moduleIsEnabled('Moysklad', $webhook->moysklad->user)) {
            MoyskladWebhookProcess::dispatch($request->collect(), $webhook);
        }

        return \response()->json();
    }
}
