<?php

namespace Modules\Moysklad\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladWebhook;

class MoyskladApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Moysklad $moysklad, MoyskladWebhook $webhook, Request $request)
    {
        logger($request->input());
    }
}
