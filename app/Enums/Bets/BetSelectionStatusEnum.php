<?php

namespace App\Enums\Bets;

use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum BetSelectionStatusEnum
{
    use EnumActions;

    /**
     * NOTE
     *
     * In case of update, the database tables that have used
     * this Enum should be searched and updated.
     */

    case NotResulted;
    case Returned;
    case Lost;
    case Won;
    case WinReturn;
    case LossReturn;
}
