<?php

namespace App\Mail\Form;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactusForm extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * The default data.
     *
     * @var data
     */
    public $data;

    public $setting;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->setting = array(
            'from' => config('app.no_reply_email'),
            'subject' => "Заполнена форма обратной связи " . config('app.name'),
            'markdown' => 'emails.client.form.contactus.index',
        );
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->setting['from']) 
        			->subject($this->setting['subject'])  
                    ->markdown($this->setting['markdown']);
    }
}
