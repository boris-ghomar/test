<?php

namespace App\Mail;

use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Database\Tables\VerificationsTableEnum;
use App\Models\General\Verification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailVerificationMail extends SuperMail
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(protected string $verificationId)
    {
        parent::__construct();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), $this->getAppName()),
            subject: __('email.EmailVerificationNotification.subject', ['appName' => $this->getAppName()]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $verification = Verification::where(VerificationsTableEnum::Id->dbName(), $this->verificationId)->first();

        $recipientUser = $verification->user;

        $greetingName = is_null($recipientUser) ? __('email.unknownUser') : $recipientUser[UsersTableEnum::DisplayName->dbName()];

        return new Content(
            view: 'hhh.Emails.EmailVerificationMail',
            with: [
                'greeting' => __('email.EmailVerificationNotification.greeting', ['greetingName' => $greetingName]),
                'appName' => $this->getAppName(),
                'siteLogo' => $this->getSiteLogoFile(),
                'verificationCode' => $verification[VerificationsTableEnum::Code->dbName()],
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
