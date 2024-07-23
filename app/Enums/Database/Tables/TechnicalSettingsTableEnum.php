<?php

namespace App\Enums\Database\Tables;

use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;

enum TechnicalSettingsTableEnum
{
    use EnumToDatabaseColumnName;

    case Id;
    case Name;
    case Value;
    case Cast;
}
