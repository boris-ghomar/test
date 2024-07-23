<?php

namespace App\Models\BackOffice\PostGrouping;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\PostGroupsTableEnum as TableEnum;
use App\Enums\General\ModelGlobalScopesEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostCategory extends PostGroup
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
        $this->table = DatabaseTablesEnum::PostGroups->tableName();

        $this->fillable = [
            TableEnum::Title->dbName(),
            TableEnum::ParentId->dbName(),
            TableEnum::Description->dbName(),
            TableEnum::Photo->dbName(),
            TableEnum::IsActive->dbName(),
            TableEnum::PrivateNote->dbName(),
        ];

        $this->attributes = [
            TableEnum::IsActive->dbName() => 1,
            TableEnum::IsSpace->dbName() => 0,
            TableEnum::IsPublicSpace->dbName() => 0,
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
        static::addGlobalScope(ModelGlobalScopesEnum::PostCategory_Only->name, function (Builder $builder) {
            $builder->where(TableEnum::IsSpace->dbNameWithTable(DatabaseTablesEnum::PostGroups), 0);
        });
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get all of the "subCategories" for the PostCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subCategories(): HasMany
    {
        return $this->hasMany(PostCategory::class, TableEnum::ParentId->dbName(), TableEnum::Id->dbName());
    }

    /**
     * Get all of the "PostSapces" for the PostCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function postSapces(): HasMany
    {
        return $this->hasMany(PostSpace::class, TableEnum::ParentId->dbName(), TableEnum::Id->dbName());
    }
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
