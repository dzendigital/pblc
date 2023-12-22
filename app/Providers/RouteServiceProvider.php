<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * 
     * Путь к панели управления сайтом
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const ADMIN = "/admin";
    
    /**
     * Путь к личному кабинету
     *
     * @var string
     */
    public const ACCOUNT = "/account";

    /**
     * Путь к личному кабинету менеджера
     *
     * @var string
     */
    public const MANAGER = "/admin";

    /**
     * Путь к личному кабинету специалиста платформы
     *
     * @var string
     */
    public const SPECIALIST = "/specialist";

    /**
     * Путь к личному кабинету пользователя платформы
     *
     * @var string
     */
    public const USER = "/user";

    /**
     * Базовый путь
     *
     * @var string
     */
    public const HOME = "/";

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    # protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
