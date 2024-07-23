<?php

namespace App\Models\BackOffice\PeronnelManagement;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\PersonnelExtrasTableEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Models\SuperClasses\SuperModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonnelExtra extends SuperModel
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

        $this->table = DatabaseTablesEnum::PersonnelExtras->tableName();

        $this->fillable = [
            TableEnum::FirstName->dbName(),
            TableEnum::LastName->dbName(),
            TableEnum::AliasName->dbName(),
            TableEnum::Gender->dbName(),
            TableEnum::Descr->dbName(),
        ];

        parent::__construct($attributes);
    }

    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get the Personnel that owns the PersonnelExtra
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function personnel(): BelongsTo
    {
        return $this->belongsTo(Personnel::class, TableEnum::UserId->dbName(), UsersTableEnum::Id->dbName());
    }
    /**************** Relationships END ********************/
}
