<?php

namespace App\Notifications\BackOffice\User;

use App\Enums\Routes\AdminPublicRoutesEnum;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends ResetPassword
{
    use Queueable;

    /**
     * Get the reset URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function resetUrl($notifiable)
    {
        return AdminPublicRoutesEnum::ResetPasswordIndex->route([
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], true);
    }
}
