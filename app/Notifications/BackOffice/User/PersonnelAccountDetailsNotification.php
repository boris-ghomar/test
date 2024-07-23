<?php

namespace App\Notifications\BackOffice\User;

use App\Enums\Routes\AdminPublicRoutesEnum;
use App\Notifications\SuperClasses\SuperMailNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;


class PersonnelAccountDetailsNotification extends SuperMailNotification
{
    use Queueable;


    /**
     * Create a new notification instance.
     *
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  object  $notifiable
     * @return array
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }


    /**
     * Get the mail representation of the notification.
     *
     * @param  object  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {

        $url = AdminPublicRoutesEnum::ForgotPassword->url();
        $notifiableExtra = $notifiable->personnel->personnelExtra;

        return (new MailMessage)
            ->from(env('MAIL_FROM_ADDRESS'))
            ->subject('Created account notification')
            ->greeting('Congratulations! Your account is ready!')

            ->line(sprintf("Hello dear %s %s,", $notifiableExtra->first_name, $notifiableExtra->last_name))
            ->line(sprintf("You have received this email, because with your email an account has been created on the '%s' site and is ready to use.", env('APP_NAME')))
            ->line("The password was not sent to you due to security issues, so click the button below to enter the \"Forgot Password\" page and request a password reset to reset your password.")
            ->line(sprintf("Your email: %s", $notifiable->email))
            ->line(sprintf("Your username: %s", $notifiable->username))

            ->action('FORGOT PASSWORD', $url)

            ->line('If this account was not created at your request, please ignore this email and do nothing.')
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  object  $notifiable
     * @return array
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }


    /******************************* HHH ********************************/
    //
    /******************************* HHH END ********************************/
}
