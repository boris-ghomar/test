<?php

namespace App\Models\General;

use App\Enums\Database\DatabaseTablesEnum as Database;
use App\Enums\Database\Tables\PermissionRoleTableEnum;
use App\Enums\Database\Tables\PermissionsTableEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Models\BackOffice\AccessControl\Permission;
use App\Models\SuperClasses\SuperModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermissionRole extends SuperModel
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

        $this->table = Database::PermissionRole->tableName();
        $this->keyType = 'string';
        $this->incrementing = false;
        $this->timestamps = false;

        $this->fillable = [
            PermissionRoleTableEnum::IsActive->dbName(),
            PermissionRoleTableEnum::Descr->dbName(),
        ];

        $this->attributes = [
            PermissionRoleTableEnum::IsActive->dbName() => 0,
        ];

        $this->casts = [
            PermissionRoleTableEnum::IsActive->dbName() => 'boolean',
        ];

        parent::__construct($attributes);
    }

    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get the Permission that owns the PermissionRole
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class, PermissionRoleTableEnum::PermissionId->dbName(), PermissionsTableEnum::Id->dbName());
    }

    /**
     * Get the Role that owns the PermissionRole
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, PermissionRoleTableEnum::RoleId->dbName(), RolesTableEnum::Id->dbName());
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
            ->IsActive($filter)
            ->Description($filter);
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
            ->Role($filter)
            ->Route($filter)
            ->Ability($filter);
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
        $thisTable = Database::PermissionRole->dbName();
        $permissionsTable = Database::Permissions->dbName();
        $rolesTable = Database::Roles->dbName();

        return $query
            ->ControllerAllItems($filter)
            ->join($permissionsTable, PermissionsTableEnum::Id->dbNameWithTable(Database::Permissions), '=', PermissionRoleTableEnum::PermissionId->dbNameWithTable(Database::PermissionRole))
            ->join($rolesTable, RolesTableEnum::Id->dbNameWithTable(Database::Roles), '=', PermissionRoleTableEnum::RoleId->dbNameWithTable(Database::PermissionRole))
            ->where(PermissionsTableEnum::IsActive->dbNameWithTable(Database::Permissions), 1)
            ->select(

                $thisTable . '.*',

                PermissionsTableEnum::Route->dbNameWithTable(Database::Permissions),
                PermissionsTableEnum::Ability->dbNameWithTable(Database::Permissions),

                RolesTableEnum::Name->dbNameWithTable(Database::Roles),
            )
            ->SortOrder($filter, [
                PermissionRoleTableEnum::PermissionId->dbName() => PermissionsTableEnum::Route->dbName(),
                PermissionRoleTableEnum::RoleId->dbName()       => RolesTableEnum::Name->dbName(),
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
                ->orderBy(RolesTableEnum::Name->dbName(), 'asc')
                ->orderBy(PermissionsTableEnum::Route->dbName(), 'asc')
                ->orderBy(PermissionsTableEnum::Ability->dbName(), 'asc');
        }, $replaceSortFields);
    }

    /**
     * Scope a query to only include "role" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRole(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = RolesTableEnum::Name->dbName();
        $dbCol = RolesTableEnum::Name->dbNameWithTable(Database::Roles);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "route" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRoute(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = PermissionsTableEnum::Route->dbName();
        $dbCol = PermissionsTableEnum::Route->dbNameWithTable(Database::Permissions);

        return $this->superScopeDropbox($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "ability" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAbility(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = PermissionsTableEnum::Ability->dbName();
        $dbCol = PermissionsTableEnum::Ability->dbNameWithTable(Database::Permissions);

        return $this->superScopeDropbox($dbCol, $query, $filter, $filterKey);
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
        return $this->superScopeCheckbox(PermissionRoleTableEnum::IsActive->dbName(), $query, $filter);
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
        return $this->superScopeLikeAs(PermissionRoleTableEnum::Descr->dbName(), $query, $filter);
    }


    /**************** scopes END ********************/
}
