<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DynamicAuthGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  array  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $isAuthenticated = false;

        // Перебираем список guard'ов и проверяем, авторизован ли пользователь в одном из них
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $isAuthenticated = true;
                break;  // Если авторизован, выходим из цикла
            }
        }

        // Если пользователь не авторизован в указанных guard'ах
        if (!$isAuthenticated) {
            return redirect()->route('login');  // Перенаправляем на страницу авторизации
        }

        // Продолжаем выполнение запроса, если авторизован
        return $next($request);
    }
}
