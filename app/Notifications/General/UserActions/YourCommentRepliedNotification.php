<?php

namespace App\Notifications\General\UserActions;

use App\Enums\Database\Tables\CommentsTableEnum;
use App\Enums\UserActions\CommentableTypesEnum;
use App\HHH_Library\general\php\LogCreator;
use App\Models\General\Notification;
use App\Models\Site\UserActions\Comment;
use App\Notifications\SuperClasses\SuperDatabaseNotification;
use Illuminate\Bus\Queueable;


class YourCommentRepliedNotification extends SuperDatabaseNotification
{
    use Queueable;

    private $replyId;

    /**
     * Create a new notification instance.
     *
     *
     * @param int $replyId
     * @return void
     */
    public function __construct(int $replyId)
    {
        $this->replyId = $replyId;
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
            'replyId'   => $this->replyId,
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
        return config('hhh_config.notification.categories.info.iconBgClass');
    }

    /**
     * This function returns the icon "CSS Class" name.
     *
     * @return string ::Sample: "fa fa-id-card" or config('hhh_config.fontIcons.employment')
     */
    public static function getIconViewClass(): string
    {
        return config('hhh_config.fontIcons.menu.Comments');
    }

    /**
     * This function returns the subject of notification.
     * May be you need to return translated subject.
     *
     * @return string ::Sample: "User workgroup changed!"
     */
    public static function getSubject(): string
    {
        return trans('notifications.YourCommentReplied.subject');
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

                if ($reply = Comment::find($notificationData['replyId'])) {

                    if ($reply[CommentsTableEnum::CommentableType->dbName()] !== CommentableTypesEnum::Comment->name) {
                        // Maybe commnet move to another post by admin and the commentable type changed to post
                        $notification->delete();
                        return __('thisApp.Errors.Comments.CommentDeleted');
                    }

                    $message = trans(
                        'notifications.YourCommentReplied.message',
                        self::addPadToArrayVal([
                            'replyOwnerDisplayName' => $reply->OwnerDispalyName,
                        ])
                    );

                    return sprintf(
                        "%s <a href='%s' target='_blank'>%s</a>",
                        $message,
                        $reply->DisplayUrl,
                        __('general.buttons.View'),
                    );
                } else {
                    $notification->delete();
                    return __('thisApp.Errors.Comments.CommentDeleted');
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
