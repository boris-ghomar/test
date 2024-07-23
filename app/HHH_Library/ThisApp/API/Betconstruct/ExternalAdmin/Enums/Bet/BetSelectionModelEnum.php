<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bet;

use App\Enums\Database\Tables\BetSelectionsTableEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\LogCreator;
use App\HHH_Library\general\php\traits\Enums\EnumCastParams;
use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;
use App\Interfaces\Castable;
use App\Models\BackOffice\Bets\BetSelection;
use App\Models\General\ApiNewAttrinute;
use Carbon\Carbon;

enum BetSelectionModelEnum implements Castable
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

    case SelectionId; // long
    case SelectionName; // string
    case MarketTypeId; //long
    case MarketName; // string
    case MatchId; // int
    case MatchShortId; // int
    case MatchName; // string
    case MatchStartDate; // DateTime UTC
    case RegionId; // int
    case RegionName; // string
    case CompetitionId; // int
    case CompetitionName; // string
    case SportId; // int
    case SportName; // string
    case SportAlias; // string
    case Price; // decimal
    case IsLive; // bool
    case Basis; // Decimal
    case MatchInfo; // string
    case SelectionScore; // string
    case IsOutright; // bool
    case ReSettlementReason; // string
    case State; // int NotResulted = 0, Returned = 2, Lost = 3, Won = 4, WinReturn = 5, LossReturn = 6 (Registerd in App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bet::BetSelectionStateEnum)
    case RequestHash; // string


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

            self::SelectionId, self::MarketTypeId, self::MatchId, self::MatchShortId,
            self::RegionId, self::CompetitionId, self::SportId, self::State
            => CastEnum::Int,

            self::IsLive, self::IsOutright  => CastEnum::Boolean,

            self::Price, self::Basis => CastEnum::Float,

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

            $betModelCase = BetSelectionsTableEnum::SelectionId;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::SelectionId, $betModelCase);

            $betModelCase = BetSelectionsTableEnum::SelectionName;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::SelectionName, $betModelCase);

            $betModelCase = BetSelectionsTableEnum::MarketId;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::MarketTypeId, $betModelCase);

            $betModelCase = BetSelectionsTableEnum::MarketName;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::MarketName, $betModelCase);

            $betModelCase = BetSelectionsTableEnum::MatchId;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::MatchId, $betModelCase);

            $betModelCase = BetSelectionsTableEnum::MatchShortId;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::MatchShortId, $betModelCase);

            $betModelCase = BetSelectionsTableEnum::MatchName;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::MatchName, $betModelCase);

            $betModelCase = BetSelectionsTableEnum::RegionId;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::RegionId, $betModelCase);

            $betModelCase = BetSelectionsTableEnum::RegionName;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::RegionName, $betModelCase);

            $betModelCase = BetSelectionsTableEnum::CompetitionId;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::CompetitionId, $betModelCase);

            $betModelCase = BetSelectionsTableEnum::CompetitionName;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::CompetitionName, $betModelCase);

            $betModelCase = BetSelectionsTableEnum::SportId;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::SportId, $betModelCase);

            $betModelCase = BetSelectionsTableEnum::SportName;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::SportName, $betModelCase);

            $betModelCase = BetSelectionsTableEnum::SportAlias;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::SportAlias, $betModelCase);

            $betModelCase = BetSelectionsTableEnum::Odds;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::Price, $betModelCase);

            $betModelCase = BetSelectionsTableEnum::IsLive;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::IsLive, $betModelCase);

            $betModelCase = BetSelectionsTableEnum::Basis;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::Basis, $betModelCase);

            $betModelCase = BetSelectionsTableEnum::MatchInfo;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::MatchInfo, $betModelCase);

            $betModelCase = BetSelectionsTableEnum::SelectionScore;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::SelectionScore, $betModelCase);

            $betModelCase = BetSelectionsTableEnum::IsOutright;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::IsOutright, $betModelCase);

            $betModelCase = BetSelectionsTableEnum::ResettlementReason;
            $res[$betModelCase->dbName()] = self::getApiAttributeValue($inputs, self::ReSettlementReason, $betModelCase);

            $betModelCase = BetSelectionsTableEnum::Status;
            $betStatus = BetSelectionStateEnum::getAppBetSelectionStatusByValue(self::getApiAttributeValue($inputs, self::State, $betModelCase));
            $res[$betModelCase->dbName()] = is_null($betStatus) ? null : $betStatus->name;

            $betModelCase = BetSelectionsTableEnum::MatchStartDate;
            $res[$betModelCase->dbName()] = self::modifyDateAttributes(self::getApiAttributeValue($inputs, self::MatchStartDate, $betModelCase));

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
     * @param  null|\App\Enums\Database\Tables\BetSelectionsTableEnum $betSelectionsCase
     * @return mixed
     */
    public static function getApiAttributeValue(array $inputs, self $case, ?BetSelectionsTableEnum $betSelectionsCase): mixed
    {
        $key = $case->name;

        $value = isset($inputs[$key]) ? $inputs[$key] : null;

        if (is_null($betSelectionsCase))
            return $value;

        return is_null($value) ? null : $betSelectionsCase->cast($value);
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
     * @param  null|\App\Models\BackOffice\Bets\BetSelection $model (optinal) is_null($model) ? new model will be create
     * @return \App\Models\BackOffice\Bets\BetSelection
     */
    public static function fillModel(array $inputs, BetSelection|null $model = null): BetSelection
    {

        if (is_null($model)) {

            if (array_key_exists(BetSelectionsTableEnum::Id->dbName(), $inputs)) {
                $model = BetSelection::find($inputs[BetSelectionsTableEnum::Id->dbName()]);
            }

            if (is_null($model))
                $model = new BetSelection();
        }

        foreach ($inputs as $key => $value) {

            $case = self::getCase($key);

            if (is_null($case)) {

                // Save the new attribute to the database to update the library with changes
                ApiNewAttrinute::saveNewItem(__CLASS__, $key, $value);
            }
        }

        $model->fill(self::convertApiInputsToBetModelArray($inputs));
        return $model;
    }
}
