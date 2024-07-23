<?php

namespace App\Models\BackOffice\Domains;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\AssignedDomainsTableEnum as TableEnum;
use App\Enums\Database\Tables\DomainsTableEnum;
use Illuminate\Database\Eloquent\Builder;

class AssignedDomainsStatistic extends AssignedDomain
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
        $this->table = DatabaseTablesEnum::AssignedDomains->tableName();

        $this->fillable = [];

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
            ->ClientTrustScore($filter)
            ->FakeAssigned($filter);
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
            ->DomainName($filter)
            ->DomainStatus($filter)
            ->PublicDomain($filter)
            ->SuspiciousDomain($filter)
            ->ReportedDomain($filter)
            ->AnnouncedAt($filter)
            ->BlockedAt($filter);
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
        $thisTable = DatabaseTablesEnum::AssignedDomains;
        $domainsTable = DatabaseTablesEnum::Domains;

        return $query
            ->ControllerAllItems($filter)
            ->leftJoin($domainsTable->tableName(), DomainsTableEnum::Id->dbNameWithTable($domainsTable), '=', TableEnum::DomainId->dbNameWithTable($thisTable))
            ->select(
                $thisTable->tableName() . ".*",

                DomainsTableEnum::Name->dbNameWithTable($domainsTable) . " as domain_name",
                DomainsTableEnum::Status->dbNameWithTable($domainsTable) . " as domain_status",
                DomainsTableEnum::Public->dbNameWithTable($domainsTable) . " as domain_public",
                DomainsTableEnum::Suspicious->dbNameWithTable($domainsTable) . " as domain_suspicious",
                DomainsTableEnum::Reported->dbNameWithTable($domainsTable) . " as domain_reported",
                DomainsTableEnum::AnnouncedAt->dbNameWithTable($domainsTable),
                DomainsTableEnum::BlockedAt->dbNameWithTable($domainsTable),
            )
            ->groupBy(TableEnum::DomainId->dbNameWithTable($thisTable), TableEnum::FakeAssigned->dbNameWithTable($thisTable))
            ->distinct()
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
                ->orderBy(TableEnum::ClientTrustScore->dbName(), 'asc');
        }, $replaceSortFields);
    }

    /**
     * Scope a query to only include "public" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublicDomain(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'domain_public';
        $dbCol = DomainsTableEnum::Public->dbNameWithTable(DatabaseTablesEnum::Domains);

        return $this->superScopeCheckbox($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "suspicious" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSuspiciousDomain(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'domain_suspicious';
        $dbCol = DomainsTableEnum::Suspicious->dbNameWithTable(DatabaseTablesEnum::Domains);

        return $this->superScopeCheckbox($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "reported" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReportedDomain(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'domain_reported';
        $dbCol = DomainsTableEnum::Reported->dbNameWithTable(DatabaseTablesEnum::Domains);

        return $this->superScopeCheckbox($dbCol, $query, $filter, $filterKey);
    }

    /**************** scopes END ********************/
}
