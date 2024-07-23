<?php

namespace App\Models\Site\Referral;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ReferralsTableEnum as TableEnum;
use App\Enums\General\ModelGlobalScopesEnum;
use App\Models\BackOffice\Referral\Referral;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ReferralPanel extends Referral
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
        $this->table = DatabaseTablesEnum::Referrals->tableName();

        $this->fillable = [];

        parent::__construct($attributes);
    }

    /**
     * The "booted" method of the model.
     * This scope controls only Betcart users to be loaded on all requests of this model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::booted();

        static::addGlobalScope(ModelGlobalScopesEnum::MyReferral_Only->name, function (Builder $builder) {

            $builder->where(TableEnum::UserId->dbName(), User::authUser()->id);
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
    /**************** scopes Collection END ********************/

    /**************** scopes ********************/
    //
    /**************** scopes END ********************/
}
