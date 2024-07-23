<?php

namespace App\Enums\Database\Tables;

use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;
use App\Interfaces\Castable;

enum ReferralRewardConclusionsTableEnum implements Castable
{
    use EnumToDatabaseColumnName;

    case Id;
    case UserId; // Referrer user id
    case ReferralSessionId;
    case TotalEffectiveBetsCount; // Total number of effective bets of introduced users
    case TotalEffectiveBetsAmount; // Total amounts of effective bets of introduced users
    case RewardsCount;
    case IsDone;
    case Descr;
    case CalculatedUntil;
    case CalculatedAt;

    /**
     * Convert the variable cast to the actual cast type.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function cast(mixed $value): mixed
    {
        /**
         * Default cast is string,
         * so only register non string cases
         */

        /** @var CastEnum $castEnum */
        $castEnum = match ($this) {

            default => CastEnum::String
        };

        return $castEnum->cast($value);
    }
}
