<?php

namespace App\Enums\Users;

use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum RoleTypesEnum
{
    use EnumActions;

    case AdminPanel;
    case Site;
}
