<?php

namespace App\Policies\SuperClasses;

use App\Models\User;
use App\Policies\traits\CheckPermission;
use Illuminate\Auth\Access\HandlesAuthorization;

abstract class superPolicy
{
    use HandlesAuthorization;
    use CheckPermission;

    /************************ implements *******************************/
    /**
     * This function returns the main path of this section,
     * which is specified in the config file('hh_config.php').
     *
     * Example:
     *  //based on "routes\web.php" names;
     *  return 'BackOffice.WorkOffices.OfficeCategories';
     *
     * @return string
     */
    abstract static function getPermissionRoute(): string;

    /**
     * This function determines whether this route
     * is relevant to the user or not.
     * This permission is a priority for all permissions,
     * and even if this permission is given to the user
     * in the permission section, this function can violate it.
     *
     * @param  \App\Models\User $user
     * @return boolean
     */
    abstract static function isRelatedToUser(User $user): bool;
    /************************ implements END *******************************/

    /**
     * Perform pre-authorization checks.
     *
     * @param  \App\Models\User $user
     * @param  string $ability
     * @return ?bool
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($this->isRelatedToUser($user)) {

            if ($user->isSuperAdmin()) {
                return true;
            }
            return null;
        } else
            return false;
    }
}
