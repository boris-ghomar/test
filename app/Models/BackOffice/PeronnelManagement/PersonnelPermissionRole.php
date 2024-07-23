<?php

namespace App\Models\BackOffice\PeronnelManagement;

use App\Enums\AccessControl\PermissionTypeEnum;
use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\PermissionsTableEnum;
use App\Models\General\PermissionRole;
use Illuminate\Database\Eloquent\Builder;

class PersonnelPermissionRole extends PermissionRole
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
        $this->table = DatabaseTablesEnum::PermissionRole->tableName();

        parent::__construct($attributes);
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
        $filterKey = DatabaseTablesEnum::Permissions->dbName() . '.' . PermissionsTableEnum::Type->dbName();

        return parent::scopeApiIndexCollection($query, $filter)
            ->where($filterKey, PermissionTypeEnum::AdminPanel->name);
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/
    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/
    /**************** Accessors & Mutators END ********************/

    /**************** scopes Collection ********************/
    /**************** scopes END ********************/
}
