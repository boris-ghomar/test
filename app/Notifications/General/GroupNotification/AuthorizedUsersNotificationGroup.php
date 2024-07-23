<?php

namespace App\Notifications\General\GroupNotification;

use App\Enums\Database\Tables\PermissionsTableEnum;
use App\Models\BackOffice\AccessControl\Permission;
use App\Notifications\SuperClasses\SuperGroupNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Notification;

class AuthorizedUsersNotificationGroup extends SuperGroupNotification
{

    private Collection $permissions;

    /**
     * Create a new notification instance.
     *
     * @param [object of Notification Class] $notification :: like as new SystemRuntimeError()
     * @return void
     */
    public function __construct(Notification $notification, string $route, array $abilities)
    {
        $this->permissions = $this->getPermissions($route, $abilities);

        parent::__construct($notification);
    }

    /**
     * Notification will be send to only users that own the permission
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function notifiableGroup(): Collection
    {
        $roles = new Collection();
        foreach($this->permissions as $permission){
            $roles = $roles->merge($permission->rolesActive);
        }

        $notifiableUsers = new Collection();
        foreach($roles as $role){
            $notifiableUsers = $notifiableUsers->merge($role->usersActive);
        }

        return $notifiableUsers;
    }

    /**
     * Create collection of requested permissions
     *
     * @param  string $route
     * @param  array $abilities
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getPermissions(string $route, array $abilities): Collection
    {

        $query = Permission::AllItems([
            PermissionsTableEnum::Route->dbName()       => $route,
            PermissionsTableEnum::IsActive->dbName()    => true
        ]);

        $query->where(function (Builder $query) use ($abilities) {

            foreach ($abilities as $ability) {

                $query->orWhere(PermissionsTableEnum::Ability->dbName(), $ability);
            }
        });

        return $query->get();
    }


}
