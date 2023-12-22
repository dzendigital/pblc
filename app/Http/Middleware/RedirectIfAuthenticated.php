<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {

        # используем пути редиректов в личный кабинет
        if ( Auth::user() == null ) {
            return $next($request);
        }
        switch ( Auth::user()->roles()->first()->slug ) {
            case 'admin':
                $path = RouteServiceProvider::ADMIN;
                break;
            case 'account':
                $path = RouteServiceProvider::ACCOUNT;
                break;
            case 'specialist':
                $path = RouteServiceProvider::SPECIALIST;
                break;
            case 'manager':
                $path = RouteServiceProvider::MANAGER;
                break;
            case 'user':
                $path = RouteServiceProvider::USER;
                break;
            default:

                break;
        }
        $guards = empty($guards) ? [null] : $guards;
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect($path);
            }
        }

        return $next($request);
    }
}
