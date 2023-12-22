<?php

namespace App\Listeners\Form;

use App\Events\Form\CallbackEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Mail;
use App\Mail\Form\CallbackForm;

class CallbackListener
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
     * @param  CallbackEvent  $event
     * @return void
     */
    public function handle(CallbackEvent $event)
    {
        $data = $event->data;
        // emailto
        $data['emailto'] = isset($data['emailto']) ? $data['emailto'] : config('app.email');
        Mail::to($data['emailto'])->send(new CallbackForm($data));
    }
}
