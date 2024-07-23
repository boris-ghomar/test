<?php

namespace App\Enums\Database\Tables;

use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;
use App\Interfaces\Castable;

enum ReferralRewardPaymentsTableEnum implements Castable
{
    use EnumToDatabaseColumnName;

    case Id;
    case UserId; // Referrer user id
    case RewardConclusionsId;
    case RewardItemId;
    case Amount;
    case IsSuccessful;
    case IsDone;
    case QueuedAt;
    case Descr;
    case SystemMessage;

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
