<?php

namespace App\Enums\Bets;

use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum BetStatusEnum
{
    use EnumActions;

    /**
     * NOTE
     *
     * In case of update, the database tables that have used
     * this Enum should be searched and updated.
     */

    case Accepted;
    case Returned;
    case Lost;
    case Won;
    case CashedOut;
}
