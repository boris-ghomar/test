<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums;


use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\HHH_Library\Calendar\CalendarHelper;
use App\HHH_Library\general\php\DatabaseHelper;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\traits\Enums\EnumCastParams;
use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;
use App\Models\General\ApiNewAttrinute;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Models\BetconstructSwarmClient;
use App\Interfaces\Castable;
use Carbon\Carbon;

enum ClientSwarmModelEnum implements Castable
{
    /**
     * NOTICE:
     *
     * To add a new itemTo this model, if you wantTo store in the database,
     * the new item must be addedTo the database table.
     *
     * DB tabel: betconstruct_clients
     */

    use EnumCastParams;
    use EnumToDatabaseColumnName;

    /** Extra Items **/
    case UserId;
    case Descr;

    /** Extra Items END**/


    case Id; // int Client Id
        // case user_id;   // int Exmp: 12015887, placed in id column
    case Username;  // string|None Exmp: "FrUqnSTP",
    case Email;  // string|None Exmp: "rhIGooRk@mailinator.com",
    case FirstName;  // string|None Exmp: "NOYbMFbz",
    case LastName;  // string|None Exmp: "SzXTnxFl",
    case MiddleName; // string|None Exmp: "xPZntXjQ",
    case Name;  // string|None Exmp: "NOYbMFbz",
    case Phone; // string|None Exmp: "11422648",
    case MobilePhone;  // string|None Exmp: "11422648",
    case BirthDate;  // time string Exmp: "1963-05-18",
    case Gender; // string|None Exmp: "M",
    case Language; // string Exmp: "ar",
    case PersonalId; // string
    case Status; // int Exmp: 0,
    case DepositCount; // int Exmp: 8,


    case ActiveTimeInCasino; // int Exmp: 48

    case BirthRegion;  // string|None Exmp: None,
    case CountryCode;  // string|None Exmp: "TD",
    case Province; // string|None Exmp: "Txqbzhsz",
    case City; // string|None Exmp: "pEjCRLVx"
    case Address;  // string Exmp: "yJXHceTf"
    case AdditionalAddress; // string Exmp: "ZxtnTjAL"
    case ZipCode;  // string|None Exmp: "ZSPtGhwT",

    case AffiliateId;  // int|None Exmp: None
    case Btag;  // string|None Exmp: "zNJDKYyl",

    case Currency;  // string|None Exmp: "AMD",
    case Balance;  // float Exmp: 0.0
    case UnplayedBalance;  // float Exmp: 0.0
    case BonusBalance;  // float Exmp: 0.0,
    case FrozenBalance; // float Exmp: 0.0,
    case BonusMoney;  // float Exmp: 0.0,
    case BonusWinBalance;  // float Exmp: 0.0,
    case SportBonus;  // float Exmp: 0.0,
    case CasinoBalance;  // float Exmp: 0.0,
    case CasinoUnplayedBalance;  // float Exmp: 0.0,
    case CasinoBonus; // float Exmp: 0.0,
    case CasinoBonusWin; // float Exmp: 0.0,
    case CasinoMaximalDailyBet;  // float Exmp: 21.0,
    case CasinoMaximalSingleBet;  // float Exmp: 16.0,
    case CounterOfferMinAmount;  // float Exmp: 16.0,

    case HasFreeBets; // bool Exmp: False,

    case Iban;  // string|None Exmp: "VRbMKGQo",
    case SwiftCode;  // string|None Exmp: "AGRIFRPI",
    case IsTaxApplicable; // bool Exmp: False,

    case IsVerified; // bool Exmp: False,
    case IsAgent; // bool Exmp: False,

    case LastLoginDate; // int Exmp: 1521097755,

    case LoyaltyLevelId;   // int Exmp: 1,
    case LoyaltyPoint; // float Exmp: 0.0,
    case LoyaltyEarnedPoints; // float Exmp: 0.0,
    case LoyaltyExchangedPoints; // float Exmp: 0.0,
    case LoyaltyLastEarnedPoints; // float Exmp: 0.0,
    case LoyaltyMaxExchangePoint; // float Exmp: 0,
    case LoyaltyMinExchangePoint; // float Exmp: 0,
    case LoyaltyPointUsagePeriod; // float Exmp: 0,

    case MaximalDailyBet; // float Exmp: 31.0,
    case MaximalSingleBet; // float Exmp: 17.0,

    case RegDate; // string|None Exmp: "2018-03-15",
    case SportsbookProfileId;  // int Exmp: 1,
    case AuthenticationStatus;  // int Exmp: 0,
    case IsTwoFactorAuthenticationEnabled; // bool Exmp: False,

    case SubscribeToBonus; // bool Exmp: True,
    case SubscribeToEmail; // bool Exmp: True,
    case SubscribeToSms; // bool Exmp: True,
    case SubscribedToNews; // bool Exmp: True,
    case SubscribeToInternalMessage; // bool Exmp: True,
    case SubscribeToPhoneCall; // bool Exmp: True,
    case SubscribeToPushNotification; // bool Exmp: True,

    case IsBonusAllowed; // bool Exmp: False,
    case IsCashOutAvailable; // bool Exmp: False,
    case IsGdprPassed; // bool Exmp: False,
    case IsPhoneVerified; // bool Exmp: False,
    case IsMobilePhoneVerified; // bool Exmp: False,
    case IsSuperBetAvailable; // bool Exmp: False,

    case Wallets;  // json array  [{"Currency": "RUB","Balance": 1053.0},{"Currency": "USD","Balance": 54.0}]
    case SupportedCurrencies; // json array  ["RUB","USD","TJS"]


        // Ignored cases from save to database
    case SiteId;
    case AuthToken;
    case CurrencyName; // it is same as currency
    case DocRegionId;
    case QrCodeOrigin;
    case LastLoginIp;
    case UniqueId; // user ID
    case TermsAndConditionsAcceptanceDate;
    case TermsAndConditionsVersion;
    case ClientNotifications; // string|None Exmp: None
    case UnreadCount;   // int Exmp: 0,
    case DocIssueCode;  // string|None Exmp: "nskbGQMe",
    case DocIssueDate;  // string|None Exmp: "1992-02-25",
    case DocIssueBy;  // string|None Exmp: "vbmDvBHr",
    case DocNumber;  // string|None Exmp: "kbpGhdgg",
    case ExcludeDate; // string|None Exmp: None,
    case IncorrectFields;  // string|None Exmp: None,
    case BirthCity;  // string|None Exmp: "Ahvaz",
    case PartnerClientCategoryId;  // int|None Exmp: 777, 301, 316, 315, 317
    case ExternalId;  // int|None Exmp: 185589, 164809, 183586, 156678, 185470
    case LastSportBetTime;  // timestamp|None Exmp: 1687517444, 1684317112, 1684505576



    /**
     * Convert the variable cast to the actual cast type.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function cast(mixed $value): mixed
    {
        if (strtolower($value) == "none")
            return null;

        /** @var CastEnum $castEnum */
        $castEnum =  match ($this) {

            self::UserId, self::Status, self::DepositCount, self::ActiveTimeInCasino, self::AffiliateId, self::LoyaltyLevelId, self::SportsbookProfileId, self::AuthenticationStatus,
            self::UnreadCount
            => CastEnum::Int,

            self::HasFreeBets, self::IsTaxApplicable, self::IsVerified, self::IsAgent, self::IsTwoFactorAuthenticationEnabled,
            self::SubscribeToBonus, self::SubscribeToEmail, self::SubscribeToSms, self::SubscribedToNews, self::SubscribeToInternalMessage, self::SubscribeToPhoneCall, self::SubscribeToPushNotification,
            self::IsBonusAllowed, self::IsCashOutAvailable, self::IsGdprPassed, self::IsPhoneVerified, self::IsMobilePhoneVerified, self::IsSuperBetAvailable
            => CastEnum::Boolean,

            self::Balance, self::UnplayedBalance, self::BonusBalance, self::FrozenBalance, self::BonusMoney, self::BonusWinBalance, self::SportBonus,
            self::CasinoBalance, self::CasinoUnplayedBalance, self::CasinoBonus, self::CasinoBonusWin, self::CasinoMaximalDailyBet, self::CasinoMaximalSingleBet, self::CounterOfferMinAmount,
            self::LoyaltyPoint, self::LoyaltyEarnedPoints, self::LoyaltyExchangedPoints, self::LoyaltyLastEarnedPoints, self::LoyaltyMaxExchangePoint, self::LoyaltyMinExchangePoint, self::LoyaltyPointUsagePeriod,
            self::MaximalDailyBet, self::MaximalSingleBet
            => CastEnum::Float,


            default => CastEnum::String
        };

        return $castEnum->cast($value);
    }

    /**
     * Get date attributes
     *
     * @return array
     */
    public static function dateAttributes(): array
    {
        return [
            self::BirthDate->dbName(),
            // self::DocIssueDate->dbName(),
            // self::ExcludeDate->dbName(),
            self::LastLoginDate->dbName(),
            self::RegDate->dbName(),
        ];
    }


    /**
     * Get ignored cases from save to database
     *
     * @param  mixed $getAsDbName
     * @return array
     */
    public static function getIgnoredSaveItems(bool $getAsDbName = true): array
    {

        $cases =  [
            self::SiteId,
            self::AuthToken,
            self::CurrencyName,
            self::DocRegionId,
            self::QrCodeOrigin,
            self::LastLoginIp,
            self::UniqueId,
            self::TermsAndConditionsAcceptanceDate,
            self::TermsAndConditionsVersion,
            self::ClientNotifications,
            self::UnreadCount,
            self::DocIssueDate,
            self::DocIssueCode,
            self::DocIssueBy,
            self::DocNumber,
            self::ExcludeDate,
            self::IncorrectFields,
            self::BirthCity,
            self::PartnerClientCategoryId,
            self::ExternalId,
            self::LastSportBetTime,

        ];

        if ($getAsDbName) {

            $dbNames = [];
            foreach ($cases as $case) {
                array_push($dbNames, $case->dbName());
            }

            return $dbNames;
        }

        return $cases;
    }

    /**
     * Get the fillable attributes for the model
     *To save in admin panel controller.
     *
     * @return array
     */
    public static function fillableController(): array
    {
        return [
            self::Descr->dbName(),
        ];
    }

    /**
     * Get the fillable attributes for the model
     *To save incoming data from API.
     *
     * @return array
     */
    public static function fillableApi(): array
    {
        $res = [];

        /**
         * Because this function is used in the model (BetconstructClient),
         * if you use the helper, an infinite loop will be created.
         *
         *  Do not use this:
         *  ModelHelper::getColumnList(DatabaseTablesEnum::BetconstructSwarmClients->tableName());
         */
        $tableColumns = DatabaseHelper::getColumnList(DatabaseTablesEnum::BetconstructSwarmClients->tableName());

        foreach (self::cases() as $case) {

            $column = $case->dbName();

            if (in_array($column, $tableColumns))
                array_push($res, $column);
        }

        return $res;
    }

    /**
     * Fill "BetconstructClient" model via input data from API
     *
     * @param  array $inputs
     * @param bool $isSwarmResponse Is $inputs come from the Betconstruct SWARM api response
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Models\BetconstructSwarmClient|null $model (optinal) is_null($model) ? new model will be create
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Models\BetconstructSwarmClient
     */
    public static function fillModel(array $inputs, bool $isSwarmResponse, BetconstructSwarmClient|null $model = null): BetconstructSwarmClient
    {

        if (is_null($model)) {

            if (array_key_exists(self::Id->name, $inputs))
                $model = BetconstructSwarmClient::find($inputs[self::Id->name]);

            if (array_key_exists(self::Id->dbName(), $inputs))
                $model = BetconstructSwarmClient::find($inputs[self::Id->dbName()]);

            if (is_null($model))
                $model = new BetconstructSwarmClient();
        }

        $fillable = self::fillableApi();

        $ignoredSaveItems = self::getIgnoredSaveItems();

        foreach ($inputs as $key => $value) {

            if (in_array($key, $fillable)) {

                $keyCase = self::getCaseByDbName($key);

                if (!is_null($keyCase)) {

                    if ($isSwarmResponse && $key == self::UserId->dbName())
                        // Replace ID key, because ID key is for betconstruct and user_id is foreign key for users table
                        $model[self::Id->dbName()] = $keyCase->cast($value);
                    else
                        $model->$key = $keyCase->cast($value);
                }
            } else {

                if (!in_array($key, $ignoredSaveItems)) {

                    // Save the new attributeTo the databaseTo update the library with changes
                    ApiNewAttrinute::saveNewItem(__CLASS__, $key, $value);
                }
            }
        }

        $model[TimestampsEnum::UpdatedAt->dbName()] = \Carbon\Carbon::now();

        return self::modifyDateAttributes($model);
    }

    /**
     * Modify date attributes
     * to convert timestamp to date string
     *
     * @param \App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Models\BetconstructSwarmClient $model
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Models\BetconstructSwarmClient
     */
    private static function modifyDateAttributes(BetconstructSwarmClient $model): BetconstructSwarmClient
    {

        foreach (self::dateAttributes() as $attr) {

            // Get original attributes
            $attributes = $model->getAttributes();

            if (isset($attributes[$attr])) {

                $date = $attributes[$attr];
                // if($attr== 'last_login_date') dd($date);
                if (empty($date)) {
                    $model[$attr] = null;
                } else if (CalendarHelper::isTimestamp($date)) {
                    // date is comming from API
                    $model[$attr] = CalendarHelper::converTimstampToDateString($date);
                } else {
                    $model[$attr] = (new Carbon($date))->toDateTimeString();
                }
            }
        }

        return $model;
    }
}
