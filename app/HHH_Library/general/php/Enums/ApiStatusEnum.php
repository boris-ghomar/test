<?php

namespace App\HHH_Library\general\php\Enums;

use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum ApiStatusEnum
{
    use EnumActions;

    case Success;
        // General Failed
    case Failed;
        // For separate internal failed form external failed
    case FailedInternal;
    case FailedExternal;
}
