<?php

namespace App\Notifications\BackOffice\GroupNotification;

use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\SystemReserved\AdminRoleReservedEnum;
use App\Models\BackOffice\PeronnelManagement\PersonnelRole;
use App\Notifications\SuperClasses\SuperGroupNotification;
use Illuminate\Database\Eloquent\Collection;

class SystemAdminNotificationGroup extends SuperGroupNotification
{

    /**
     * Get 'SystemAdmin' role instance
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function notifiableGroup(): Collection
    {

        $systemAdminRole = PersonnelRole::where(RolesTableEnum::Name->dbName(), AdminRoleReservedEnum::SuperAdmin->value)
            ->first();

        return $systemAdminRole->personnel()->ActivePersonnel()->get();
    }

}
