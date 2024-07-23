<?php

namespace App\Policies\BackOffice\Global;

use App\Enums\Routes\AdminRoutesEnum;
use App\Models\User;
use App\Policies\SuperClasses\superPolicy;

class GlobalViewClientEmailPolicy extends superPolicy
{
    /**
     * This function returns the main path of this section,
     * which is specified in the config file('hh_config.php').
     *
     * @return string
     */

    public static function getPermissionRoute(): string
    {
        return AdminRoutesEnum::Global_ViewClientEmail->value;
    }

    /**
     * This function determines whether this route
     * is relevant to the user or not.
     * This permission is a priority for all permissions,
     * and even if this permission is given to the user
     * in the permission section, this function can violate it.
     *
     * @return boolean
     */
    public static function isRelatedToUser(User $user): bool
    {
        return $user->isPersonnel() || $user->isClient();
    }

    /**
     * Determine whether the user can view any models.
     */
    public function view(User $user): bool
    {
        // Client needs to see their profile
        return $user->isClient() ? true : $this->isPermissionValid($user, __FUNCTION__);
    }
}
