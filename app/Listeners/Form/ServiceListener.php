<?php

namespace App\Listeners\Form;

use App\Events\Form\ServiceEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Mail;
use App\Mail\Form\ServiceForm;

class ServiceListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ServiceEvent  $event
     * @return void
     */
    public function handle(ServiceEvent $event)
    {
        $data = $event->data;
        // emailto
        $data['emailto'] = isset($data['emailto']) ? $data['emailto'] : config('app.email');
        Mail::to($data['emailto'])->send(new ServiceForm($data));
    }
}
