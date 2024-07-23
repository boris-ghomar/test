<?php

namespace App\Models\BackOffice\ClientsManagement;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ClientTrustScoresTableEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\Models\SuperClasses\SuperModel;
use Illuminate\Database\Eloquent\Builder;

class ClientTrustScore extends SuperModel
{

    /**************** Parnet Items ********************/

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->fillable = [
            TableEnum::Score->dbName(),
            TableEnum::DomainSuspicious->dbName(),
            TableEnum::Descr->dbName(),
        ];

        $this->attributes = [
            TableEnum::DomainSuspicious->dbName()   => 0,
        ];

        parent::__construct($attributes);
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/
    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/
    //
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
            ->UserId($filter)
            ->Score($filter)
            ->DomainSuspicious($filter)
            ->DepositCount($filter)
            ->Balance($filter)
            ->Description($filter);
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
            ->Role($filter)
            ->Username($filter)
            ->CurrencyId($filter)
            ->BetconstructId($filter);
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
        $thisTable = DatabaseTablesEnum::ClientTrustScores;
        $clientExtrasTable = DatabaseTablesEnum::BetconstructClients;
        $usersTable = DatabaseTablesEnum::Users;

        return $query
            ->ControllerAllItems($filter)
            ->leftJoin($usersTable->tableName(), UsersTableEnum::Id->dbNameWithTable($usersTable), '=', TableEnum::UserId->dbNameWithTable($thisTable))
            ->leftJoin($clientExtrasTable->tableName(), ClientModelEnum::UserId->dbNameWithTable($clientExtrasTable), '=', TableEnum::UserId->dbNameWithTable($thisTable))
            ->select(
                $thisTable->tableName() . ".*",

                UsersTableEnum::RoleId->dbNameWithTable($usersTable),

                ClientModelEnum::Id->dbNameWithTable($clientExtrasTable) . " as betconstruct_id",
                ClientModelEnum::Login->dbNameWithTable($clientExtrasTable) . " as username",
                ClientModelEnum::CurrencyId->dbNameWithTable($clientExtrasTable),
            )
            ->SortOrder($filter);
    }
    /**************** scopes Collection END ********************/

    /**************** scopes ********************/

    /**
     * Scope a query to set SortOrder as request or defults.
     *
     * @param array $replaceSortFields
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @param  array $replaceSortFields
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOrder(Builder $query, array $filter = null, array $replaceSortFields = []): Builder
    {
        return $this->superScopeSortOrder($query, $filter, function ($query) {
            return $query
                ->orderBy(TableEnum::Score->dbName(), 'desc');
        }, $replaceSortFields);
    }

    /**
     * Scope a query to only include "user_id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUserId(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeExactlyNumber(TableEnum::UserId->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "score" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeScore(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeNumberRange(TableEnum::Score->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "domain_suspicious" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDomainSuspicious(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeNumberRange(TableEnum::DomainSuspicious->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "deposit_count" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDepositCount(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeNumberRange(TableEnum::DepositCount->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "balance" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBalance(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeNumberRange(TableEnum::Balance->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "descr" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDescription(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeLikeAs(TableEnum::Descr->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "role_id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRole(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = UsersTableEnum::RoleId->dbName();
        $dbCol = UsersTableEnum::RoleId->dbNameWithTable(DatabaseTablesEnum::Users);

        return $this->superScopeDropboxId($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "betconstruct_id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetconstructId(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'betconstruct_id';
        $dbCol = ClientModelEnum::Id->dbNameWithTable(DatabaseTablesEnum::BetconstructClients);

        return $this->superScopeExactlyNumber($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "username" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsername(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'username';
        $dbCol = ClientModelEnum::Login->dbNameWithTable(DatabaseTablesEnum::BetconstructClients);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "currency_id" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrencyId(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::CurrencyId->dbName();
        $dbCol = ClientModelEnum::CurrencyId->dbNameWithTable(DatabaseTablesEnum::BetconstructClients);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }
    /**************** scopes END ********************/
}
