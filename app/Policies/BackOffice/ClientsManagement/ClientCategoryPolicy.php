<?php

namespace App\Policies\BackOffice\ClientsManagement;

use App\Enums\Routes\AdminRoutesEnum;
use App\Models\BackOffice\ClientsManagement\ClientCategory;
use App\Models\User;
use App\Policies\SuperClasses\superPolicy;

class ClientCategoryPolicy extends superPolicy
{
    /**
     * This function returns the main path of this section,
     * which is specified in the config file('hh_config.php').
     *
     * @return string
     */

    public static function getPermissionRoute(): string
    {
        return AdminRoutesEnum::Clients_CategoriesManagement->value;
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
        return $user->isPersonnel();
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->isPermissionValid($user, __FUNCTION__);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ClientCategory $clientCategory = null): bool
    {
        return $this->isPermissionValid($user, __FUNCTION__);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->isPermissionValid($user, __FUNCTION__);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ClientCategory $clientCategory = null): bool
    {
        return $this->isPermissionValid($user, __FUNCTION__);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ClientCategory $clientCategory = null): bool
    {
        return $this->isPermissionValid($user, __FUNCTION__);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ClientCategory $clientCategory = null): bool
    {
        return $this->isPermissionValid($user, __FUNCTION__);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ClientCategory $clientCategory = null): bool
    {
        return $this->isPermissionValid($user, __FUNCTION__);
    }
}
