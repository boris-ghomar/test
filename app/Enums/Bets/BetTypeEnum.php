<?php

namespace App\Enums\Bets;

use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum BetTypeEnum
{
    use EnumActions;

    /**
     * NOTE
     *
     * In case of update, the database tables that have used
     * this Enum should be searched and updated.
     */

        // Sport bet types
    case Singel;
    case Multiple;
    case System;
}
