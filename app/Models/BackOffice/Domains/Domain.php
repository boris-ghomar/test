<?php

namespace App\Models\BackOffice\Domains;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\AssignedDomainsTableEnum;
use App\Enums\Database\Tables\DomainCategoriesTableEnum;
use App\Enums\Database\Tables\DomainHolderAccountsTableEnum;
use App\Enums\Database\Tables\DomainHoldersTableEnum;
use App\Enums\Database\Tables\DomainsTableEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Domains\DomainStatusEnum;
use App\HHH_Library\ThisApp\Packages\Client\TrustScore\ClientTrustScoreEngine;
use App\Models\SuperClasses\SuperModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Domain extends SuperModel
{
    use SoftDeletes;

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
            TableEnum::DomainCategoryId->dbName(),
            TableEnum::DomainHolderAccountId->dbName(),
            TableEnum::AutoRenew->dbName(),
            TableEnum::Status->dbName(),
            TableEnum::Public->dbName(),
            TableEnum::Suspicious->dbName(),
            TableEnum::Reported->dbName(),
            TableEnum::Descr->dbName(),
            TableEnum::RegisteredAt->dbName(),
            TableEnum::ExpiresAt->dbName(),
            TableEnum::AnnouncedAt->dbName(),
            TableEnum::BlockedAt->dbName(),
        ];

        $this->attributes = [
            TableEnum::AutoRenew->dbName()  => 1,
            TableEnum::Status->dbName()     => DomainStatusEnum::Unknown->name,
            TableEnum::Public->dbName()     => 0,
            TableEnum::Suspicious->dbName() => 0,
            TableEnum::Reported->dbName()   => 0,
        ];

        $this->casts = [
            TableEnum::AutoRenew->dbName()  => 'boolean',
            TableEnum::Public->dbName()     => 'boolean',
            TableEnum::Suspicious->dbName() => 'boolean',
            TableEnum::Reported->dbName()   => 'boolean',
        ];

        parent::__construct($attributes);
    }

    /**
     * @override parent boot
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::softDeleted(function (self $model) {

            // Rename the soft deleted item
            $nameCol = TableEnum::Name->dbName();

            $deletedName = sprintf("%s[deleted-%s]", $model->$nameCol, $model->id);

            $model[$nameCol] = $deletedName;
            $model->save();

            return $model;
        });

        self::saving(function (self $model) {

            $modelAttributes = $model->getAttributes();

            //  Default register date
            $registeredAtCol = TableEnum::RegisteredAt->dbName();
            $registeredAt = $model->$registeredAtCol;

            if (empty($registeredAt)) {
                $registeredAt = Carbon::now();
                $modelAttributes[$registeredAtCol] = $registeredAt;
            }

            //  Default expire date
            $expiresAtCol = TableEnum::ExpiresAt->dbName();
            $expiresAt = $model->$expiresAtCol;

            if (empty($expiresAt)) {
                $registerDate = $model->getRawOriginal($registeredAtCol);

                if (empty($registerDate))
                    $registerDate = $modelAttributes[$registeredAtCol];

                $expiresAt = Carbon::parse($registerDate)->addYear();

                $modelAttributes[$expiresAtCol] = $expiresAt;
            }
            // setRawAttributes used to ignore model mutators for set dates
            $model->setRawAttributes($modelAttributes);

            return $model;
        });

        self::updating(function (self $model) {

            // Check if domain blocked
            $statusCol = TableEnum::Status->dbName();

            if ($model->isDirty($statusCol)) {

                if ($model->$statusCol == DomainStatusEnum::Blocked->name) {

                    ClientTrustScoreEngine::domainBlocked($model);
                    $model[TableEnum::BlockedAt->dbName()] = Carbon::now();
                }
            }

            return $model;
        });
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get the 'DomainHolderAccount' that own the 'Domain'
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function domainCategory(): BelongsTo
    {
        return $this->belongsTo(DomainCategory::class, TableEnum::DomainCategoryId->dbName(), DomainCategoriesTableEnum::Id->dbName());
    }

    /**
     * Get the 'DomainHolderAccount' that own the 'Domain'
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function domainHolderAccount(): BelongsTo
    {
        return $this->belongsTo(DomainHolderAccount::class, TableEnum::DomainHolderAccountId->dbName(), DomainHolderAccountsTableEnum::Id->dbName());
    }

    /**
     * Get all of the "assigned users" for the Domain
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function assignedUsers(): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class,
            AssignedDomain::class,
            AssignedDomainsTableEnum::DomainId->dbName(),
            UsersTableEnum::Id->dbName(),
            null,
            AssignedDomainsTableEnum::UserId->dbName(),
        );
    }
    /**************** Relationships END ********************/

    /**************** Exclusive Items ********************/

    /**
     * Get status enum case
     *
     * @return \App\Enums\Domains\DomainStatusEnum|null
     */
    public function statusEnum(): ?DomainStatusEnum
    {
        return DomainStatusEnum::getCase($this[TableEnum::Status->dbName()]);
    }

    /**
     * Check if the domain is usable
     *
     * @param  \App\Models\BackOffice\Domains\Domain $domain
     * @return bool
     */
    public function isDomainUsable(): bool
    {
        $res = false;

        $expiresAtCol = TableEnum::ExpiresAt->dbName();

        if ($this->$expiresAtCol > Carbon::now()) {

            $statusCase = $this->statusEnum();

            if (!is_null($statusCase))
                $res = $statusCase->isUseable();
        } else {

            $domain = Domain::find($this->id);

            if ($domain[TableEnum::AutoRenew->dbName()]) {

                $domain->$expiresAtCol = Carbon::parse($domain->$expiresAtCol)->addYear();
                $this->$expiresAtCol = $domain->$expiresAtCol;
                $res = true;
            } else {

                $statusCol = TableEnum::Status->dbName();

                $domain->$statusCol = DomainStatusEnum::Expired->name;
                $this->$statusCol = $domain->$statusCol;
            }

            $domain->save();
        }

        return $res;
    }

    /**
     * Get convertable dates
     *
     * Because these dates are used by the system and modified by the user,
     *  they are converted only when needed.
     */
    private  static function getConvertableDates(): array
    {

        return [
            TableEnum::RegisteredAt->dbName(),
            TableEnum::ExpiresAt->dbName(),
            TableEnum::AnnouncedAt->dbName(),
            TableEnum::BlockedAt->dbName(),
        ];
    }

    /**
     * Convert user input local dates to UTC date
     */
    public function converDatesToUTC()
    {
        $user = User::authUser();

        $dates = self::getConvertableDates();

        foreach ($dates as $date) {
            $this->$date = $user->convertLocalTimeToUTC($this->$date);
        }
    }
    /**************** Exclusive Items END ********************/

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
    public function setAutoRenewAttribute($value)
    {
        $this->attributes[TableEnum::AutoRenew->dbName()] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
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
    public function setPublicAttribute($value)
    {
        $this->attributes[TableEnum::Public->dbName()] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
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
    public function setSuspiciousAttribute($value)
    {
        $this->attributes[TableEnum::Suspicious->dbName()] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
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
    public function setReportedAttribute($value)
    {
        $this->attributes[TableEnum::Reported->dbName()] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**************** Accessors & Mutators END ********************/

    /**************** scopes Collection ********************/

    /**
     * Scope all items
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  array $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAllItems(Builder $query, array $filter): Builder
    {
        return $query
            ->Name($filter)
            ->DomainCategoryId($filter)
            ->DomainHolderAccountId($filter)
            ->AutoRenew($filter)
            ->Status($filter)
            ->Public($filter)
            ->Suspicious($filter)
            ->Reported($filter)
            ->Description($filter)
            ->RegisteredAt($filter)
            ->ExpiresAt($filter)
            ->AnnouncedAt($filter)
            ->BlockedAt($filter);
    }

    /**
     * Scope a collection of scopes for get all items for controller list.
     *
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param array $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeControllerAllItems(Builder $query, array $filter): Builder
    {
        return $query
            ->AllItems($filter)
            ->DomainHolder($filter);
    }

    /**
     * Scope a collection of scopes for the "Controller->apiIndex" function.
     * The main offices that are not a subset of any other office.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  array $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApiIndexCollection(Builder $query, array $filter): Builder
    {
        $thisTable = DatabaseTablesEnum::Domains;
        $domainCategoriesTable = DatabaseTablesEnum::DomainCategories;
        $domainHolderAccountsTable = DatabaseTablesEnum::DomainHolderAccounts;
        $domainHoldersTable = DatabaseTablesEnum::DomainHolders;

        return $query
            ->ControllerAllItems($filter)
            ->leftJoin($domainCategoriesTable->tableName(), DomainCategoriesTableEnum::Id->dbNameWithTable($domainCategoriesTable), '=', TableEnum::DomainCategoryId->dbNameWithTable($thisTable))
            ->leftJoin($domainHolderAccountsTable->tableName(), DomainHolderAccountsTableEnum::Id->dbNameWithTable($domainHolderAccountsTable), '=', TableEnum::DomainHolderAccountId->dbNameWithTable($thisTable))
            ->leftJoin($domainHoldersTable->tableName(), DomainHoldersTableEnum::Id->dbNameWithTable($domainHoldersTable), '=', DomainHolderAccountsTableEnum::DomainHolderId->dbNameWithTable($domainHolderAccountsTable))
            ->where(TimestampsEnum::DeletedAt->dbNameWithTable($domainCategoriesTable), null)
            ->where(TimestampsEnum::DeletedAt->dbNameWithTable($domainHolderAccountsTable), null)
            ->where(TimestampsEnum::DeletedAt->dbNameWithTable($domainHoldersTable), null)
            ->select(
                $thisTable->tableName() . '.*',

                DomainCategoriesTableEnum::Name->dbNameWithTable($domainCategoriesTable) . ' as domain_category_name',

                DomainHolderAccountsTableEnum::Username->dbNameWithTable($domainHolderAccountsTable) . ' as domain_holder_account_username',

                DomainHoldersTableEnum::Id->dbNameWithTable($domainHoldersTable) . ' as domain_holder_id',
                DomainHoldersTableEnum::Name->dbNameWithTable($domainHoldersTable) . ' as domain_holder_name',
            )
            ->SortOrder($filter, [
                TableEnum::DomainCategoryId->dbName() => "domain_category_name",
                TableEnum::DomainHolderAccountId->dbName() => "domain_holder_account_username",
                'domain_holder_id' => "domain_holder_name",
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
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOrder(Builder $query, ?array $filter = null, array $replaceSortFields = []): Builder
    {
        return $this->superScopeSortOrder($query, $filter, function ($query) {
            return $query
                ->orderBy(TableEnum::Status->dbName(), 'asc')
                ->orderBy(TableEnum::ExpiresAt->dbName(), 'asc')
                ->orderBy(TableEnum::BlockedAt->dbName(), 'asc')
                ->orderBy(TableEnum::RegisteredAt->dbName(), 'desc')
                ->orderBy(TableEnum::Name->dbName(), 'asc');
        }, $replaceSortFields);
    }

    /**
     * Scope a query to only include "name" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeName(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeLikeAs(TableEnum::Name->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "domain_category_id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDomainCategoryId(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeDropboxId(TableEnum::DomainCategoryId->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "domain_holder_account_id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDomainHolderAccountId(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeDropboxId(TableEnum::DomainHolderAccountId->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "auto_renew" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAutoRenew(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeCheckbox(TableEnum::AutoRenew->dbName(), $query, $filter);
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
        return $this->superScopeDropbox(TableEnum::Status->dbName(), $query, $filter);
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
        return $this->superScopeCheckbox(TableEnum::Public->dbName(), $query, $filter);
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
        return $this->superScopeCheckbox(TableEnum::Suspicious->dbName(), $query, $filter);
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
     * Scope a query to only include "descr" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDescription(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeLikeAs(TableEnum::Descr->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "registered_at" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRegisteredAt(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeDateRange(TableEnum::RegisteredAt->dbName(), $query, $filter, null, User::authUser()->getCalendarHelper());
    }

    /**
     * Scope a query to only include "expires_at" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpiresAt(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeDateRange(TableEnum::ExpiresAt->dbName(), $query, $filter, null, User::authUser()->getCalendarHelper());
    }

    /**
     * Scope a query to only include "announced_at" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAnnouncedAt(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeDateRange(TableEnum::AnnouncedAt->dbName(), $query, $filter, null, User::authUser()->getCalendarHelper());
    }

    /**
     * Scope a query to only include "blocked_at" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBlockedAt(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeDateRange(TableEnum::BlockedAt->dbName(), $query, $filter, null, User::authUser()->getCalendarHelper());
    }

    /**
     * Scope a query to only include "domain_holder_url" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDomainHolder(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'domain_holder_id';
        $dbCol = DomainHoldersTableEnum::Id->dbNameWithTable(DatabaseTablesEnum::DomainHolders);

        return $this->superScopeDropboxId($dbCol, $query, $filter, $filterKey);
    }
    /**************** scopes END ********************/
}
