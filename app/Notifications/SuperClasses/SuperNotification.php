<?php

namespace App\Notifications\SuperClasses;

use App\HHH_Library\general\php\traits\AddAttributesPad;
use App\HHH_Library\general\php\traits\TranslateDatabaseField;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

abstract class SuperNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use AddAttributesPad;
    use TranslateDatabaseField;

    /**
     * Create a new notification instance.
     *
     *
     * @return void
     */
    public function __construct()
    {
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  object $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  object $notifiable
     * @return array
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            //
        ];
    }

    /******************************* HHH ********************************/

    /******************** Implements ********************/
    //
    /******************** Implements ********************/


    /******************************* HHH END ********************************/
}
