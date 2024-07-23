<?php

namespace App\Enums\Database\Tables;

use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;
use App\Interfaces\Castable;

enum BetSelectionsTableEnum implements Castable
{
    use EnumToDatabaseColumnName;

    case Id;
    case BetId;
    case SelectionId; // long
    case SelectionName; // string
    case MarketId; //long // MarketTypeId
    case MarketName; // string
    case MatchId; // int
    case MatchShortId; // int
    case MatchName; // string
    case RegionId; // int
    case RegionName; // string
    case CompetitionId; // int
    case CompetitionName; // string
    case SportId; // int
    case SportName; // string
    case SportAlias; // string
    case Odds; // decimal // Price
    case IsLive; // bool
    case Basis; // Decimal
    case MatchInfo; // string
    case SelectionScore; // string
    case IsOutright; // bool
    case ResettlementReason; // string
    case Status; // int NotResulted = 0, Returned = 2, Lost = 3, Won = 4, WinReturn = 5, LossReturn = 6 (Registerd in App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bet::BetSelectionStateEnum)
    case MatchStartDate; // DateTime UTC


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

            self::BetId, self::SelectionId, self::MarketId, self::MatchId, self::MatchShortId,
            self::RegionId, self::CompetitionId, self::SportId
            => CastEnum::Int,

            self::IsLive, self::IsOutright  => CastEnum::Boolean,

            self::Odds, self::Basis => CastEnum::Float,

            default => CastEnum::String
        };

        return $castEnum->cast($value);
    }
}
