<?php

namespace App\Models\BackOffice\Domains;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\AssignedDomainsTableEnum as TableEnum;
use App\Enums\Database\Tables\ClientTrustScoresTableEnum;
use App\Enums\Database\Tables\DomainsTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\Models\SuperClasses\SuperModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignedDomain extends SuperModel
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
            TableEnum::DomainId->dbName(),
            TableEnum::ClientTrustScore->dbName(),
        ];

        $this->attributes = [
            TableEnum::Reported->dbName()       => 0,
            TableEnum::FakeAssigned->dbName()   => 0,
        ];

        $this->casts = [
            TableEnum::Reported->dbName()       => 'boolean',
            TableEnum::FakeAssigned->dbName()   => 'boolean',
        ];

        parent::__construct($attributes);
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get the user that owns the AssignedDomain
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, TableEnum::UserId->dbName(), UsersTableEnum::Id->dbName());
    }

    /**
     * Get the domain that owns the AssignedDomain
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class, TableEnum::DomainId->dbName(), DomainsTableEnum::Id->dbName());
    }
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/
    //
    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/

    /**
     * Convert string boolean to boolean when saving information.
     * When receiving information from the API client,
     * boolean values may be received in the form of strings.
     *
     * Example:
     *   "true" => true
     *   "false" => false
     *
     * @param string $value
     * @return void
     */
    public function setReportedAttribute($value)
    {
        $this->attributes[TableEnum::Reported->dbName()] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Convert string boolean to boolean when saving information.
     * When receiving information from the API client,
     * boolean values may be received in the form of strings.
     *
     * Example:
     *   "true" => true
     *   "false" => false
     *
     * @param string $value
     * @return void
     */
    public function setFakeAssignedAttribute($value)
    {
        $this->attributes[TableEnum::FakeAssigned->dbName()] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
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
            ->UserId($filter)
            ->ClientTrustScore($filter)
            ->DomainSuspiciousScore($filter)
            ->Reported($filter)
            ->FakeAssigned($filter)
            ->CreatedAt($filter)
            ->ReportedAt($filter);
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
            ->Username($filter)
            ->DomainName($filter)
            ->DomainStatus($filter)
            ->Public($filter)
            ->Suspicious($filter)
            ->AnnouncedAt($filter)
            ->BlockedAt($filter)
            ->CurrentTrustScore($filter)
            ->CurrentDomainSuspiciousScore($filter);
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
        $clientExtrasTable = DatabaseTablesEnum::BetconstructClients;
        $domainsTable = DatabaseTablesEnum::Domains;
        $clientTrustScoresTable = DatabaseTablesEnum::ClientTrustScores;

        return $query
            ->ControllerAllItems($filter)
            ->leftJoin($clientExtrasTable->tableName(), ClientModelEnum::UserId->dbNameWithTable($clientExtrasTable), '=', TableEnum::UserId->dbNameWithTable($thisTable))
            ->leftJoin($domainsTable->tableName(), DomainsTableEnum::Id->dbNameWithTable($domainsTable), '=', TableEnum::DomainId->dbNameWithTable($thisTable))
            ->leftJoin($clientTrustScoresTable->tableName(), ClientTrustScoresTableEnum::UserId->dbNameWithTable($clientTrustScoresTable), '=', TableEnum::UserId->dbNameWithTable($thisTable))
            ->select(
                $thisTable->tableName() . ".*",

                ClientModelEnum::Login->dbNameWithTable($clientExtrasTable) . " as username",

                DomainsTableEnum::Name->dbNameWithTable($domainsTable) . " as domain_name",
                DomainsTableEnum::Status->dbNameWithTable($domainsTable) . " as domain_status",
                DomainsTableEnum::Public->dbNameWithTable($domainsTable),
                DomainsTableEnum::Suspicious->dbNameWithTable($domainsTable),
                DomainsTableEnum::AnnouncedAt->dbNameWithTable($domainsTable),
                DomainsTableEnum::BlockedAt->dbNameWithTable($domainsTable),

                ClientTrustScoresTableEnum::Score->dbNameWithTable($clientTrustScoresTable) . ' as current_trust_score',
                ClientTrustScoresTableEnum::DomainSuspicious->dbNameWithTable($clientTrustScoresTable) . ' as current_domain_suspicious_score',
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
                ->orderBy('domain_status', 'asc')
                ->orderBy(TimestampsEnum::CreatedAt->dbName(), 'desc');
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
     * Scope a query to only include "trust_score" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeClientTrustScore(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = TableEnum::ClientTrustScore->dbName();
        $dbCol = TableEnum::ClientTrustScore->dbNameWithTable(DatabaseTablesEnum::AssignedDomains);

        return $this->superScopeNumberRange($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "domain_suspicious_score" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDomainSuspiciousScore(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = TableEnum::DomainSuspiciousScore->dbName();
        $dbCol = TableEnum::DomainSuspiciousScore->dbNameWithTable(DatabaseTablesEnum::AssignedDomains);

        return $this->superScopeNumberRange($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "reported" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReported(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeCheckbox(TableEnum::Reported->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "fake_assigned" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFakeAssigned(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeCheckbox(TableEnum::FakeAssigned->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "created_at" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedAt(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeDateRange(TimestampsEnum::CreatedAt->dbName(), $query, $filter, null, User::authUser()->getCalendarHelper());
    }

    /**
     * Scope a query to only include "reported_at" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReportedAt(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeDateRange(TableEnum::ReportedAt->dbName(), $query, $filter, null, User::authUser()->getCalendarHelper());
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
     * Scope a query to only include "domain_name" as request.
     * login is username in betconstruct model
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

    /**
     * Scope a query to only include "DomainStatus" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDomainStatus(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'domain_status';
        $dbCol = DomainsTableEnum::Status->dbNameWithTable(DatabaseTablesEnum::Domains);

        return $this->superScopeExactly($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "public" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublic(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = DomainsTableEnum::Public->dbName();
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
    public function scopeSuspicious(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = DomainsTableEnum::Suspicious->dbName();
        $dbCol = DomainsTableEnum::Suspicious->dbNameWithTable(DatabaseTablesEnum::Domains);

        return $this->superScopeCheckbox($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "announced_at" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAnnouncedAt(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = DomainsTableEnum::AnnouncedAt->dbName();
        $dbCol = DomainsTableEnum::AnnouncedAt->dbNameWithTable(DatabaseTablesEnum::Domains);

        return $this->superScopeDateRange($dbCol, $query, $filter, $filterKey, User::authUser()->getCalendarHelper());
    }

    /**
     * Scope a query to only include "blocked_at" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBlockedAt(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = DomainsTableEnum::BlockedAt->dbName();
        $dbCol = DomainsTableEnum::BlockedAt->dbNameWithTable(DatabaseTablesEnum::Domains);

        return $this->superScopeDateRange($dbCol, $query, $filter, $filterKey, User::authUser()->getCalendarHelper());
    }

    /**
     * Scope a query to only include "current_trust_score" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrentTrustScore(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'current_trust_score';
        $dbCol = ClientTrustScoresTableEnum::Score->dbNameWithTable(DatabaseTablesEnum::ClientTrustScores);

        return $this->superScopeNumberRange($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "current_domain_suspicious_score" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrentDomainSuspiciousScore(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'current_domain_suspicious_score';
        $dbCol = ClientTrustScoresTableEnum::DomainSuspicious->dbNameWithTable(DatabaseTablesEnum::ClientTrustScores);

        return $this->superScopeNumberRange($dbCol, $query, $filter, $filterKey);
    }
    /**************** scopes END ********************/
}
