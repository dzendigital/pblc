<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Имеет ли текущий Пользователь заданную Роль/Право
     *
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param $role
     * @param null $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role, $permission = null )
    {
        if( is_null($permission) ){
            if(!auth()->user()->hasRole($role)) {
                abort(404);
            }
        }else{
            if($role != 'admin' && !auth()->user()->hasRole($permission)) {
                abort(404);
            }
        }
        return $next($request);
        /*
         *
         * Использование в routes/web.php
         *
         *  Route::group(['middleware' => 'role:web-developer'], function() {
         *      Route::get('/dashboard', function() {
         *          return 'Добро пожаловать, Веб-разработчик';
         *      });
         *  });
         *
         *
         */
    }
}
