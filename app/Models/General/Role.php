<?php

namespace App\Models\General;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\PermissionRoleTableEnum;
use App\Enums\Database\Tables\PermissionsTableEnum;
use App\Enums\Database\Tables\RolesTableEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Users\UsersStatusEnum;
use App\Models\BackOffice\AccessControl\Permission;
use App\Models\SuperClasses\SuperModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends SuperModel
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
            TableEnum::DisplayName->dbName(),
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
     * Get all of the users for the Role
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, UsersTableEnum::RoleId->dbName(), TableEnum::Id->dbName());
    }

    /**
     * Get all of the active users for the Role
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usersActive(): HasMany
    {
        return $this->users()
            ->where(UsersTableEnum::Status->dbNameWithTable(DatabaseTablesEnum::Users), UsersStatusEnum::Active->name);
    }

    /**
     * The permissions that belong to the role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            DatabaseTablesEnum::PermissionRole->tableName(),
            PermissionRoleTableEnum::RoleId->dbName(),
            PermissionRoleTableEnum::PermissionId->dbName(),
        );
    }

    /**
     * The active roles that belong to the Permission
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissionsActive(): BelongsToMany
    {
        return $this->permissions()
            ->where(PermissionsTableEnum::IsActive->dbNameWithTable(DatabaseTablesEnum::Permissions), 1)
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

    /**
     * Interact with the role's DisplayName.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function displayName(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value, array $attributes) => is_null($value) ? $attributes[TableEnum::Name->dbName()] : $value,
        );
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
            ->Name($filter)
            ->DisplayName($filter)
            ->Type($filter)
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
     * Scope a query to only include "display_name" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDisplayName(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeLikeAs(TableEnum::DisplayName->dbName(), $query, $filter);
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
