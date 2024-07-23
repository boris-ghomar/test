<?php

namespace App\Enums\SystemReserved;

use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\Users\RoleTypesEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\Models\BackOffice\ClientsManagement\ClientCategory;

enum ClientCategoryReservedEnum: string
{
    use EnumActions;

    case NormalUser      = 'Normal User';


    /**
     * Description of reserved roles
     *
     * @return string
     */
    public function description(): string
    {
        return match($this){

            self::NormalUser => "Every user is placed in this category for the first time after membership, so note that this role should have the lowest level of site permissions. (Reserved by system)",

            default => ''
        };

    }

    /**
     * Get type of role
     *
     * @param bool $returnCase true ? return case
     * @return \App\Enums\Users\RoleTypesEnum|string
     */
    public function type(bool $returnCase = true): RoleTypesEnum|string
    {

        return $returnCase ? RoleTypesEnum::Site : RoleTypesEnum::Site->name;
    }


    /**
     * Get model of role
     *
     * @return App\Models\BackOffice\ClientsManagement\ClientCategory|null
     */
    public function model(): ClientCategory|null
    {
        return ClientCategory::where(RolesTableEnum::Name->dbName(), $this->value)->first();
    }
}
