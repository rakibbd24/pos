<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OnSaleMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $attatchement_name = rand(111111,999999);
        $attchment_name =  $attatchement_name.".pdf";
        return $this->subject($this->data['subject'])
        ->markdown('emails.on-sale')
        ->attach($this->data['file'], [
            'as' => $attchment_name, // Change the filename as needed
        ])
        ->with('data', $this->data);
    }
}
