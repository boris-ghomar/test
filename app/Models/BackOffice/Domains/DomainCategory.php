<?php

namespace App\Models\BackOffice\Domains;

use App\Enums\Database\Tables\DomainCategoriesTableEnum as TableEnum;
use App\Enums\Database\Tables\DomainsTableEnum;
use App\Models\SuperClasses\SuperModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DomainCategory extends SuperModel
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
            TableEnum::DomainAssignment->dbName(),
            TableEnum::IsActive->dbName(),
            TableEnum::Descr->dbName(),
        ];

        $this->attributes = [
            TableEnum::DomainAssignment->dbName() => 0,
            TableEnum::IsActive->dbName() => 0,
        ];

        $this->casts = [
            TableEnum::DomainAssignment->dbName() => 'boolean',
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
            $nameCol = TableEnum::Name->dbName();

            $deletedName = sprintf("%s[deleted-%s]", $model->$nameCol, $model->id);

            $model[$nameCol] = $deletedName;
            $model->save();

            return $model;
        });
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/
    /**
     * Get all of the domains for the DomainCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class, DomainsTableEnum::DomainCategoryId->dbName(), TableEnum::Id->dbName());
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
    public function setDomainAssignmentAttribute($value)
    {
        $this->attributes[TableEnum::DomainAssignment->dbName()] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
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
    public function setIsActiveAttribute($value)
    {
        $this->attributes[TableEnum::IsActive->dbName()] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
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
            ->DomainAssignment($filter)
            ->IsActive($filter)
            ->Description($filter);
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
        return $query->AllItems($filter)
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
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOrder(Builder $query, ?array $filter = null, array $replaceSortFields = []): Builder
    {
        return $this->superScopeSortOrder($query, $filter, function ($query) {
            return $query
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
     * Scope a query to only include "domain_assignment" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDomainAssignment(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeCheckbox(TableEnum::DomainAssignment->dbName(), $query, $filter);
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
    /**************** scopes END ********************/
}
