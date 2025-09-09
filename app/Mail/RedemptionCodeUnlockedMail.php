<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RedemptionCodeUnlockedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contactEmail;
    public $record;
    public $codes;

    public function __construct($contactEmail, $record, array $codes)
    {
        $this->contactEmail = $contactEmail;
        $this->record = $record;
        $this->codes = $codes;
    }

    public function build()
    {
        return $this->subject('交易武器已解鎖！Redemption Code Unlocked')
            ->view('emails.redemption_code_unlocked')
            ->with([
                'contactEmail' => $this->contactEmail,
                'record' => $this->record,
                'codes' => $this->codes,
            ]);
    }
}
