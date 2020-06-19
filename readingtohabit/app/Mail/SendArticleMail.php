<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendArticleMail extends Mailable
{
    use Queueable, SerializesModels;
    
    protected $name;
    protected $bookname;
    protected $learning;
    protected $action;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $bookname, $learning, $action)
    {
        $this->name     = $name;
        $this->bookname = $bookname;
        $this->learning = $learning;
        $this->action   = $action;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.send_article_mail')
                    ->subject('Readingtohabit:リマインドメール')
                    ->with([
                        'name'     => $this->name,
                        'bookname' => $this->bookname,
                        'learning' => $this->learning,
                        'action'   => $this->action,
                    ]);
    }
}
