<?php

namespace App\Models\BackOffice\AccessControl;

use App\Enums\AccessControl\PermissionTypeEnum;
use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\PermissionRoleTableEnum;
use App\Enums\Database\Tables\PermissionsTableEnum as TableEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Models\General\Role;
use App\Models\SuperClasses\SuperModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends SuperModel
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
            TableEnum::Route->dbName(),
            TableEnum::Ability->dbName(),
            TableEnum::Type->dbName(),
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

    /**
     * The roles that belong to the Permission
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            DatabaseTablesEnum::PermissionRole->tableName(),
            PermissionRoleTableEnum::PermissionId->dbName(),
            PermissionRoleTableEnum::RoleId->dbName(),
        );
    }

    /**
     * The active roles that belong to the Permission
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function rolesActive(): BelongsToMany
    {
        return $this->roles()
            ->where(RolesTableEnum::IsActive->dbNameWithTable(DatabaseTablesEnum::Roles), 1)
            ->where(PermissionRoleTableEnum::IsActive->dbNameWithTable(DatabaseTablesEnum::PermissionRole), 1);
    }

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
     * Get scope of Active permissions
     *
     * @param  mixed $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(TableEnum::IsActive->dbName(), 1);
    }

    /**
     * Get scope of Active permissions
     *
     * @param  mixed $query
     * @return Builder
     */
    public function scopeNotActive(Builder $query): Builder
    {
        return $query->where(TableEnum::IsActive->dbName(), 0);
    }

    /**
     * Scope a collection of scopes for get admin panel permissions.
     *
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdminPanelPermissions(Builder $query): Builder
    {
        return $query->where(TableEnum::Type->dbName(), PermissionTypeEnum::AdminPanel->name);
    }

    /**
     * Scope a collection of scopes for get site permissions.
     *
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSitePermissions(Builder $query): Builder
    {
        return $query->where(TableEnum::Type->dbName(), PermissionTypeEnum::Site->name);
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
            ->Route($filter)
            ->Ability($filter)
            ->Type($filter)
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
        return $query
            ->AllItems($filter)
            ->SortOrder($filter);
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
                ->orderBy(TableEnum::Route->dbName(), 'asc')
                ->orderBy(TableEnum::Ability->dbName(), 'asc');
        }, $replaceSortFields);
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
        return $this->superScopeDropbox(TableEnum::Route->dbName(), $query, $filter);
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
        return $this->superScopeDropbox(TableEnum::Ability->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "type" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeType(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeDropbox(TableEnum::Type->dbName(), $query, $filter);
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
