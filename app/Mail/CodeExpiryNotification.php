<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CodeExpiryNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $contactEmail;
    public $code;

    public function __construct($contactEmail, $code)
    {
        $this->contactEmail = $contactEmail;
        $this->code = $code;
    }

    public function build()
    {
        return $this->subject('Your Redemption Code is Expiring Soon')
        ->view('emails.expiry_notice')
        ->with([
            'expiryDate' => $this->code->expired_date,
            'contactEmail' => $this->contactEmail,
        ]);
    }
}
