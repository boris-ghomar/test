<?php

namespace App\Enums\Database\Defaults;

use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;

enum RememberTokenEnum
{
    use EnumToDatabaseColumnName;

    case TwoFactorSecret;
    case TwoFactorRecoveryCodes;
    case TwoFactorConfirmedAt;
    case RememberToken;

}
