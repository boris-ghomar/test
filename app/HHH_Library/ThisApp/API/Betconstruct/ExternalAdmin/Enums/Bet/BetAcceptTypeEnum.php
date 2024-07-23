<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bet;

use App\Enums\Bets\BetAcceptTypeEnum as AppBetAcceptTypeEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum BetAcceptTypeEnum: int
{
    use EnumActions;

    case None   = 0;
    case Higher = 1;
    case Any    = 2;


    /**
     * Convert betconstruct bet accept type case
     * to app bet accept type case by case
     *
     * @return \App\Enums\Bets\BetAcceptTypeEnum|null
     */
    public function getAppBetAcceptType(): ?AppBetAcceptTypeEnum
    {
        return match ($this) {

            self::None    => AppBetAcceptTypeEnum::None,
            self::Higher  => AppBetAcceptTypeEnum::Higher,
            self::Any     => AppBetAcceptTypeEnum::Any,

            default => null
        };
    }

    /**
     * Convert betconstruct bet accept type case
     * to app bet accept type case by value
     *
     * @param null|int $value
     * @return \App\Enums\Bets\BetAcceptTypeEnum|null
     */
    public static function getAppBetAcceptTypeByValue(?int $value): ?AppBetAcceptTypeEnum
    {
        if (is_null($value))
            return null;

        $case = self::getCaseByValue($value);

        return is_null($case) ? null : $case->getAppBetAcceptType();
    }
}
