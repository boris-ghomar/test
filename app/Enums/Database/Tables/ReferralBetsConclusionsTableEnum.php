<?php

namespace App\Enums\Database\Tables;

use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;
use App\Interfaces\Castable;

enum ReferralBetsConclusionsTableEnum implements Castable
{
    use EnumToDatabaseColumnName;

    case Id;
    case ReferralSessionId;
    case ReferrerId;
    case ReferredId;
    case BetsCount;
    case BetsTotalAmount;
    case CalculatedUntil;
    case CalculatedAt;
    case Descr;

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
