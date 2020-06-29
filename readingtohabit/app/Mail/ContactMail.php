<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;
    
    protected $created_at;
    protected $email;
    protected $contact;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($created_at, $email, $contact)
    {
        $this->created_at = $created_at;
        $this->email      = $email;
        $this->contact    = $contact;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.contact_mail')
                    ->subject('Readingtohabit:お問い合わせ')
                    ->with([
                        'created_at' => $this->created_at,
                        'email'   => $this->email,
                        'contact' => $this->contact
                    ]);
    }
}
