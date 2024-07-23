<?php

namespace App\Enums\Database\Tables;

use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;
use App\Interfaces\Castable;

enum ReferralRewardPackagesTableEnum implements Castable
{
    use EnumToDatabaseColumnName;

    case Id;
    case Name;
    case DisplayName;
    case ClaimCount;
    case Descr;
    case IsActive;

    case MinBetCountReferrer;
    case MinBetOddsReferrer;
    case MinBetAmountUsdReferrer;
    case MinBetAmountIrrReferrer;

    case MinBetCountReferred;
    case MinBetOddsReferred;
    case MinBetAmountUsdReferred;
    case MinBetAmountIrrReferred;

    case PrivateNote;

    /**
     * Convert the variable cast to the actual cast type.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function cast(mixed $value): mixed
    {
        return match ($this) {

            default => (string) $value
        };
    }
}
