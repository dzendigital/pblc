<?php

namespace App\Listeners\Form;

use App\Events\Form\CalculatorEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Mail;
use App\Mail\Form\CalculatorForm;

class CalculatorListener
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
     * @param  ContactusEvent  $event
     * @return void
     */
    public function handle(CalculatorEvent $event)
    {
        $data = $event->data;

        # emailto
        $data['emailto'] = isset($data['emailto']) ? $data['emailto'] : config('app.email');
        Mail::to($data['emailto'])->send(new CalculatorForm($data));
    }
}
