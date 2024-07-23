<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bonus;

use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\traits\Enums\EnumCastParams;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Traits\FilterableEnum;
use App\Interfaces\Castable;

enum FilterPartnerBonusModelEnum implements Castable
{
    use EnumCastParams;
    use FilterableEnum;

    case Type; // int? SportBonus = 1,WageringBonus = 2, FreeBet = 6 (Registered in App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bonus\BonusTypeEnum)

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

            self::Type => CastEnum::Int,

            default => CastEnum::String
        };

        return $castEnum->cast($value);
    }
}
