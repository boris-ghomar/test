<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Balance;

use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum BalanceCorrectionTypeEnum: int
{
    use EnumActions;

        // Based on Betconstruct ExternalAdmin-API Documentation

    case CorrectionUp       = 301;
    case CorrectionDown     = 302;
    case BounusCorrection   = 303;
}
