<?php

namespace App\Http\Middleware\Specialist;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use App\Models\Specialist\Item as Specialist;

class ProfileMiddleware extends Middleware
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
        $userdata = auth()->user();
        $email = auth()->user()->email;
        $specialist = Specialist::where([["email", "=", $email]])->first();
        if ( is_null($specialist) ) 
        {
            # аккаунт специалиста не создан
            return redirect("/specialist/profile/create");
        }
     
        return $next($request);
    }
}
