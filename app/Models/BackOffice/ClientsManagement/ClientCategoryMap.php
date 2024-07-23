<?php

namespace App\Models\BackOffice\ClientsManagement;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ClientCategoryMapsTableEnum as TableEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\Models\SuperClasses\SuperModel;
use Illuminate\Database\Eloquent\Builder;

class ClientCategoryMap extends SuperModel
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
        parent::__construct($attributes);

        $this->fillable = [
            TableEnum::RoleId->dbName(),
            TableEnum::MapType->dbName(),
            TableEnum::ItemValue->dbName(),
            TableEnum::Priority->dbName(),
            TableEnum::IsActive->dbName(),
            TableEnum::Descr->dbName(),
        ];
    }

    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/
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
     * @param mixed $value
     * @return void
     */
    public function setIsActiveAttribute(mixed $value): void
    {
        $this->attributes[TableEnum::IsActive->dbName()] = CastEnum::Boolean->cast($value);
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
            ->RoleId($filter)
            ->MapType($filter)
            ->ItemValue($filter)
            ->Priority($filter)
            ->IsActive($filter)
            ->Description($filter);
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
        $thisTable = DatabaseTablesEnum::ClientCategoryMaps;
        $rolesTable = DatabaseTablesEnum::Roles;

        return $query
            ->AllItems($filter)
            ->leftJoin($rolesTable->tableName(), RolesTableEnum::Id->dbNameWithTable($rolesTable), "=", TableEnum::RoleId->dbNameWithTable($thisTable))
            ->select(
                $thisTable->tableName() . ".*",

                RolesTableEnum::Name->dbNameWithTable($rolesTable) . " as client_category_name",
            )
            ->SortOrder($filter, [
                TableEnum::RoleId->dbName() => "client_category_name"
            ]);
    }
    /**************** scopes Collection END ********************/

    /**************** scopes ********************/

    /**
     * scopeActive
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(TableEnum::IsActive->dbName(), 1);
    }

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
                ->orderBy(TableEnum::Priority->dbName(), 'asc');
        }, $replaceSortFields);
    }


    /**
     * Scope a query to only include "role_id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRoleId(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeDropboxId(TableEnum::RoleId->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "map_type" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMapType(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeDropbox(TableEnum::MapType->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "Value" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeItemValue(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeExactly(TableEnum::ItemValue->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "priority" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePriority(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeExactlyNumber(TableEnum::Priority->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "is_active" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsActive(Builder $query, array $filter = null): Builder
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
    public function scopeDescription(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeLikeAs(TableEnum::Descr->dbName(), $query, $filter);
    }
    /**************** scopes END ********************/
}
