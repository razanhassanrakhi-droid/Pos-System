<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $userName;

    public function __construct($otp, $userName)
    {
        $this->otp = $otp;
        $this->userName = $userName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'رمز التحقق الخاص بك - OTP Code',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
