<?php

namespace App\Http\Middleware\User;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use App\Models\User\Item as PlatformUser;

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
        $user = PlatformUser::where([["email", "=", $email]])->first();

        if ( is_null($user) ) 
        {
            # аккаунт специалиста не создан
            return redirect("/user/profile/create");
        }
     
        return $next($request);
    }
}
