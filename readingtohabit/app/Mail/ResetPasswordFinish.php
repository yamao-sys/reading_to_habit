<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordFinish extends Mailable
{
    use Queueable, SerializesModels;

    protected $name;
    protected $url_login;
    protected $url_contact;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->url_login   = \DocumentRootConst::DOCUMENT_ROOT.'login';
        $this->url_contact = \DocumentRootConst::DOCUMENT_ROOT.'contact_form';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.reset_password_finish')
                    ->subject('Readingtohabit：パスワードリセット完了')
                    ->with([
                        'name' => $this->name,
                        'url_login'  => $this->url_login,
                        'url_contact' => $this->url_contact,
                      ]);
    }
}
