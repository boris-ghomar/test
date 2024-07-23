<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bet;

use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\traits\Enums\EnumCastParams;
use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;
use App\Interfaces\Castable;

enum BetModelEnum implements Castable
{
    /**
     * NOTICE:
     *
     * To add a new item to this model, if you want to store in the database,
     * the new item must be added to the database table.
     *
     * DB tabel: betconstruct_bets
     */

    use EnumCastParams;
    use EnumToDatabaseColumnName;

    case AuthToken; // string
    case TransactionId; // long
    case BetId; // long
    case Amount; // decimal
    case WinAmount; // decimal
    case Created; // datetime UTC date
    case CalcDate; // datetime UTC date
    case PaymentDate; // datetime UTC date
    case BetType; // int Singel = 1, Multiple = 2, System = 3, (Registered in \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bet::BetTypeEnum)
    case SystemMinCount; // int?
    case SelectionCount; // int?
    case TotalPrice; // decimal
    case BonusBetAmount; // decimal
    case BonusId; // int?
    case Source; // int?
    case State; // int Accepted = 1, Returned = 2, Lost = 3, Won = 4, CashedOut = 5, (Registered in \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bet::BetStateEnum)
    case ClientId; // int? Player’s Id (optional)
    case CashoutAmount; // decimal?
    case IsLive; // Boolean
    case Currency; // string Currency ISO code
    case BetShop; // string Betshop name
    case Number; // long Cashdesk’s betslip number
    case ExternalId; // long?
    case PaymentBetShop; // string Payment batshop’s name
    case BarCode; // long The barcode printed on the betslip
    case BetShopGroupName; // string Betshop group name
    case ParentBetId; // long? In case of partial cashouts it will show main bet Id (Optional)
    case AcceptTypeId; // int (optional) None = 0, Higher = 1, Any = 2,
    case AcceptLowerOdds; // bool(optional) If true then bet is accepted with lower odds
    case Selections; // List of BetSelectionModel objects
    case OddType;
    case HashCode;
    case CashDesk;
    case CashDeskId;
    case InfoCashDeskId;
    case PossibleTaxAmount;
    case PossibleWin;
    case IsEachWay;
    case HashToCheck;
    case RequestHash;


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

            self::TransactionId, self::BetId, self::BetType, self::SystemMinCount, self::SelectionCount,
            self::BonusId, self::Source, self::State, self::ClientId, self::Number, self::ExternalId,
            self::BarCode, self::ParentBetId, self::AcceptTypeId, self::OddType, self::CashDeskId, self::InfoCashDeskId
            => CastEnum::Int,

            self::IsLive, self::AcceptLowerOdds, self::IsEachWay => CastEnum::Boolean,

            self::Amount, self::WinAmount, self::TotalPrice, self::BonusBetAmount, self::CashoutAmount,
            self::CashoutAmount, self::PossibleTaxAmount, self::PossibleWin
            => CastEnum::Float,

            default => CastEnum::String
        };

        return $castEnum->cast($value);
    }
}
