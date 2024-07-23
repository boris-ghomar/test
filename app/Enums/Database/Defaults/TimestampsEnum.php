<?php

namespace App\Enums\Database\Defaults;

use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;

enum TimestampsEnum
{
    use EnumToDatabaseColumnName;

    case CreatedAt;
    case UpdatedAt;
    case DeletedAt;
}
