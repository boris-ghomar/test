<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bet;

use App\Enums\Bets\BetContextEnum;
use App\Enums\Database\Tables\BetsTableEnum;
use App\Enums\General\CurrencyEnum;
use App\Enums\General\PartnerEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\LogCreator;
use App\HHH_Library\general\php\traits\Enums\EnumCastParams;
use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;
use App\Interfaces\Castable;
use App\Models\BackOffice\Bets\Bet;
use App\Models\General\ApiNewAttrinute;
use Carbon\Carbon;

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

    /**
     * Get the fillable attributes for the model
     * to save incoming data from API.
     *
     * @param array $inputs from api response
     * @return array
     */
    public static function convertApiInputsToBetModelArray(array $inputs): ?array
    {
        try {

            $res = [];

            $res[BetsTableEnum::Partner->dbName()] = PartnerEnum::Betconstruct->name;
            $res[BetsTableEnum::Context->dbName()] = BetContextEnum::Sport->name;

            $betModelCase = BetsTableEnum::PartnerBetId;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::BetId, $betModelCase);

            $betModelCase = BetsTableEnum::BetType;
            $betType = BetTypeEnum::getAppBetTypeByValue(self::getApiAttributeValue($inputs, self::BetType, $betModelCase));
            $res[$betModelCase->dbName()] = is_null($betType) ? null : $betType->name;

            $betModelCase = BetsTableEnum::TransactionId;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::TransactionId, $betModelCase);

            $betModelCase = BetsTableEnum::Amount;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::Amount, $betModelCase);

            $betModelCase = BetsTableEnum::WinAmount;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::WinAmount, $betModelCase);

            $betModelCase = BetsTableEnum::Odds;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::TotalPrice, $betModelCase);

            $betModelCase = BetsTableEnum::BonusId;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::BonusId, $betModelCase);

            $betModelCase = BetsTableEnum::BonusBetAmount;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::BonusBetAmount, $betModelCase);

            $betModelCase = BetsTableEnum::Status;
            $betStatus = BetStateEnum::getAppBetStatusByValue(self::getApiAttributeValue($inputs, self::State, $betModelCase));
            $res[$betModelCase->dbName()] = is_null($betStatus) ? null : $betStatus->name;

            $betModelCase = BetsTableEnum::CashoutAmount;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::CashoutAmount, $betModelCase);

            $betModelCase = BetsTableEnum::IsLive;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::IsLive, $betModelCase);

            $betModelCase = BetsTableEnum::Currency;
            $currency = CurrencyEnum::getCase(strtoupper(self::getApiAttributeValue($inputs, self::Currency, $betModelCase)));
            $res[$betModelCase->dbName()] = is_null($currency) ? null : $currency->name;

            $betModelCase = BetsTableEnum::ExternalId;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::ExternalId, $betModelCase);

            $betModelCase = BetsTableEnum::Barcode;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::BarCode, $betModelCase);

            $betModelCase = BetsTableEnum::ParentBetId;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::ParentBetId, $betModelCase);

            $betModelCase = BetsTableEnum::AcceptType;
            $acceptType = BetAcceptTypeEnum::getAppBetAcceptTypeByValue(self::getApiAttributeValue($inputs, self::AcceptTypeId, $betModelCase));
            $res[$betModelCase->dbName()] = is_null($acceptType) ? null : $acceptType->name;

            $betModelCase = BetsTableEnum::PlacedAt;
            $res[$betModelCase->dbName()] = self::modifyDateAttributes(self::getApiAttributeValue($inputs, self::Created, $betModelCase));

            $betModelCase = BetsTableEnum::CalculatedAt;
            $res[$betModelCase->dbName()] = self::modifyDateAttributes(self::getApiAttributeValue($inputs, self::CalcDate, $betModelCase));

            $betModelCase = BetsTableEnum::PaidAt;
            $res[$betModelCase->dbName()] = self::modifyDateAttributes(self::getApiAttributeValue($inputs, self::PaymentDate, $betModelCase));

            return $res;
        } catch (\Throwable $th) {

            $error = sprintf(
                "Error: %s\nPartner Input Data:\n%s",
                $th->getMessage(),
                json_encode($inputs)
            );

            LogCreator::createLogAlert(
                __CLASS__,
                __FUNCTION__,
                $error,
                'Error during convert partner bet data to app model.'
            );

            return null;
        }
    }

    /**
     * Get attribute value from input array
     *
     * @param  array $inputs
     * @param  self $case
     * @param  null|\App\Enums\Database\Tables\BetsTableEnum $betCase
     * @return mixed
     */
    public static function getApiAttributeValue(array $inputs, self $case, ?BetsTableEnum $betCase): mixed
    {
        $key = $case->name;

        $value = isset($inputs[$key]) ? $inputs[$key] : null;

        if (is_null($betCase))
            return $value;

        return is_null($value) ? null : $betCase->cast($value);
    }

    /**
     * Modify date attributes
     * to convert partner date-time to app date string
     *
     * @return ?string
     */
    private static function modifyDateAttributes(?string $date): ?string
    {
        if (is_null($date))
            return null;

        try {
            return Carbon::parse($date)->toDateTimeString();
        } catch (\Throwable $th) {

            $error = sprintf(
                "Error: %s\nPartner Input Date:\n%s",
                $th->getMessage(),
                $date
            );

            LogCreator::createLogAlert(
                __CLASS__,
                __FUNCTION__,
                $error,
                'Error during convert partner date-time to app date-time.'
            );
        }

        return null;
    }

    /**
     * Fill "BetconstructClient" model via input data from API
     *
     * @param  array $inputs
     * @param  null|\App\Models\BackOffice\Bets\Bet $model (optinal) is_null($model) ? new model will be create
     * @return \App\Models\BackOffice\Bets\Bet|null
     */
    public static function fillModel(array $inputs, Bet|null $model = null): ?Bet
    {

        if (is_null($model)) {

            if (array_key_exists(BetsTableEnum::Id->dbName(), $inputs)) {
                $model = Bet::find($inputs[BetsTableEnum::Id->dbName()]);
            }

            if (is_null($model))
                $model = new Bet();
        }

        foreach ($inputs as $key => $value) {

            $case = self::getCase($key);

            if (is_null($case)) {

                // Save the new attribute to the database to update the library with changes
                ApiNewAttrinute::saveNewItem(__CLASS__, $key, $value);
            }
        }

        $appModelinputs = self::convertApiInputsToBetModelArray($inputs);
        return is_null($appModelinputs) ? null : $model->fill($appModelinputs);
    }
}
