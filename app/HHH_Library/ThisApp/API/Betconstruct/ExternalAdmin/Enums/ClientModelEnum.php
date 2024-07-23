<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\HHH_Library\Calendar\CalendarHelper;
use App\HHH_Library\general\php\DatabaseHelper;
use App\HHH_Library\general\php\Enums\ApiStatusEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\LogCreator;
use App\HHH_Library\general\php\traits\Enums\EnumCastParams;
use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\ExternalAdminAPI;
use App\Models\General\ApiNewAttrinute;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient;
use App\Interfaces\Castable;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

enum ClientModelEnum implements Castable
{
    /**
     * NOTICE:
     *
     * To add a new item to this model, if you want to store in the database,
     * the new item must be added to the database table.
     *
     * DB tabel: betconstruct_clients
     */

    use EnumCastParams;
    use EnumToDatabaseColumnName;

        // Items that do not need to be stored in the database
    case RequestHash; //  string required for partner verification

    /** Internal Items **/
    case UserId;
    case Descr;
    case MobileVerifiedAtInternal;
    case ProvinceInternal;
    case CityInternal;
    case JobFieldInternal;
    case ContactNumbersInternal;
    case ContactMethodsInternal;
    case CallerGenderInternal;

    case DepositCount; // int comes from SwarmAPi=>LoginRequest
    /** Internal Items END **/


    case Id; // int Client Id
    case Login; //  string username of client in website
    case Password; // string
    case Email; // string
    case FirstName; //  string
    case LastName; //  string
    case MiddleName; //  string
    case Name; //  string LastName + FirstName + MiddleName
    case NickName; // string
    case Phone; // string
    case MobilePhone; //  string
    case BirthDateStamp; // long? UNIX timestamp representation of registration date
    case Gender; //  int? Male = 1, Female = 2
    case Language; // string prefered language of client, ISO 639-1 codes
    case RegionCode; //  string ISO ALPHA-2 Code of country (FR, GB,RU)
    case TimeZone; // decimal? Timezone of client (in hours)
    case ProfileId; //  int?
    case DocNumber; //  string Passport Number of client
    case PersonalId; // string Unique identity number of client
    case BTag; //  string
    case IsTest; // bool?
    case IsLocked; // bool?
    case IsSubscribedToNewsletter; //  bool?
    case IsVerified; // bool?

    case CurrencyId; // string ISO 4217 code of currency (USD, EUR,RUB, ..)
    case Balance; // decimal
    case UnplayedBalance; // decimal
    case IBAN; //  string international bank account number of client
    case LastLoginIp; // string
    case LastLoginTimeStamp; //  long?
    case City; // sting City where client lives
    case Address; //  string
    case PromoCode; // string Promotional code by which client was registered
    case ExtAgentId; //  string
    case CreatedStamp; // long?
    case ModifiedStamp; // long?
    case ExcludedStamp; // long?
    case RFId; // string
    case ResetCode; // string
    case ResetExpireDateStamp; //  long?
    case DocIssuedBy; // string
    case PreMatchSelectionLimit; // decimal?
    case LiveSelectionLimit; // decimal?
    case SportsbookProfileId; //  int?
    case GlobalLiveDelay; // int?
    case ExcludedLastStamp; // long?
    case ExternalId; // string
    case ZipCode; // string
    case TermsAndConditionsVersion; // string?
    case IsExcludedFromBonuses; // bool
    case CustomPlayerCategory; // int?

    case MaxMonthlyDeposit; //  decimal? Set limit during create client
    case MaxWeeklyDeposit; // decimal? Set limit during create client
    case MaxYearlyDeposit; // decimal? Set limit during create client
    case MaxSingleDeposit; // decimal? Set limit during create client
    case MaxDailyDeposit; // decimal? Set limit during create client

    case CanLogin; // bool?
    case CanDeposit; // bool?
    case CanWithdraw; // bool?
    case CanBet; // bool?


    /**
     * Register the extra internal cases
     * These items do not come from BC and are not updated with BC
     *
     * @param  bool $returnName
     * @return array
     */
    public static function internalCases(bool $returnName = false): array
    {
        $internalCases = [
            self::UserId,
            self::Descr,
            self::DepositCount,
            self::MobileVerifiedAtInternal,
            self::ProvinceInternal,
            self::CityInternal,
            self::JobFieldInternal,
            self::ContactNumbersInternal,
            self::ContactMethodsInternal,
            self::CallerGenderInternal,
        ];

        if ($returnName) {

            $names = [];
            foreach ($internalCases as $case)
                array_push($names, $case->name);

            return $names;
        }

        return $internalCases;
    }

    /**
     * Register the items that you don't want to fill with api response
     * The model does not fill these items and you have to fill them yourself if needed
     *
     * @param bool $returnName
     * @return array
     */
    public static function unfillableCases(bool $returnName = false): array
    {

        $unfillableCases = [
            self::Password,
        ];

        if ($returnName) {

            $names = [];
            foreach ($unfillableCases as $case)
                array_push($names, $case->name);

            return $names;
        }

        return $unfillableCases;
    }

    /**
     * These must be in uppercase letters when submitted to Betconstruct.
     *
     * @param  bool $returnName
     * @return array
     */
    public static function uppercaseCases(bool $returnName = false): array
    {
        $items = [
            self::CurrencyId,
            self::RegionCode,
        ];

        if ($returnName) {

            $names = [];
            foreach ($items as $case)
                array_push($names, $case->name);

            return $names;
        }

        return $items;
    }

    /**
     * Convert the variable cast to the actual cast type.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function cast(mixed $value): mixed
    {
        /** @var CastEnum $castEnum */
        $castEnum =  match ($this) {

            self::Id, self::Gender, self::ProfileId, self::SportsbookProfileId, self::GlobalLiveDelay, self::CustomPlayerCategory => CastEnum::Int,
            self::IsLocked, self::IsSubscribedToNewsletter, self::IsVerified, self::IsTest, self::CanLogin, self::CanDeposit, self::CanWithdraw, self::CanBet, self::IsExcludedFromBonuses => CastEnum::Boolean,
            // self::BirthDateStamp, self::CreatedStamp, self::ModifiedStamp, self::ExcludedStamp, self::ResetExpireDateStamp, self::LastLoginTimeStamp, self::ExcludedLastStamp => CastEnum::Float, // let them to go as string
            self::TimeZone, self::Balance, self::PreMatchSelectionLimit, self::LiveSelectionLimit, self::UnplayedBalance, self::MaxMonthlyDeposit, self::MaxWeeklyDeposit, self::MaxYearlyDeposit, self::MaxSingleDeposit, self::MaxDailyDeposit => CastEnum::Float,

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
            self::BirthDateStamp->dbName(),
            self::LastLoginTimeStamp->dbName(),
            self::CreatedStamp->dbName(),
            self::ModifiedStamp->dbName(),
            self::ExcludedStamp->dbName(),
            self::ResetExpireDateStamp->dbName(),
            self::ExcludedLastStamp->dbName(),
        ];
    }

    /**
     * Check if attribute is a date
     *
     * @return bool
     */
    public function isDateAttribute(): bool
    {
        return in_array($this->dbName(), self::dateAttributes());
    }

    /**
     * Get the fillable attributes for the model
     * to save in admin panel controller.
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
     * to save incoming data from API.
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
         *  ModelHelper::getColumnList(DatabaseTablesEnum::BetconstructClients->tableName());
         */
        $tableColumns = DatabaseHelper::getColumnList(DatabaseTablesEnum::BetconstructClients->tableName());

        $internalItems = self::internalCases(true);
        $unfillableItems = self::unfillableCases(true);

        $notAllowedItems = array_merge($internalItems, $unfillableItems);

        foreach (self::cases() as $case) {

            $column = $case->dbName();

            if (in_array($column, $tableColumns) && !in_array($case->name, $notAllowedItems))
                array_push($res, $column);
        }

        return $res;
    }

    /**
     * Fill "BetconstructClient" model via input data from API
     *
     * @param  array $inputs
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient|null $model (optinal) is_null($model) ? new model will be create
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient
     */
    public static function fillModel(array $inputs, BetconstructClient|null $model = null): BetconstructClient
    {
        if (is_null($model)) {

            if (array_key_exists(self::Id->name, $inputs)) {
                $model = BetconstructClient::find($inputs[self::Id->name]);
            }

            if (is_null($model))
                $model = new BetconstructClient();
        }

        $fillable = self::fillableApi();

        foreach ($inputs as $key => $value) {

            $case = self::getCase($key);

            if (!is_null($case)) {

                $caseDbName = $case->dbName();

                if (in_array($caseDbName, $fillable)) {

                    $model->$caseDbName = $case->cast($value);
                }
            } else {
                // Save the new attribute to the database to update the library with changes
                ApiNewAttrinute::saveNewItem(__CLASS__, $key, $value);
            }
        }

        $model[TimestampsEnum::UpdatedAt->dbName()] = \Carbon\Carbon::now();

        return self::modifyInputData($model);
    }

    /**
     * Convert data to client model for update client on Betconstruct
     *
     * @param array $data
     * @return array
     */
    public static function convertDataToBcModel(array $data): array
    {
        $params = [];

        $internalItems = self::internalCases(true);
        $unfillableItems = self::unfillableCases(true);
        $uppercaseNames = self::uppercaseCases(true);

        $notAllowedItems = array_merge($internalItems, $unfillableItems);

        foreach ($data as $key => $value) {

            $case = self::getCase($key);

            if (is_null($case))
                $case = self::getCaseByDbName($key);

            if (!is_null($case) && !in_array($case->name, $notAllowedItems)) {

                if ($case->isDateAttribute()) {

                    try {
                        $timestamp = is_string($value) ?  Carbon::parse($value)->timestamp : $value;
                        $value = $timestamp;
                    } catch (\Throwable $th) {
                    }

                    $params[$case->name] = $value;
                } else {

                    $params[$case->name] = in_array($case->name, $uppercaseNames) ? strtoupper($case->cast($value)) : $case->cast($value);
                }
            }
        }

        return $params;
    }

    /**
     * Modify date attributes
     * to convert timestamp to date string
     *
     * @param \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient $model
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient
     */
    private static function modifyInputData(BetconstructClient $model): BetconstructClient
    {
        // City
        $key = self::City->dbName();
        $model[$key] = Str::limit($model[$key], 50, '');

        // Date attributes
        $model = self::modifyDateAttributes($model);
        return $model;
    }

    /**
     * Modify date attributes
     * to convert timestamp to date string
     *
     * @param \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient $model
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient
     */
    private static function modifyDateAttributes(BetconstructClient $model): BetconstructClient
    {

        foreach (self::dateAttributes() as $attr) {

            // Get original attributes
            $attributes = $model->getAttributes();

            if (isset($attributes[$attr])) {

                $date = $attributes[$attr];

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

    /**
     * Update client data on the Betconstruct
     *
     * @param \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient $betconstructClient
     * @return bool|string|App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient
     * BetconstructClient: updated successfully | false: No need to any action | array: error message
     */
    public static function updateBetconstructClientData(BetconstructClient $betconstructClient): bool|string|BetconstructClient
    {
        try {
            $dirtyItems = $betconstructClient->getDirty();

            if (empty($dirtyItems))
                return false; // No need to any action

            $bcDirtyItems = self::convertDataToBcModel($dirtyItems);

            if (!empty($bcDirtyItems)) {
                // Sync updated data with Betconstruct

                $updateClientResponse = ExternalAdminAPI::updateClient($betconstructClient[self::Id->dbName()], $bcDirtyItems);
                // $updateClientResponse = ApiResponseTest::updateClient(TestResponseEnum::UpdateClient_Failed_DuplicateIBAN); // Do not clean this for fast action if need

                if ($updateClientResponse->getStatus()->name === ApiStatusEnum::Success->name) {

                    $betconstructClient = self::fillModel($updateClientResponse->getData(), $betconstructClient);
                } else {
                    return $updateClientResponse->getErrorMessage();
                }
            }

            $betconstructClient->save();
            return $betconstructClient;
        } catch (\Throwable $th) {

            $errorMessage = sprintf(
                "%s\nModel Data:\n%s\nDirty Items:\n%s",
                $th->getMessage(),
                $betconstructClient,
                json_encode($dirtyItems),
            );

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                $errorMessage,
                'Update Betconstruct client data issue'
            );

            return __('error.UnknownError');
        }
    }

    /**
     * Check if client's username is duplicate
     *
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient $betconstructClient
     * @param  ?string $newEmail
     * @return ?string null: no error, string: error message
     */
    public static function checkUsernameDuplicate(?string $username, ?string $duplicateError = null): ?string
    {
        if (empty($username))
            return __('validation.required', ['attribute' => __('general.UserName')]);

        $clientsResponse = ExternalAdminAPI::getClientByUsername($username);

        if ($clientsResponse->getStatus()->name === ApiStatusEnum::Success->name) {

            $clientsData = (new Collection($clientsResponse->getData()));

            if (!$clientsData->isEmpty())
                return empty($duplicateError) ? __('bc_api.DuplicateUsername') : $duplicateError;
        } else {
            $apiError = $clientsResponse->getErrorMessage();
            return empty($apiError) ? __('error.UnknownError') : $apiError;
        }

        return null;
    }

    /**
     * Check if client's email is duplicate
     *
     * @param  null|\App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient $betconstructClient
     * @param  ?string $newEmail
     * @return ?string null: no error, string: error message
     */
    public static function checkEmailDuplicate(?BetconstructClient $betconstructClient, ?string $newEmail): ?string
    {
        if (empty($newEmail))
            return __('validation.required', ['attribute' => __('general.Email')]);

        $clientsResponse = ExternalAdminAPI::getClientByEmail($newEmail);

        if ($clientsResponse->getStatus()->name === ApiStatusEnum::Success->name) {

            $clientsData = (new Collection($clientsResponse->getData()));

            if (is_null($betconstructClient)) {

                if (!$clientsData->isEmpty())
                    return __('bc_api.DuplicateEmail');
            } else {

                $currentClientBcId = $betconstructClient[self::Id->dbName()];

                foreach ($clientsData as $clientData) {

                    // Ignore current client
                    if ($clientData[ClientModelEnum::Id->name] != $currentClientBcId)
                        return __('bc_api.DuplicateEmail');
                }
            }
        } else {
            $apiError = $clientsResponse->getErrorMessage();
            return empty($apiError) ? __('error.UnknownError') : $apiError;
        }

        return null;
    }

    /**
     * Check if client's phone number is duplicate
     *
     * @param  ?string $phoneNumber
     * @param  ?string $duplicateError -optional-
     * @return ?string null: no error, string: error message
     */
    public static function checkPhoneNumberDuplicate(?string $phoneNumber, ?string $duplicateError = null): ?string
    {
        if (empty($phoneNumber))
            return __('validation.required', ['attribute' => __('general.PhoneNumber')]);

        $clientsResponse = ExternalAdminAPI::getClientByPhoneNumber($phoneNumber);

        if ($clientsResponse->getStatus()->name === ApiStatusEnum::Success->name) {

            $clientsData = (new Collection($clientsResponse->getData()));

            if (!$clientsData->isEmpty())
                return empty($duplicateError) ? __('bc_api.DuplicatePhoneNumber') : $duplicateError;
        } else {
            $apiError = $clientsResponse->getErrorMessage();
            return empty($apiError) ? __('error.UnknownError') : $apiError;
        }

        return null;
    }

    /**
     * Check if client's mobile number is duplicate
     *
     * @param  ?string $newMobileNumber
     * @param  ?string $duplicateError -optional-
     * @return ?string null: no error, string: error message
     */
    public static function checkMobileNumberDuplicate(?string $newMobileNumber, ?string $duplicateError = null): ?string
    {
        if (empty($newMobileNumber))
            return __('validation.required', ['attribute' => __('general.MobileNumber')]);

        $clientsResponse = ExternalAdminAPI::getClientByMobilePhone($newMobileNumber);

        if ($clientsResponse->getStatus()->name === ApiStatusEnum::Success->name) {

            $clientsData = (new Collection($clientsResponse->getData()));

            if (!$clientsData->isEmpty())
                return empty($duplicateError) ? __('bc_api.DuplicateMobileNumber') : $duplicateError;
        } else {
            $apiError = $clientsResponse->getErrorMessage();
            return empty($apiError) ? __('error.UnknownError') : $apiError;
        }

        return null;
    }
}
