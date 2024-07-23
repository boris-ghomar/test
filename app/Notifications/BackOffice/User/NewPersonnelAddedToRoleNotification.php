<?php

namespace App\Notifications\BackOffice\User;

use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\HHH_Library\general\php\LogCreator;
use App\Models\General\Notification;
use App\Models\General\Role;
use App\Models\User;
use App\Notifications\SuperClasses\SuperDatabaseNotification;
use Illuminate\Bus\Queueable;


class NewPersonnelAddedToRoleNotification extends SuperDatabaseNotification
{
    use Queueable;

    private $operatorPersonnelId;
    private $newPersonnelId;
    private $roleId;


    /**
     * Create a new notification instance.
     *
     * @param int $newPersonnelId
     * @param int $roleId
     *
     * @return void
     */
    public function __construct(int $newPersonnelId, int $roleId)
    {
        $this->operatorPersonnelId = auth()->user()->id;
        $this->newPersonnelId = $newPersonnelId;
        $this->roleId = $roleId;
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
     * @param  object $notifiable
     * @return array
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'operatorPersonnelId'   => $this->operatorPersonnelId,
            'newPersonnelId'        => $this->newPersonnelId,
            'roleId'                => $this->roleId,
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
        return config('hhh_config.notification.categories.danger.iconBgClass');
    }

    /**
     * This function returns the icon "CSS Class" name.
     *
     * @return string ::Sample: "fa fa-id-card" or config('hhh_config.fontIcons.employment')
     */
    public static function getIconViewClass(): string
    {
        return config('hhh_config.fontIcons.menu.Personnel');
    }

    /**
     * This function returns the subject of notification.
     * May be you need to return translated subject.
     *
     * @return string ::Sample: "User workgroup changed!"
     */
    public static function getSubject(): string
    {
        return trans('notifications.NewPersonnelAddedToRole.subject');
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

                return trans(
                    'notifications.NewPersonnelAddedToRole.message',
                    self::addPadToArrayVal([
                        'operator' => parent::findItem(User::class, $notificationData['operatorPersonnelId'], usersTableEnum::Username->dbName()),
                        'username' => parent::findItem(User::class, $notificationData['newPersonnelId'], usersTableEnum::Username->dbName()),
                        'role'     => parent::findItem(Role::class, $notificationData['roleId'], RolesTableEnum::Name->dbName()),
                    ])
                );
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
