<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'App\Events\Form\ContactusEvent' => [
            'App\Listeners\Form\BitrixListener',
            'App\Listeners\Form\ContactusListener',
        ],
        'App\Events\Form\CallbackEvent' => [
            'App\Listeners\Form\BitrixListener',
            'App\Listeners\Form\CallbackListener',
        ],
        'App\Events\Form\ServiceEvent' => [
            'App\Listeners\Form\ServiceListener',
        ],
        'App\Events\Form\CalculatorEvent' => [
            'App\Listeners\Form\BitrixListener',
            'App\Listeners\Form\CalculatorListener',
        ],
        'App\Events\Notification\IndexEvent' => [
            'App\Listeners\Notification\IndexListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
