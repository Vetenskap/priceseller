<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Context;
use Symfony\Component\HttpFoundation\Response;

class UserAvitoSubPermission
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && !$request->user()->isAvitoSub()) {
            return redirect()->route('subscribe.avito');
        }

        Context::add('subscribe', 'avito');
        Context::push('user', ['subscribe_avito' => $request->user()->isAvitoSub() ? 'yes' : 'no']);
        Context::push('user', ['user_id' => $request->user()->id]);

        return $next($request);
    }
}
