<?php

namespace App\Notifications\SuperClasses;

use App\HHH_Library\general\php\LogCreator;
use Illuminate\Support\Str;

abstract class SuperDatabaseNotification extends SuperNotification
{

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
        return ['database'];
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
            //
        ];
    }

    /******************************* HHH ********************************/

    /******************** Implements ********************/

    /**
     * This functions used for showing notification not storing in database.
     * You can define this functions, and when you want to show notification,
     * you can use these items.
     */


    /**
     * This function returns the background
     * "CSS Class" color  of the icon for display.
     *
     * @return string ::Sample: "bg-warning" | "bg-danger" | "bg-info"
     */
    abstract public static function getIconBgClass(): string;

    /**
     * This function returns the icon "CSS Class" name.
     *
     * @return string ::Sample: "fa fa-id-card" or config('hhh_config.fontIcons.employment')
     */
    abstract public static function getIconViewClass(): string;

    /**
     * This function returns the subject of notification.
     * May be you need to return translated subject.
     *
     * @return string ::Sample: "User workgroup changed!"
     */
    abstract public static function getSubject(): string;

    /**
     * This function returns the message of notification.
     * May be you need to return translated subject.
     *
     * @param string $notificationId
     * @return ?string ::Sample: "!!User workgroup has been changed!!"
     */
    abstract public static function getMessage(string $notificationId): ?string;

    /******************** Implements ********************/


    /**
     * Find required item from database model
     *
     * @param  string $modelClass
     * @param  string|int $id : Item Id in database
     * @param  string $ColName : (optinal) Column name in database, if is null => returns $item
     * @return mixed
     */
    protected static function findItem(string $modelClass, string|int $id, string $colName = null): mixed
    {

        if (Str::of($modelClass)->trim()->isEmpty())
            return null;

        $traits = class_uses_recursive($modelClass);
        $usedSoftDelete = in_array('Illuminate\Database\Eloquent\SoftDeletes', $traits);

        try {

            $item = $usedSoftDelete ? $modelClass::withTrashed()->find($id) : $modelClass::find($id);

            if (is_null($item))
                return null;

            return is_null($colName) ? $item : $item[$colName];
        } catch (\Throwable $th) {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                $th->getMessage(),
                'Super databes notification find item error'
            );
        }
        return null;
    }
    /******************************* HHH END ********************************/
}
