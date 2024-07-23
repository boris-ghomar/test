<?php

namespace App\Enums\Database\Tables;

use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;

enum UserSettingsTableEnum
{
    use EnumToDatabaseColumnName;

    case Id;
    case UserId;
    case SettingId;
    case Value;
}
