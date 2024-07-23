<?php

namespace App\Enums\Database\Tables;

use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;

enum PasswordResetTokenEnum
{
    use EnumToDatabaseColumnName;

    case Email;
    case Token;
}
