<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bet;

use App\Enums\Bets\BetTypeEnum as AppBetTypeEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum BetTypeEnum: int
{
    use EnumActions;

    case Singel     = 1;
    case Multiple   = 2;
    case System     = 3;


    /**
     * Convert betconstruct bet type case
     * to app bet type case by case
     *
     * @return \App\Enums\Bets\BetTypeEnum|null
     */
    public function getAppBetType(): ?AppBetTypeEnum
    {
        return match ($this) {

            self::Singel    => AppBetTypeEnum::Singel,
            self::Multiple  => AppBetTypeEnum::Multiple,
            self::System    => AppBetTypeEnum::System,

            default => null
        };
    }

    /**
     * Convert betconstruct bet type case
     * to app bet type case by value
     *
     * @param null|int $value
     * @return \App\Enums\Bets\BetTypeEnum|null
     */
    public static function getAppBetTypeByValue(?int $value): ?AppBetTypeEnum
    {
        if (is_null($value))
            return null;

        $case = self::getCaseByValue($value);

        return is_null($case) ? null : $case->getAppBetType();
    }
}
