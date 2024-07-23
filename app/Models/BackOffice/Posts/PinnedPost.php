<?php

namespace App\Models\BackOffice\Posts;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\PostsTableEnum as TableEnum;
use App\Enums\General\ModelGlobalScopesEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PinnedPost extends Post
{
    use HasFactory;
    /**************** Parnet Items ********************/

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->table = DatabaseTablesEnum::Posts->tableName();

        parent::__construct($attributes);

        $this->fillable = [
            TableEnum::PinNumber->dbName(),
            TableEnum::PrivateNote->dbName(),
        ];


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

        static::addGlobalScope(ModelGlobalScopesEnum::PinnedPost_Only->name, function (Builder $builder) {

            $builder->where(TableEnum::PinNumber->dbName(), ">", 0);
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

    /**
     * Scope a query to set SortOrder as request or defults.
     *
     * @param array $replaceSortFields
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @param  array $replaceSortFields
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOrder(Builder $query, array $filter = null, array $replaceSortFields = []): Builder
    {
        return $this->superScopeSortOrder($query, $filter, function ($query) {
            return $query
                ->orderBy(TableEnum::PinNumber->dbName(), 'asc');
        }, $replaceSortFields);
    }

    /**************** scopes END ********************/
}
