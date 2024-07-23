<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bet;

use App\Enums\Bets\BetSelectionStatusEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum BetSelectionStateEnum: int
{
    use EnumActions;

    case NotResulted    = 0;
    case Returned       = 2;
    case Lost           = 3;
    case Won            = 4;
    case WinReturn      = 5;
    case LossReturn     = 6;

    /**
     * Convert betconstruct bet selection state case
     * to app bet status case by case
     *
     * @return \App\Enums\Bets\BetSelectionStatusEnum|null
     */
    public function getAppBetSelectionStatus(): ?BetSelectionStatusEnum
    {
        return match ($this) {

            self::NotResulted   => BetSelectionStatusEnum::NotResulted,
            self::Returned      => BetSelectionStatusEnum::Returned,
            self::Lost          => BetSelectionStatusEnum::Lost,
            self::Won           => BetSelectionStatusEnum::Won,
            self::WinReturn     => BetSelectionStatusEnum::WinReturn,
            self::LossReturn    => BetSelectionStatusEnum::LossReturn,

            default => null
        };
    }

    /**
     * Convert betconstruct bet selection state case
     * to app bet status case by value
     *
     * @param null|int $value
     * @return \App\Enums\Bets\BetSelectionStatusEnum|null
     */
    public static function getAppBetSelectionStatusByValue(?int $value): ?BetSelectionStatusEnum
    {
        if (is_null($value))
            return null;

        $case = self::getCaseByValue($value);

        return is_null($case) ? null : $case->getAppBetSelectionStatus();
    }
}
