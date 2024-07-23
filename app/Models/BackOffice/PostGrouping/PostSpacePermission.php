<?php

namespace App\Models\BackOffice\PostGrouping;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\PostGroupsTableEnum;
use App\Enums\Database\Tables\PostSpacesPermissionsTableEnum  as TableEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Models\SuperClasses\SuperModel;
use Illuminate\Database\Eloquent\Builder;

class PostSpacePermission extends SuperModel
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
        $this->table = DatabaseTablesEnum::PostSpacesPermissions->tableName();
        $this->keyType = 'string';
        $this->incrementing = false;
        $this->timestamps = false;

        $this->fillable = [
            TableEnum::IsActive->dbName(),
            TableEnum::Descr->dbName(),
        ];

        $this->attributes = [
            TableEnum::IsActive->dbName() => 0,
        ];

        $this->casts = [
            TableEnum::IsActive->dbName() => 'boolean',
        ];

        parent::__construct($attributes);
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
        $this->attributes[TableEnum::IsActive->dbName()] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
    /**************** Accessors & Mutators END ********************/

    /**************** scopes Collection ********************/

    /**
     * Get scope of active permissions
     *
     * @param  mixed $query
     * @return Builder
     */
    public function scopeActivePermissions(Builder $query): ?Builder
    {
        return $query->where(TableEnum::IsActive->dbName(), 1);
    }

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
            ->PostSpaceId($filter)
            ->ClientCategoryId($filter)
            ->PostAction($filter)
            ->IsActive($filter)
            ->Description($filter);
    }

    /**
     * Scope a collection of scopes for the "PermissionController->apiIndex" function.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  array $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApiIndexCollection(Builder $query, array $filter): Builder
    {
        $thisTable = DatabaseTablesEnum::PostSpacesPermissions;
        $postGroupsTable = DatabaseTablesEnum::PostGroups;
        $rolesTable = DatabaseTablesEnum::Roles;

        return $query
            ->AllItems($filter)
            ->leftJoin($postGroupsTable->tableName(), PostGroupsTableEnum::Id->dbNameWithTable($postGroupsTable), "=", TableEnum::PostSpaceId->dbNameWithTable($thisTable))
            ->leftJoin($rolesTable->tableName(), RolesTableEnum::Id->dbNameWithTable($rolesTable), "=", TableEnum::ClientCategoryId->dbNameWithTable($thisTable))
            ->where(PostGroupsTableEnum::IsSpace->dbNameWithTable($postGroupsTable), 1)
            ->select(
                $thisTable->tableName() . ".*",

                PostGroupsTableEnum::Title->dbNameWithTable($postGroupsTable) . " as post_space_title",

                RolesTableEnum::Name->dbNameWithTable($rolesTable) . " as client_category_name",
            )
            ->SortOrder($filter, [
                TableEnum::PostSpaceId->dbNameWithTable($thisTable) => "post_space_title",
                TableEnum::ClientCategoryId->dbNameWithTable($thisTable) => "client_category_name"
            ]);
    }
    /**************** scopes Collection END ********************/

    /**************** scopes ********************/

    /**
     * Scope a query to set SortOrder as request or defults.
     *
     * @param array $replaceSortFields
     * @param ?array $filter input data array
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOrder(Builder $query, ?array $filter = null, array $replaceSortFields = []): Builder
    {
        return $this->superScopeSortOrder($query, $filter, function ($query) {
            return $query
                ->orderBy(TableEnum::PostSpaceId->dbName(), 'asc')
                ->orderBy(TableEnum::ClientCategoryId->dbName(), 'asc');
        }, $replaceSortFields);
    }

    /**
     * Scope a query to only include "post_space_id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePostSpaceId(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeDropboxId(TableEnum::PostSpaceId->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "client_category_id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeClientCategoryId(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeDropboxId(TableEnum::ClientCategoryId->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "post_action" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePostAction(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeDropbox(TableEnum::PostAction->dbName(), $query, $filter);
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
