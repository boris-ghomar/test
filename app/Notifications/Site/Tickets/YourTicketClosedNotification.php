<?php

namespace App\Notifications\Site\Tickets;

use App\Enums\Routes\SitePublicRoutesEnum;
use App\HHH_Library\general\php\LogCreator;
use App\Models\BackOffice\Tickets\Ticket;
use App\Models\General\Notification;
use App\Notifications\SuperClasses\SuperDatabaseNotification;
use Illuminate\Bus\Queueable;

class YourTicketClosedNotification extends SuperDatabaseNotification
{
    use Queueable;

    private $ticketId;

    /**
     * Create a new notification instance.
     *
     *
     * @param int $replyId
     * @return void
     */
    public function __construct(int $ticketId)
    {
        $this->ticketId = $ticketId;
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

    /**
     * Get the array representation of the notification.
     *
     * @param  object  $notifiable
     * @return array
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'ticketId'          => $this->ticketId,
        ];
    }

    /******************************* HHH ********************************/

    /******************** Implements ********************/

    /**
     * This function returns the background
     * "CSS Class" color  of the icon for display.
     *
     * @return string ::Sample: "bg-warning" | "bg-danger" | "bg-info"
     */
    public static function getIconBgClass(): string
    {
        return config('hhh_config.notification.categories.success.iconBgClass');
    }

    /**
     * This function returns the icon "CSS Class" name.
     *
     * @return string ::Sample: "fa fa-id-card" or config('hhh_config.fontIcons.employment')
     */
    public static function getIconViewClass(): string
    {
        return config('hhh_config.fontIcons.menu.Tickets');
    }

    /**
     * This function returns the subject of notification.
     * May be you need to return translated subject.
     *
     * @return string ::Sample: "User workgroup changed!"
     */
    public static function getSubject(): string
    {
        return trans('notifications.YourTicketClosed.subject');
    }

    /**
     * This function returns the message of notification.
     * May be you need to return translated subject.
     *
     * @param string $notificationId
     * @return ?string ::Sample: "!!User workgroup has been changed!!"
     */
    public static function getMessage(string $notificationId): ?string
    {

        try {
            if ($notification = Notification::find($notificationId)) {


                $notificationData = $notification->data;
                $ticketId = $notificationData['ticketId'];
                if ($ticket = Ticket::find($ticketId)) {

                    $message = trans(
                        'notifications.YourTicketClosed.message',
                        [
                            'ticketId'        => $ticketId,
                        ]
                    );

                    return sprintf(
                        "%s <a href='%s' target='_blank'>%s</a>",
                        $message,
                        SitePublicRoutesEnum::Tickets_TicketShow->route(['myTicket' => $ticketId]),
                        __('general.buttons.View'),
                    );
                } else {

                    $notification->delete();
                    return __('thisApp.Errors.Tickets.TicketDeleted');
                }
            }
        } catch (\Throwable $th) {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                $th->getMessage(),
                'Notification dispatching error'
            );
        }

        return null;
    }
    /******************** Implements ********************/


    /******************************* HHH END ********************************/
}
