<?php

namespace Modules\Moysklad\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ModuleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Moysklad\Jobs\MoyskladWebhookProcess;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladWebhook;

class MoyskladApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Moysklad $moysklad, MoyskladWebhook $webhook, Request $request)
    {
        if (ModuleService::moduleIsEnabled('Moysklad', $webhook->moysklad->user)) {
            MoyskladWebhookProcess::dispatch($request->collect(), $webhook);
        }

        return \response()->json();
    }
}
