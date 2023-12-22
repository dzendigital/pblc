<?php

namespace App\Http\Middleware\Page;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

use App\Models\Menu;

class PageMiddleware extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $item = Menu::where([
            ["slug", '=', $request->path()],
            ["is_visible", '=', 1],
        ])->firstOrFail();
     
        return $next($request);
    }
}
