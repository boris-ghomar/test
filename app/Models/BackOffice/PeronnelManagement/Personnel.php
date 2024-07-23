<?php

namespace App\Models\BackOffice\PeronnelManagement;

use App\Enums\Database\DatabaseTablesEnum as Database;
use App\Enums\Database\Tables\PersonnelExtrasTableEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\Database\Tables\UsersTableEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\General\ModelGlobalScopesEnum;
use App\Enums\Users\UsersStatusEnum;
use App\Enums\Users\UsersTypesEnum;
use App\HHH_Library\general\php\traits\ModelSuperScopes;
use App\Models\User;
use App\Notifications\BackOffice\User\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Personnel extends User
{
    use HasFactory;
    use ModelSuperScopes;

    /**************** Parnet Items ********************/

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {

        $this->table = Database::Users->tableName();

        $this->fillable = [
            TableEnum::Username->dbName(),
            TableEnum::Email->dbName(),
            TableEnum::RoleId->dbName(),
            TableEnum::Status->dbName(),
        ];

        parent::__construct($attributes);
    }

    /**
     * The "booted" method of the model.
     * This scope controls only personnel users to be loaded on all requests of this model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::addGlobalScope(ModelGlobalScopesEnum::Personnel_Only->name, function (Builder $builder) {
            $builder->where(TableEnum::Type->dbNameWithTable(Database::Users), UsersTypesEnum::Personnel->name);
        });
    }

    /**
     * @override
     * Send notification with User::calss instead of Personnel::Class
     *
     * @param  mixed $instance
     * @return void
     */
    public function notify($instance)
    {

        $this->user->notify($instance);
    }

    /**
     * @override
     *
     * Send password reset notification
     *
     * @param  mixed $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }


    /**
     * Get a new factory instance for the model.
     *
     * @param  callable|array|int|null  $count
     * @param  callable|array  $state
     * @return \Illuminate\Database\Eloquent\Factories\Factory<static>
     */
    public static function factory($count = null, $state = [])
    {
        return parent::factory($count, $state)
            ->has(
                PersonnelExtra::factory()
                    ->count(1)
                    ->state(function (array $attributes, Personnel $personnel) {
                        return [
                            PersonnelExtrasTableEnum::UserId->dbName() => $personnel[TableEnum::Id->dbName()]
                        ];
                    })
            );
    }

    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get personnel "user" model
     *
     * @return Illuminate\Database\Eloquent\Relations\HasOne $personnel
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, UsersTableEnum::Id->dbName());
    }

    /**
     * Get the personnelExtra associated with the Personnel
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function personnelExtra(): HasOne
    {
        return $this->hasOne(PersonnelExtra::class, PersonnelExtrasTableEnum::UserId->dbName(), TableEnum::Id->dbName());
    }

    /**
     * @override
     * Get the Role that owns the Personnel
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(PersonnelRole::class, TableEnum::RoleId->dbName(), RolesTableEnum::Id->dbName());
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
            ->Id($filter)
            ->Username($filter)
            ->Email($filter)
            ->Role($filter)
            ->Status($filter);
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
            ->FirstName($filter)
            ->LastName($filter)
            ->AliasName($filter)
            ->Gender($filter)
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

        $personnelExtrasTable = Database::PersonnelExtras;
        $usersTable = Database::Users;
        $rolesTable = Database::Roles;

        return $query
            ->ControllerAllItems($filter)
            ->join($personnelExtrasTable->tableName(), PersonnelExtrasTableEnum::UserId->dbNameWithTable($personnelExtrasTable), '=', TableEnum::Id->dbNameWithTable($usersTable))
            ->join($rolesTable->tableName(), RolesTableEnum::Id->dbNameWithTable($rolesTable), '=', TableEnum::RoleId->dbNameWithTable($usersTable))
            ->select(

                TableEnum::Id->dbNameWithTable($usersTable),
                TableEnum::Username->dbNameWithTable($usersTable),
                TableEnum::Email->dbNameWithTable($usersTable),
                TableEnum::RoleId->dbNameWithTable($usersTable),
                TableEnum::Status->dbNameWithTable($usersTable),

                PersonnelExtrasTableEnum::FirstName->dbNameWithTable($personnelExtrasTable),
                PersonnelExtrasTableEnum::LastName->dbNameWithTable($personnelExtrasTable),
                PersonnelExtrasTableEnum::AliasName->dbNameWithTable($personnelExtrasTable),
                PersonnelExtrasTableEnum::Gender->dbNameWithTable($personnelExtrasTable),
                PersonnelExtrasTableEnum::Descr->dbNameWithTable($personnelExtrasTable),

                RolesTableEnum::Name->dbNameWithTable($rolesTable),
            )
            ->SortOrder($filter);
    }


    /**************** scopes Collection END ********************/

    /**************** Relation scopes ********************/

    /**
     * Scope a collection of scopes for the "Active Personnel".
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivePersonnel(Builder $query): Builder
    {
        return $query->where(TableEnum::Status, UsersStatusEnum::Active->name);
    }

    /**************** Relation scopes END ********************/

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
                ->orderBy(TableEnum::Username->dbName(), 'asc');
        }, $replaceSortFields);
    }

    /**
     * Scope a query to only include "id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeId(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeExactlyNumber(UsersTableEnum::Id->dbName(), $query, $filter);
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
        return $this->superScopeLikeAs(UsersTableEnum::Username->dbName(), $query, $filter);
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
        return $this->superScopeLikeAs(UsersTableEnum::Email->dbName(), $query, $filter);
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
        return $this->superScopeDropboxId(UsersTableEnum::RoleId->dbName(), $query, $filter);
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
        return $this->superScopeDropbox(UsersTableEnum::Status->dbName(), $query, $filter);
    }

    /**************** Joined Items ********************/

    /**
     * Scope a query to only include "first_name" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFirstName(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = PersonnelExtrasTableEnum::FirstName->dbName();
        $dbCol = PersonnelExtrasTableEnum::FirstName->dbNameWithTable(Database::PersonnelExtras);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "last_name" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLastName(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = PersonnelExtrasTableEnum::LastName->dbName();
        $dbCol = PersonnelExtrasTableEnum::LastName->dbNameWithTable(Database::PersonnelExtras);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "alias_name" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAliasName(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = PersonnelExtrasTableEnum::AliasName->dbName();
        $dbCol = PersonnelExtrasTableEnum::AliasName->dbNameWithTable(Database::PersonnelExtras);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "gender" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGender(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = PersonnelExtrasTableEnum::Gender->dbName();
        $dbCol = PersonnelExtrasTableEnum::Gender->dbNameWithTable(Database::PersonnelExtras);

        return $this->superScopeDropbox($dbCol, $query, $filter, $filterKey);
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
        $filterKey = PersonnelExtrasTableEnum::Descr->dbName();
        $dbCol = PersonnelExtrasTableEnum::Descr->dbNameWithTable(Database::PersonnelExtras);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }

    /**************** Joined Items END ********************/


    /**************** scopes END ********************/
}
