<?php

namespace App\Models\BackOffice\ClientsManagement;

use App\Enums\Database\DatabaseTablesEnum as Database;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\Database\Tables\UsersTableEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\General\ModelGlobalScopesEnum;
use App\Enums\Routes\AdminRoutesEnum;
use App\Enums\Users\ClientProfileCheckEnum;
use App\Enums\Users\UsersStatusEnum;
use App\Enums\Users\UsersTypesEnum;
use App\HHH_Library\general\php\ArrayHelper;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\GendersEnum as AppGendersEnum;
use App\HHH_Library\general\php\traits\MaskModelAttribute;
use App\HHH_Library\general\php\traits\ModelSuperScopes;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\ClientSwarmModelEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;

class UserBetconstruct extends User
{
    use ModelSuperScopes;
    use MaskModelAttribute;

    private $calendarHelper;
    /**************** Parnet Items ********************/

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->table = Database::Users->tableName();

        // Base on with loginController
        $this->fillable([
            /* UsersTableEnum::Username->dbName(),
            UsersTableEnum::Email->dbName(),
            UsersTableEnum::Password->dbName(),
            UsersTableEnum::Type->dbName(), */
            UsersTableEnum::RoleId->dbName(),
            UsersTableEnum::Status->dbName(),
        ]);

        parent::__construct($attributes);
    }

    /**
     * The "booted" method of the model.
     * This scope controls only Betcart users to be loaded on all requests of this model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(ModelGlobalScopesEnum::UserBetconstruct_Only->name, function (Builder $builder) {

            $builder->where(TableEnum::Type->dbNameWithTable(Database::Users), UsersTypesEnum::BetconstructClient->name);
        });
    }

    /**
     * @override
     * Send notification with User::calss instead of UserBetconstruct::Class
     *
     * @param  mixed $instance
     * @return void
     */
    public function notify($instance)
    {
        $this->user->notify($instance);
    }

    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get personnel "user" model
     *
     * @return Illuminate\Database\Eloquent\Relations\HasOne $personnel
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, UsersTableEnum::Id->dbName(), UsersTableEnum::Id->dbName());
    }

    /**
     * Get user "betconstructClient" model
     *
     * @return Illuminate\Database\Eloquent\Relations\HasOne|null
     */
    public function betconstructClient(): HasOne|null
    {
        return $this->hasOne(BetconstructClient::class, ClientModelEnum::UserId->dbName(), UsersTableEnum::Id->dbName());
    }

    /**
     * @override
     * Get the Role that owns the UserBetconstruct
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(ClientCategory::class, TableEnum::RoleId->dbName(), RolesTableEnum::Id->dbName());
    }
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/
    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/

    /**
     * Interact with the client's Email.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function email(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $this->maskGlobalAttribute($value, AdminRoutesEnum::Global_ViewClientEmail),
        );
    }

    /**
     * Interact with the client's Email.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function phone(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $this->maskGlobalAttribute($value, AdminRoutesEnum::Global_ViewClientPhone),
        );
    }

    /**
     * Interact with the client's Email.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function mobilePhone(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $this->maskGlobalAttribute($value, AdminRoutesEnum::Global_ViewClientPhone),
        );
    }

    /**
     * Interact with the client's ContactNumbersInternal.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function contactNumbersInternal(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $this->maskGlobalAttribute(empty($value) ? [] : json_decode($value), AdminRoutesEnum::Global_ViewClientPhone),
            set: fn (?array $value) => empty($value) ? null : json_encode($value),
        );
    }

    /**************** Accessors & Mutators END ********************/

    /**************** scopes Collection ********************/

    /**
     * Scope a collection of scopes for get all items.
     *
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  array $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAllItems(Builder $query, array $filter): Builder
    {
        return $query
            ->Id($filter)
            ->Role($filter)
            ->Status($filter);
    }

    /**
     * Scope a collection of scopes for get all items for using in controller list.
     *
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeControllerAllItems(Builder $query, array $filter): Builder
    {
        return $query->AllItems($filter)
            ->BetconstructId($filter)
            ->Login($filter)
            ->Email($filter)
            ->FirstName($filter)
            ->LastName($filter)
            ->Gender($filter)
            ->Phone($filter)
            ->MobilePhone($filter)
            ->BirthDate($filter)
            ->RegionCode($filter)
            ->IsTest($filter)
            ->CurrencyId($filter)
            ->Balance($filter)
            ->UnplayedBalance($filter)
            ->LastLoginIp($filter)
            ->LastLoginTimeStamp($filter)
            ->City($filter)
            ->CreatedStamp($filter)
            ->ModifiedStamp($filter)
            ->CustomPlayerCategory($filter)
            ->DepositCount($filter)
            ->ProvinceInternal($filter)
            ->CityInternal($filter)
            ->JobFieldInternal($filter)
            ->ContactNumbersInternal($filter)
            ->ContactMethodsInternal($filter)
            ->CallerGenderInternal($filter)
            ->IsEmailVerified($filter)
            ->IsProfileFurtherInfoCompleted($filter)
            ->Description($filter)

            ->LoyaltyLevelId($filter);
    }

    /**
     * Scope a collection of scopes for the "Controller->apiIndex" function.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  array $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApiIndexCollection(Builder $query, array $filter): Builder
    {

        $usersTable = Database::Users;
        $clientExtrasTable = Database::BetconstructClients;
        $clientSwarmExtrasTable = Database::BetconstructSwarmClients;
        $rolesTable = Database::Roles;

        return $query
            ->ControllerAllItems($filter)
            ->join($clientExtrasTable->tableName(), ClientModelEnum::UserId->dbNameWithTable($clientExtrasTable), '=', TableEnum::Id->dbNameWithTable($usersTable))
            ->leftJoin($clientSwarmExtrasTable->tableName(), ClientSwarmModelEnum::UserId->dbNameWithTable($clientSwarmExtrasTable), '=', TableEnum::Id->dbNameWithTable($usersTable))
            ->join($rolesTable->tableName(), RolesTableEnum::Id->dbNameWithTable($rolesTable), '=', TableEnum::RoleId->dbNameWithTable($usersTable))
            ->select(

                TableEnum::Id->dbNameWithTable($usersTable),
                TableEnum::RoleId->dbNameWithTable($usersTable),
                TableEnum::Username->dbNameWithTable($usersTable),
                TableEnum::Status->dbNameWithTable($usersTable),

                ClientModelEnum::Id->dbNameWithTable($clientExtrasTable) . ' as betconstruct_id',
                ClientModelEnum::Login->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::Email->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::FirstName->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::LastName->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::Gender->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::Phone->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::MobilePhone->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::BirthDateStamp->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::RegionCode->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::IsTest->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::CurrencyId->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::Balance->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::UnplayedBalance->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::LastLoginIp->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::LastLoginTimeStamp->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::City->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::CreatedStamp->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::ModifiedStamp->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::CustomPlayerCategory->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::DepositCount->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::ProvinceInternal->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::CityInternal->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::JobFieldInternal->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::ContactNumbersInternal->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::ContactMethodsInternal->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::CallerGenderInternal->dbNameWithTable($clientExtrasTable),
                ClientModelEnum::Descr->dbNameWithTable($clientExtrasTable),

                ClientSwarmModelEnum::LoyaltyLevelId->dbNameWithTable($clientSwarmExtrasTable),

                RolesTableEnum::Name->dbNameWithTable($rolesTable) . ' as client_category_name',
            )
            ->SortOrder($filter, [
                UsersTableEnum::RoleId->dbName() => 'client_category_name',
            ]);
    }

    /**************** scopes Collection END ********************/

    /**************** Relation scopes ********************/

    /**
     * Scope a collection of scopes for the "Active Clients".
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActiveClients(Builder $query): Builder
    {
        return $query->where(TableEnum::Status, UsersStatusEnum::Active->name);
    }

    /**************** Relation scopes END ********************/

    /**************** scopes ********************/

    /**
     * Scope a query to set SortOrder as request or defults.
     *
     * @param array $replaceSortFields
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOrder(Builder $query, ?array $filter = null, array $replaceSortFields = []): Builder
    {
        return $this->superScopeSortOrder($query, $filter, function ($query) {
            return $query
                ->orderBy(TableEnum::Username->dbName(), 'asc');
        }, $replaceSortFields);
    }

    /**
     * Scope a query to only include "id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeId(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeExactlyNumber(UsersTableEnum::Id->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "role" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRole(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeDropboxId(UsersTableEnum::RoleId->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "status" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeDropbox(UsersTableEnum::Status->dbName(), $query, $filter);
    }

    /**************** Joined Items ********************/

    /**
     * Scope a query to only include "betconstruct_id" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetconstructId(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'betconstruct_id';
        $dbCol = ClientModelEnum::Id->dbNameWithTable(Database::BetconstructClients);

        return $this->superScopeExactlyNumber($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "login" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLogin(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::Login->dbName();
        $dbCol = ClientModelEnum::Login->dbNameWithTable(Database::BetconstructClients);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "email" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEmail(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::Email->dbName();
        $dbCol = ClientModelEnum::Email->dbNameWithTable(Database::BetconstructClients);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "first_name" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFirstName(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::FirstName->dbName();
        $dbCol = ClientModelEnum::FirstName->dbNameWithTable(Database::BetconstructClients);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "last_name" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLastName(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::LastName->dbName();
        $dbCol = ClientModelEnum::LastName->dbNameWithTable(Database::BetconstructClients);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "gender" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGender(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::Gender->dbName();
        $dbCol = ClientModelEnum::Gender->dbNameWithTable(Database::BetconstructClients);

        return $this->superScopeDropboxId($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "phone" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePhone(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::Phone->dbName();
        $dbCol = ClientModelEnum::Phone->dbNameWithTable(Database::BetconstructClients);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "mobile_phone" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMobilePhone(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::MobilePhone->dbName();
        $dbCol = ClientModelEnum::MobilePhone->dbNameWithTable(Database::BetconstructClients);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "birth_date_stamp" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBirthDate(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::BirthDateStamp->dbName();
        $dbCol = ClientModelEnum::BirthDateStamp->dbNameWithTable(Database::BetconstructClients);

        return $this->superScopeDateRange($dbCol, $query, $filter, $filterKey, User::authUser()->getCalendarHelper());
    }

    /**
     * Scope a query to only include "region code" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRegionCode(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::RegionCode->dbName();
        $dbCol = ClientModelEnum::RegionCode->dbNameWithTable(Database::BetconstructClients);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "is_test" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsTest(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::IsTest->dbName();
        $dbCol = ClientModelEnum::IsTest->dbNameWithTable(Database::BetconstructClients);

        return $this->superScopeCheckbox($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "currency_id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrencyId(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::CurrencyId->dbName();
        $dbCol = ClientModelEnum::CurrencyId->dbNameWithTable(Database::BetconstructClients);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "balance" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBalance(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::Balance->dbName();
        $dbCol = ClientModelEnum::Balance->dbNameWithTable(Database::BetconstructClients);

        return $this->superScopeNumberRange($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "unplayed_balance" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnplayedBalance(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::UnplayedBalance->dbName();
        $dbCol = ClientModelEnum::UnplayedBalance->dbNameWithTable(Database::BetconstructClients);

        return $this->superScopeNumberRange($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "last_login_ip" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLastLoginIp(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::LastLoginIp->dbName();
        $dbCol = ClientModelEnum::LastLoginIp->dbNameWithTable(Database::BetconstructClients);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "last_login_time_stamp" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLastLoginTimeStamp(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::LastLoginTimeStamp->dbName();
        $dbCol = ClientModelEnum::LastLoginTimeStamp->dbNameWithTable(Database::BetconstructClients);

        return $this->superScopeDateRange($dbCol, $query, $filter, $filterKey, User::authUser()->getCalendarHelper());
    }

    /**
     * Scope a query to only include "city" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCity(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::City->dbName();
        $dbCol = ClientModelEnum::City->dbNameWithTable(Database::BetconstructClients);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "created_stamp" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedStamp(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::CreatedStamp->dbName();
        $dbCol = ClientModelEnum::CreatedStamp->dbNameWithTable(Database::BetconstructClients);

        return $this->superScopeDateRange($dbCol, $query, $filter, $filterKey, User::authUser()->getCalendarHelper());
    }

    /**
     * Scope a query to only include "modified_stamp" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeModifiedStamp(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::ModifiedStamp->dbName();
        $dbCol = ClientModelEnum::ModifiedStamp->dbNameWithTable(Database::BetconstructClients);

        return $this->superScopeDateRange($dbCol, $query, $filter, $filterKey, User::authUser()->getCalendarHelper());
    }

    /**
     * Scope a query to only include "custom_player_category" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCustomPlayerCategory(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::CustomPlayerCategory->dbName();
        $dbCol = ClientModelEnum::CustomPlayerCategory->dbNameWithTable(Database::BetconstructClients);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "deposit_count" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDepositCount(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::DepositCount->dbName();
        $dbCol = ClientModelEnum::DepositCount->dbNameWithTable(Database::BetconstructClients);

        return $this->superScopeNumberRange($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "province_internal" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProvinceInternal(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::ProvinceInternal->dbName();
        $dbCol = ClientModelEnum::ProvinceInternal->dbNameWithTable(Database::BetconstructClients);

        return $this->superScopeDropbox($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "city_internal" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCityInternal(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::CityInternal->dbName();
        $dbCol = ClientModelEnum::CityInternal->dbNameWithTable(Database::BetconstructClients);

        if (!Arr::has($filter, $filterKey))
            return $query;

        $provinceCol = ClientModelEnum::ProvinceInternal->dbName();

        $matchingItemsKeys = [];

        if (!Arr::has($filter, $provinceCol))
            $province = null;
        else if (empty($filter[$provinceCol]))
            $province = null;
        else
            $province = $filter[$provinceCol];

        $proviceCities = is_null($province) ? Arr::collapse(__('IranCities.Cities')) : __('IranCities.Cities.' . $province);

        if (is_array($proviceCities)) {

            $matchingItems = ArrayHelper::searchLikeAs($filter[$filterKey], $proviceCities, false);
            $matchingItemsKeys = array_keys($matchingItems);
        }

        return $this->superScopeArray($dbCol, $query, $filter, $matchingItemsKeys, false, $filterKey);
    }

    /**
     * Scope a query to only include "job_field_internal" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeJobFieldInternal(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::JobFieldInternal->dbName();
        $dbCol = ClientModelEnum::JobFieldInternal->dbNameWithTable(Database::BetconstructClients);

        return $this->superScopeDropbox($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "contact_numbers_internal" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeContactNumbersInternal(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::ContactNumbersInternal->dbName();
        $dbCol = ClientModelEnum::ContactNumbersInternal->dbNameWithTable(Database::BetconstructClients);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "contact_methods_internal" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeContactMethodsInternal(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::ContactMethodsInternal->dbName();
        $dbCol = ClientModelEnum::ContactMethodsInternal->dbNameWithTable(Database::BetconstructClients);

        if (!Arr::has($filter, $filterKey))
            return $query;

        $filterText = $filter[$filterKey];

        if (empty($filterText))
            return $query;

        $translatedArray = __('general.ContactMethodsEnum');

        $matchingItems = ArrayHelper::searchLikeAs($filterText, $translatedArray, false);
        $matchingItemsKeys = array_keys($matchingItems);

        if (empty($matchingItemsKeys))
            return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);

        return
            $query->where(function ($query) use ($dbCol, $matchingItemsKeys) {

                foreach ($matchingItemsKeys as $item) {

                    $item = '"' . $item . '"';
                    $query->orWhere($dbCol, 'like', '%' . $item . '%');
                }
            });
    }

    /**
     * Scope a query to only include "caller_gender_internal" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCallerGenderInternal(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::CallerGenderInternal->dbName();
        $dbCol = ClientModelEnum::CallerGenderInternal->dbNameWithTable(Database::BetconstructClients);

        if (!Arr::has($filter, $filterKey))
            return $query;

        $filterText = $filter[$filterKey];

        if (empty($filterText))
            return $query;

        $translatedArray = DropdownListCreater::makeByArray(AppGendersEnum::translatedArray())
            ->useReverseList()->get();

        $matchingItems = ArrayHelper::searchLikeAs($filterText, $translatedArray, true);
        $matchingItemsKeys = array_keys($matchingItems);

        if (empty($matchingItemsKeys))
            return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);

        return
            $query->where(function ($query) use ($dbCol, $matchingItemsKeys) {

                foreach ($matchingItemsKeys as $item) {

                    $item = '"' . $item . '"';
                    $query->orWhere($dbCol, 'like', '%' . $item . '%');
                }
            });
    }

    /**
     * Scope a query to only include "IsEmailVerified" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsEmailVerified(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = TableEnum::IsEmailVerified->dbName();

        $value = $this->getCheckboxInput($filter, $filterKey);

        if (is_null($value))
            return $query;

        return ClientProfileCheckEnum::LastEmailVerification->isCompletedSearchQuery($query, $value);
    }

    /**
     * Scope a query to only include "IsProfileFurtherInfoCompleted" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsProfileFurtherInfoCompleted(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'IsProfileFurtherInfoCompleted';

        $value = $this->getCheckboxInput($filter, $filterKey);

        if (is_null($value))
            return $query;

        return ClientProfileCheckEnum::FurtherInformationTab->isCompletedSearchQuery($query, $value);
    }

    /**
     * Scope a query to only include "descr" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDescription(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::Descr->dbName();
        $dbCol = ClientModelEnum::Descr->dbNameWithTable(Database::BetconstructClients);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "loyalty_level_id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLoyaltyLevelId(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientSwarmModelEnum::LoyaltyLevelId->dbName();
        $dbCol = ClientSwarmModelEnum::LoyaltyLevelId->dbNameWithTable(Database::BetconstructSwarmClients);

        return $this->superScopeExactly($dbCol, $query, $filter, $filterKey);
    }
    /**************** Joined Items END ********************/

    /**************** scopes END ********************/
}
