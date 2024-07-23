<?php

namespace App\Enums\Database\Tables;

use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;

enum PermissionRoleTableEnum
{
    use EnumToDatabaseColumnName;

    case PermissionId;
    case RoleId;
    case IsActive;
    case Descr;

}
