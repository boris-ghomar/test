<?php

namespace App\Models\BackOffice\AccessControl;

use App\Enums\AccessControl\PermissionTypeEnum;
use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\PermissionsTableEnum as TableEnum;
use App\Enums\General\ModelGlobalScopesEnum;
use Illuminate\Database\Eloquent\Builder;

class PermissionAdminPanel extends Permission
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
        $this->table = DatabaseTablesEnum::Permissions->tableName();

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
            $builder->where(TableEnum::Type->dbName(), PermissionTypeEnum::AdminPanel->name);
        });
    }
    /**************** Parnet Items END ********************/
}
