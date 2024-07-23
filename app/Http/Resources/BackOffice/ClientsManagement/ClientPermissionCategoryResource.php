<?php

namespace App\Http\Resources\BackOffice\ClientsManagement;

use App\Enums\Database\Tables\PermissionRoleTableEnum;
use App\Enums\Database\Tables\PermissionsTableEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Http\Resources\ApiResponseResource;

class ClientPermissionCategoryResource extends ApiResponseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        if (is_null($this[PermissionsTableEnum::Route->dbName()])) {
            // resource loaded from update method

            $permission = $this->permission;
            $role = $this->role;

            $roleName = $role[RolesTableEnum::Name->dbName()];

            $route = $permission[PermissionsTableEnum::Route->dbName()];
            $ability = $permission[PermissionsTableEnum::Ability->dbName()];
        } else {
            // Resource loaded from apiIndex

            $roleName = $this[RolesTableEnum::Name->dbName()];

            $route = $this[PermissionsTableEnum::Route->dbName()];
            $ability = $this[PermissionsTableEnum::Ability->dbName()];
        }

        return [
            PermissionRoleTableEnum::PermissionId->dbName()     => (int) $this[PermissionRoleTableEnum::PermissionId->dbName()],
            PermissionRoleTableEnum::RoleId->dbName()           => (int) $this[PermissionRoleTableEnum::RoleId->dbName()],
            PermissionRoleTableEnum::IsActive->dbName()         => (bool) $this[PermissionRoleTableEnum::IsActive->dbName()],
            PermissionsTableEnum::Descr->dbName()               => $this[PermissionRoleTableEnum::Descr->dbName()],

            RolesTableEnum::Name->dbName()                      => $roleName,

            PermissionsTableEnum::Route->dbName()               => $route,
            PermissionsTableEnum::Ability->dbName()             => $ability,

        ];
    }
}
