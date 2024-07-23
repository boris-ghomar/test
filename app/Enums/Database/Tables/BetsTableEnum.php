<?php

namespace App\Enums\Database\Tables;

use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;
use App\Interfaces\Castable;

enum BetsTableEnum implements Castable
{
    use EnumToDatabaseColumnName;

        // App Internal items
    case Id;
    case UserId;
    case Partner;
    case Context; // Sport|Casino (Registered in \App\Enums\Bets::BetContextEnum)
    case IsReferralBet; // bool
    case IsQueued;
    case Descr;

        // Fetching items
    case PartnerBetId;
    case BetType; // int Singel = 1, Multiple = 2, System = 3, (Registered in \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bet::BetTypeEnum)
    case TransactionId;
    case Amount;
    case WinAmount;
    case Odds; // decimal // TotalPrice
    case BonusId; // int?
    case BonusBetAmount; // decimal
    case Status; // int Accepted = 1, Returned = 2, Lost = 3, Won = 4, CashedOut = 5, (Registered in \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bet::BetStateEnum) // State
    case CashoutAmount; // decimal?
    case IsLive; // Boolean
    case Currency; // string Currency ISO code
    case ExternalId; // long?
    case Barcode; // long The barcode printed on the betslip
    case ParentBetId; // long? In case of partial cashouts it will show main bet Id (Optional)
    case AcceptType; // int (optional) None = 0, Higher = 1, Any = 2, lower = ?
    case PlacedAt;
    case CalculatedAt; // CalcDate
    case PaidAt; // PaymentDate

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

            self::PartnerBetId, self::TransactionId, self::BonusId,
            self::ExternalId, self::Barcode, self::ParentBetId,
            => CastEnum::Int,

            self::IsLive, self::IsQueued => CastEnum::Boolean,

            self::Amount, self::WinAmount, self::Odds, self::BonusBetAmount, self::CashoutAmount,
            => CastEnum::Float,

            default => CastEnum::String
        };

        return $castEnum->cast($value);
    }
}
