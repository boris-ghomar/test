<?php

namespace App\Models\BackOffice\Comments;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\CommentsTableEnum as TableEnum;
use App\Enums\General\ModelGlobalScopesEnum;
use Illuminate\Database\Eloquent\Builder;
use App\Models\BackOffice\Comments\Comment as BackoffceComment;

class UnapprovedComment extends BackoffceComment
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
        $this->table = DatabaseTablesEnum::Comments->tableName();

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

        static::addGlobalScope(ModelGlobalScopesEnum::UnapprovedComment_Only->name, function (Builder $builder) {

            $builder->where(TableEnum::IsApproved->dbName(), 0);
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
    /**************** scopes END ********************/
}
