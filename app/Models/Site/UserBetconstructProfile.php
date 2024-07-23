<?php

namespace App\Models\Site;

use App\Enums\Database\DatabaseTablesEnum as Database;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\General\ModelGlobalScopesEnum;
use App\Models\BackOffice\ClientsManagement\UserBetconstruct;
use Illuminate\Database\Eloquent\Builder;

class UserBetconstructProfile extends UserBetconstruct
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
        $this->table = Database::Users->tableName();


        $this->fillable = [
            UsersTableEnum::Username->dbName(),
            UsersTableEnum::Email->dbName(),
            UsersTableEnum::Password->dbName(),
            UsersTableEnum::ProfilePhotoName->dbName(),
        ];

        $this->attributes = [
            //
        ];

        $this->casts = [
            //
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
        parent::booted();

        static::addGlobalScope(ModelGlobalScopesEnum::UserBetconstructProfile_AuthProfile->name, function (Builder $builder) {

            $userId = auth()->check()  ? auth()->user()->id : null;

            $builder->where(UsersTableEnum::Id->dbName(), $userId);
        });
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/
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
        return $query;
    }

    /**************** scopes Collection END ********************/

    /**************** scopes ********************/
    /**************** scopes END ********************/
}
