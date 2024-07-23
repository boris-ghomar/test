<?php

namespace App\Models\BackOffice\Domains;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\DedicatedDomainsTableEnum as TableEnum;
use App\Enums\Database\Tables\DomainsTableEnum;
use App\Models\SuperClasses\SuperModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DedicatedDomain extends SuperModel
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
            TableEnum::Name->dbName(),
            TableEnum::Descr->dbName(),
        ];

        parent::__construct($attributes);
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/
    /**
     * Get the domain that owns the DedicatedDomain
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class, TableEnum::DomainId->dbName(), DomainsTableEnum::Id->dbName());
    }
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
            ->Name($filter)
            ->Description($filter);
    }

    /**
     * Scope a collection of scopes for get all items for controller list.
     *
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  array $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeControllerAllItems(Builder $query, array $filter): Builder
    {
        return $query
            ->AllItems($filter)
            ->DomainName($filter);
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
        $thisTable = DatabaseTablesEnum::DedicatedDomains;
        $domainsTable = DatabaseTablesEnum::Domains;

        return $query
            ->ControllerAllItems($filter)
            ->leftJoin($domainsTable->tableName(), DomainsTableEnum::Id->dbNameWithTable($domainsTable), '=', TableEnum::DomainId->dbNameWithTable($thisTable))
            ->select(
                $thisTable->tableName() . '.*',

                DomainsTableEnum::Name->dbNameWithTable($domainsTable) . ' as domain_name',
            )
            ->SortOrder($filter, [
                TableEnum::DomainId->dbName() => "domain_name",
            ]);
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
                ->orderBy(TableEnum::Name->dbName(), 'asc')
                ->orderBy(TableEnum::DomainId->dbName(), 'asc');
        }, $replaceSortFields);
    }


    /**
     * Scope a query to only include "name" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeName(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeLikeAs(TableEnum::Name->dbName(), $query, $filter);
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
     * Scope a query to only include "domain_holder_url" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDomainName(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'domain_name';
        $dbCol = DomainsTableEnum::Name->dbNameWithTable(DatabaseTablesEnum::Domains);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }
    /**************** scopes END ********************/
}
