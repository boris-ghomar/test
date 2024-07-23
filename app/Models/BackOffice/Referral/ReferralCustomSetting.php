<?php

namespace App\Models\BackOffice\Referral;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ReferralCustomSettingsTableEnum as TableEnum;
use App\Enums\Database\Tables\ReferralRewardPackagesTableEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\Models\SuperClasses\SuperModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralCustomSetting extends SuperModel
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
            TableEnum::UserId->dbName(),
            TableEnum::PackageId->dbName(),
            TableEnum::PrivateNote->dbName(),
        ];

        parent::__construct($attributes);
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get the referralRewardPackage that owns the ReferralSession
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referralRewardPackage(): BelongsTo
    {
        return $this->belongsTo(ReferralRewardPackage::class, TableEnum::PackageId->dbName(), ReferralRewardPackagesTableEnum::Id->dbName());
    }
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/
    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/
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
            ->PackageId($filter)
            ->PrivateNote($filter);
    }

    /**
     * Scope a collection of scopes for get all items for controller list.
     *
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeControllerAllItems(Builder $query, array $filter): Builder
    {
        return $query
            ->AllItems($filter)
            ->BcUsername($filter)
            ->BcId($filter);
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
        $thisTable = DatabaseTablesEnum::ReferralCustomSettings;
        $betconstructClientsTable = DatabaseTablesEnum::BetconstructClients;

        return $query
            ->ControllerAllItems($filter)
            ->join($betconstructClientsTable->tableName(), ClientModelEnum::UserId->dbNameWithTable($betconstructClientsTable), '=', TableEnum::UserId->dbNameWithTable($thisTable))
            ->select(

                $thisTable->dbName() . '.*',

                ClientModelEnum::Id->dbNameWithTable($betconstructClientsTable) . ' as bc_id',
                ClientModelEnum::Login->dbNameWithTable($betconstructClientsTable) . ' as bc_username',

            )
            ->SortOrder($filter, []);
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
                ->orderBy(TableEnum::PackageId->dbName(), 'asc')
                ->orderBy(TableEnum::UserId->dbName(), 'asc');
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
     * Scope a query to only include "package_id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePackageId(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeDropboxId(TableEnum::PackageId->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "private_note" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePrivateNote(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeLikeAs(TableEnum::PrivateNote->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "bc_username" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBcUsername(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'bc_username';
        $dbCol = ClientModelEnum::Login->dbNameWithTable(DatabaseTablesEnum::BetconstructClients);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "bc_id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBcId(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'bc_id';
        $dbCol = ClientModelEnum::Id->dbNameWithTable(DatabaseTablesEnum::BetconstructClients);

        return $this->superScopeExactly($dbCol, $query, $filter, $filterKey);
    }
    /**************** scopes END ********************/
}
