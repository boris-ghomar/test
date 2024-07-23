<?php

namespace App\Models\BackOffice\Domains;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\DomainHolderAccountsTableEnum as TableEnum;
use App\Enums\Database\Tables\DomainHoldersTableEnum;
use App\Enums\Database\Tables\DomainsTableEnum;
use App\Models\SuperClasses\SuperModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DomainHolderAccount extends SuperModel
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
            TableEnum::DomainHolderId->dbName(),
            TableEnum::Username->dbName(),
            TableEnum::Email->dbName(),
            TableEnum::IsActive->dbName(),
            TableEnum::Descr->dbName(),
        ];

        $this->attributes = [
            TableEnum::IsActive->dbName() => 1,
        ];

        $this->casts = [
            TableEnum::IsActive->dbName() => 'boolean',
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
            $usernameCol = TableEnum::Username->dbName();

            $deletedName = sprintf("%s[deleted-%s]", $model->$usernameCol, $model->id);

            $model[$usernameCol] = $deletedName;
            $model->save();

            return $model;
        });
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get the 'DomainHolder' that own the 'DomainHolderAccount'
     *
     * @return \App\Models\User $user
     */
    public function domainHolder(): BelongsTo
    {
        return $this->belongsTo(DomainHolder::class, TableEnum::DomainHolderId->dbName(), DomainHoldersTableEnum::Id->dbName());
    }

    /**
     * Get all of the domains for the DomainHolderAccount
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class, DomainsTableEnum::DomainHolderAccountId->dbName(), TableEnum::Id->dbName());
    }
    /**************** Relationships END ********************/

    /**************** Exclusive Items ********************/
    //
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
    public function setIsActiveAttribute($value)
    {
        $this->attributes[TableEnum::IsActive->dbName()] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**************** Accessors & Mutators END ********************/

    /**************** scopes Collection ********************/

    /**
     * Scope all items
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param array $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAllItems(Builder $query, array $filter): Builder
    {
        return $query
            ->DomainHolderId($filter)
            ->Username($filter)
            ->Email($filter)
            ->IsActive($filter)
            ->Description($filter);
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
            ->DomainHolderUrl($filter);
    }

    /**
     * Scope a collection of scopes for the "DomainHolderAccountController->apiIndex" function.
     * The main offices that are not a subset of any other office.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeApiIndexCollection(Builder $query, array $filter): Builder
    {
        $thisTable = DatabaseTablesEnum::DomainHolderAccounts;
        $domainHoldersTable = DatabaseTablesEnum::DomainHolders;

        return $query
            ->ControllerAllItems($filter)
            ->join($domainHoldersTable->tableName(), DomainHoldersTableEnum::Id->dbNameWithTable($domainHoldersTable), '=', TableEnum::DomainHolderId->dbNameWithTable($thisTable))
            ->select(
                $thisTable->tableName() . '.*',

                DomainHoldersTableEnum::Name->dbNameWithTable($domainHoldersTable) . ' as domain_holder_name',
                DomainHoldersTableEnum::Url->dbNameWithTable($domainHoldersTable) . ' as domain_holder_url',

            )
            ->SortOrder($filter, [
                TableEnum::DomainHolderId->dbName() => "domain_holder_name",
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
                ->orderBy(TableEnum::DomainHolderId->dbName(), 'asc');
        }, $replaceSortFields);
    }

    /**
     * Scope a query to only include "domain_holder_id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDomainHolderId(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeDropboxId(TableEnum::DomainHolderId->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "username" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsername(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeLikeAs(TableEnum::Username->dbName(), $query, $filter);
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
        return $this->superScopeLikeAs(TableEnum::Email->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "is_active" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsActive(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeCheckbox(TableEnum::IsActive->dbName(), $query, $filter);
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
     * Scope a query to only include "domain_holder_url" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDomainHolderUrl(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'domain_holder_url';
        $dbCol = DomainHoldersTableEnum::Url->dbNameWithTable(DatabaseTablesEnum::DomainHolders);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }
    /**************** scopes END ********************/
}
