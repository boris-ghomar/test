<?php

namespace App\Models\BackOffice\Domains;

use App\Enums\Database\Tables\DomainExtensionsTableEnum as TableEnum;
use App\Models\SuperClasses\SuperModel;
use Illuminate\Database\Eloquent\Builder;

class DomainExtension extends SuperModel
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
            TableEnum::LimitedOrder->dbName(),
            TableEnum::IsActive->dbName(),
            TableEnum::Descr->dbName(),
        ];

        $this->attributes = [
            TableEnum::LimitedOrder->dbName() => 0,
            TableEnum::IsActive->dbName() => 1,
        ];

        $this->casts = [
            TableEnum::LimitedOrder->dbName() => 'boolean',
            TableEnum::IsActive->dbName() => 'boolean',
        ];

        parent::__construct($attributes);
    }

    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/
    //
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
    public function setLimitedOrderAttribute($value)
    {
        $this->attributes[TableEnum::LimitedOrder->dbName()] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
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
            ->LimitedOrder($filter)
            ->IsActive($filter)
            ->Description($filter);
    }

    /**
     * Scope a collection of scopes for the "PaymentApiController->apiIndex" function.
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
     * Scope a query to only include "limited_order" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLimitedOrder(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeCheckbox(TableEnum::LimitedOrder->dbName(), $query, $filter);
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
