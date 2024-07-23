<?php

namespace App\Enums\Bets;

use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum BetAcceptTypeEnum
{
    use EnumActions;

    /**
     * NOTE
     *
     * In case of update, the database tables that have used
     * this Enum should be searched and updated.
     */

    /**
     * Condition that the client has accepted to place the bet.
     */

    case None;
    case Any;
    case Higher;
    case Lower;
}
