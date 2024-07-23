<?php

namespace App\Policies\traits;

use App\Enums\Database\Tables\PermissionsTableEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Users\UsersStatusEnum;
use App\Models\BackOffice\AccessControl\Permission;
use App\Models\User;
use Illuminate\Support\Str;


trait  CheckPermission
{

    /**
     * This function checks the user permissions
     * to determine if the user is allowed to access a section.
     *
     * This function is used in policy files.
     *
     * @param User $user
     * @param  string $ability
     * @return boolean
     */
    protected function isPermissionValid(User $user, string $ability): bool
    {
        //Check user status
        if ($user[UsersTableEnum::Status->dbName()] !== UsersStatusEnum::Active->name)
            return false;

        // Check input variables
        $permissionRoute = Str::of($this->getPermissionRoute())->trim();
        $ability = Str::of($ability)->trim();

        if ($permissionRoute->isEmpty() || $ability->isEmpty()) {
            return false;
        } else {
            $permissionRoute = $permissionRoute->toString();
            $ability = $ability->toString();
        }

        // Check permission status
        $permission = Permission::where(PermissionsTableEnum::Route->dbName(), $this->getPermissionRoute())
            ->where(PermissionsTableEnum::Ability->dbName(), $ability)
            ->where(PermissionsTableEnum::IsActive->dbName(), 1)
            ->first();

        if (is_null($permission))
            return false;

        // Verify that permissions are granted to the role?
        $permittedRolesIds = $permission->rolesActive->pluck(RolesTableEnum::Id->dbName())->toArray();

        if (!in_array($user[UsersTableEnum::RoleId->dbName()], $permittedRolesIds))
            return false;

        return true;
    }


}
