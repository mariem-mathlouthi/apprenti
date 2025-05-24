<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $resetUrl;

    public function __construct($code, $email)
    {
        $this->code = $code;
        $this->resetUrl = config('app.frontend_url').'/change-password?'.http_build_query([
            'email' => rawurlencode($email),
            'code' => $code
        ]);
    }

    public function build()
    {
        return $this->subject('Réinitialisation de mot de passe - '.config('app.name'))
                    ->view('emails.reset-password');
    }
}