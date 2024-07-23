<?php

namespace App\Models\BackOffice\ClientsManagement;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\RolesTableEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\General\ModelGlobalScopesEnum;
use App\Enums\Users\RoleTypesEnum;
use App\HHH_Library\general\php\DropdownListCreater;
use App\Models\General\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientCategory extends Role
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
        $this->table = DatabaseTablesEnum::Roles->tableName();

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
        static::addGlobalScope(ModelGlobalScopesEnum::ClientCategory_Only->name, function (Builder $builder) {
            $builder->where(TableEnum::Type->dbName(), RoleTypesEnum::Site->name);
        });
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get all of the PersonnelExtra for the PersonnelRole
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function client(): HasMany
    {
        return $this->hasMany(UserBetconstruct::class, UsersTableEnum::RoleId->dbName(), TableEnum::Id->dbName());
    }

    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/

    public static function getDorpdownList(): array
    {
        return DropdownListCreater::makeByModel(self::class, TableEnum::Name->dbName())
        ->useReverseList()
        ->sort()
        ->get();
    }
    /************************ Exclusive Items END ****************************/
}
