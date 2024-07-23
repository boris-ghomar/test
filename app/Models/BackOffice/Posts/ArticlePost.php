<?php

namespace App\Models\BackOffice\Posts;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\PostsTableEnum as TableEnum;
use App\Enums\General\ModelGlobalScopesEnum;
use App\Enums\Routes\AdminPublicRoutesEnum;
use App\Models\BackOffice\PostGrouping\PostSpace;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArticlePost extends Post
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

        $this->fillable = [
            TableEnum::PostSpaceId->dbName(),
            TableEnum::Title->dbName(),
            TableEnum::MainPhoto->dbName(),
            TableEnum::MetaDescription->dbName(),
            TableEnum::IsPublished->dbName(),
            TableEnum::AuthorId->dbName(),
            TableEnum::EditorId->dbName(),
            TableEnum::PrivateNote->dbName(),
        ];

        $this->appends = [
            TableEnum::ShortenedContentForPostSpace->dbName(),
        ];

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

        static::addGlobalScope(ModelGlobalScopesEnum::ArticlePost_Only->name, function (Builder $builder) {

            $builder->whereIn(TableEnum::PostSpaceId->dbName(), PostSpace::Articles()->PluckIds());
        });
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/
    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/

    /**
     * Get the edit post URL
     *
     * @return string
     */
    public function getEditUrlAttribute(): string
    {
        return AdminPublicRoutesEnum::Posts_ArticlesEdit->url(['articlePost' => $this[TableEnum::Id->dbName()]]);
    }

    /**
     * Get the display post URL
     *
     * @return string
     */
    public function getDisplayUrlAttribute(): string
    {
        // Comes from parent (post)
        return $this[TableEnum::DisplayUrlArticle->dbName()];
    }

    /**************** Accessors & Mutators END ********************/

    /**************** scopes Collection ********************/
    /**************** scopes Collection END ********************/

    /**************** scopes ********************/
    /**************** scopes END ********************/
}
