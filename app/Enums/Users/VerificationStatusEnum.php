<?php

namespace App\Enums\Users;

use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum VerificationStatusEnum
{
    use EnumActions;

    case Verified;
    case NeedVerify;
    case NoNeedVerify;
    case UnderVerify;
}
