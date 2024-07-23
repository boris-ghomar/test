<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bet;

use App\Enums\Bets\BetStatusEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum BetStateEnum: int
{
    use EnumActions;

    case Accepted   = 1;
    case Returned   = 2;
    case Lost       = 3;
    case Won        = 4;
    case CashedOut  = 5;

    /**
     * Convert betconstruct bet state case
     * to app bet status case by case
     *
     * @return \App\Enums\Bets\BetStatusEnum|null
     */
    public function getAppBetStatus(): ?BetStatusEnum
    {
        return match ($this) {

            self::Accepted  => BetStatusEnum::Accepted,
            self::Returned  => BetStatusEnum::Returned,
            self::Lost      => BetStatusEnum::Lost,
            self::Won       => BetStatusEnum::Won,
            self::CashedOut => BetStatusEnum::CashedOut,

            default => null
        };
    }

    /**
     * Convert betconstruct bet state case
     * to app bet status case by value
     *
     * @param null|int $value
     * @return \App\Enums\Bets\BetStatusEnum|null
     */
    public static function getAppBetStatusByValue(?int $value): ?BetStatusEnum
    {
        if (is_null($value))
            return null;

        $case = self::getCaseByValue($value);

        return is_null($case) ? null : $case->getAppBetStatus();
    }
}
