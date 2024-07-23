<?php

namespace App\Notifications\General\UserActions;

use App\Enums\Database\Tables\PostsTableEnum;
use App\HHH_Library\general\php\LogCreator;
use App\Models\General\Notification;
use App\Models\Site\UserActions\Comment;
use App\Notifications\SuperClasses\SuperDatabaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Str;

class YourCommentPublishedNotification extends SuperDatabaseNotification
{
    use Queueable;

    private $commentId;

    /**
     * Create a new notification instance.
     *
     *
     * @param int $replyId
     * @return void
     */
    public function __construct(int $commentId)
    {
        $this->commentId = $commentId;
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
            'commentId'   => $this->commentId,
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
        return trans('notifications.YourCommentPublished.subject');
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

                if ($comment = Comment::find($notificationData['commentId'])) {

                    $post = $comment->post;

                    $message = trans(
                        'notifications.YourCommentPublished.message',
                        self::addPadToArrayVal([
                            'postTitle' => Str::words($post[PostsTableEnum::Title->dbName()], 10),
                        ])
                    );

                    return sprintf(
                        "%s <a href='%s' target='_blank'>%s</a>",
                        $message,
                        $comment->DisplayUrl,
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
