<?php

namespace App\Listeners\Form;

use App\Events\Form\ContactusEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Mail;
use App\Mail\Form\ContactusForm;

class ContactusListener
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
    public function handle(ContactusEvent $event)
    {
        $data = $event->data;
        // emailto
        $data['emailto'] = isset($data['emailto']) ? $data['emailto'] : config('app.email');
        Mail::to($data['emailto'])->send(new ContactusForm($data));
    }
}
