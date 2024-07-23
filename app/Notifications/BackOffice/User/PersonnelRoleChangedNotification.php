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


class PersonnelRoleChangedNotification extends SuperDatabaseNotification
{
    use Queueable;

    private $operatorPersonnelId;
    private $affectedPersonnelId;
    private $oldRoleId;
    private $newRoleId;


    /**
     * Create a new notification instance.
     *
     * @param int $affectedPersonnelId
     * @param int $oldRoleId
     * @param int $newRoleId
     *
     * @return void
     */
    public function __construct($affectedPersonnelId, $oldRoleId, $newRoleId)
    {
        $this->operatorPersonnelId = auth()->user()->id;
        $this->affectedPersonnelId = $affectedPersonnelId;
        $this->oldRoleId = $oldRoleId;
        $this->newRoleId = $newRoleId;
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
            'operatorPersonnelId'   => $this->operatorPersonnelId,
            'affectedPersonnelId'   => $this->affectedPersonnelId,
            'oldRoleId'             => $this->oldRoleId,
            'newRoleId'             => $this->newRoleId,
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
        return trans('notifications.PersonnelRoleChanged.subject');
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
                    'notifications.PersonnelRoleChanged.message',
                    self::addPadToArrayVal([
                        'operator' => parent::findItem(User::class, $notificationData['operatorPersonnelId'], usersTableEnum::Username->dbName()),
                        'username' => parent::findItem(User::class, $notificationData['affectedPersonnelId'], usersTableEnum::Username->dbName()),
                        'oldRole'  => parent::findItem(Role::class, $notificationData['oldRoleId'], RolesTableEnum::Name->dbName()),
                        'newRole'  => parent::findItem(Role::class, $notificationData['newRoleId'], RolesTableEnum::Name->dbName()),
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
