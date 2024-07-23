<?php

namespace App\Enums\SystemReserved;

use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\Users\RoleTypesEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\Models\BackOffice\PeronnelManagement\PersonnelRole;

enum AdminRoleReservedEnum: string
{
    use EnumActions;

    case SuperAdmin      = 'Super Admin';

    /**
     * Description of reserved roles
     *
     * @return string
     */
    public function description(): string
    {
        return match ($this) {

            self::SuperAdmin => "Full access user. (Reserved by system)",

            default => ''
        };
    }

    /**
     * Get type of role
     *
     * @param bool $returnCase return ($returnCase ?  case : case->name)
     * @return \App\Enums\Users\RoleTypesEnum|string
     */
    public function type(bool $returnCase = false): RoleTypesEnum|string
    {

        return $returnCase ? RoleTypesEnum::AdminPanel : RoleTypesEnum::AdminPanel->name;
    }

    /**
     * Get model of role
     *
     * @return \App\Models\BackOffice\PeronnelManagement\PersonnelRole|null
     */
    public function model(): PersonnelRole|null
    {
        return PersonnelRole::where(RolesTableEnum::Name->dbName(), $this->value)->first();
    }
}
