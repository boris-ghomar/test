<?php

namespace App\Models\BackOffice\PostGrouping;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\PostGroupsTableEnum as TableEnum;
use App\Enums\Database\Tables\PostsTableEnum;
use App\Enums\General\ModelGlobalScopesEnum;
use App\Enums\Posts\TemplatesEnum;
use App\Models\BackOffice\Posts\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostSpace extends PostGroup
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

        parent::__construct($attributes);

        $this->fillable = [
            TableEnum::Title->dbName(),
            TableEnum::ParentId->dbName(),
            TableEnum::Template->dbName(),
            TableEnum::Description->dbName(),
            TableEnum::Photo->dbName(),
            TableEnum::IsPublicSpace->dbName(),
            TableEnum::IsActive->dbName(),
            TableEnum::PrivateNote->dbName(),
        ];

        $this->attributes = [
            TableEnum::IsActive->dbName() => 0,
            TableEnum::IsSpace->dbName() => 1,
        ];
    }

    /**
     * The "booted" method of the model.
     * This scope controls only personnel users to be loaded on all requests of this model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::addGlobalScope(ModelGlobalScopesEnum::PostSpace_Only->name, function (Builder $builder) {
            $builder->where(TableEnum::IsSpace->dbNameWithTable(DatabaseTablesEnum::PostGroups), 1);
        });
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get the "PostCategory" that owns the PostSpace
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function postCategory(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class, TableEnum::ParentId->dbName(), TableEnum::Id->dbName());
    }

    /**
     * Get all of the posts for the PostSpace
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, PostsTableEnum::PostSpaceId->dbName(), TableEnum::Id->dbName());
    }

    /**
     * Get all of the published posts for the PostSpace
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function publishedPosts(): HasMany
    {
        return $this->posts()->where(PostsTableEnum::IsPublished->dbNameWithTable(DatabaseTablesEnum::Posts), 1);
    }

    /**
     * Get all of the published posts for the PostSpace with sort
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function publishedPostsWithSort(): HasMany
    {
        return $this->publishedPosts()
            ->orderBy(PostsTableEnum::IsPinned->dbName(), 'desc')
            ->orderBy(PostsTableEnum::PinNumber->dbName(), 'asc')
            ->orderBy(TimestampsEnum::UpdatedAt->dbName(), 'desc')
            ->orderBy(TimestampsEnum::CreatedAt->dbName(), 'desc');
    }

    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/
    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/
    /**************** Accessors & Mutators END ********************/

    /**************** scopes Collection ********************/

    /**
     * Scope public sapces
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return Builder
     */
    public function scopePublicSpaces(Builder $query): ?Builder
    {
        return $query->where(TableEnum::IsPublicSpace->dbName(), 1);
    }

    /**
     * Scope private sapces
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return Builder
     */
    public function scopePrivateSpaces(Builder $query): ?Builder
    {
        return $query->where(TableEnum::IsPublicSpace->dbName(), 0);
    }

    /**
     * Scope Articles sapces
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return Builder
     */
    public function scopeArticles(Builder $query): ?Builder
    {
        return $query->where(TableEnum::Template->dbNameWithTable(DatabaseTablesEnum::PostGroups), TemplatesEnum::Article->name);
    }

    /**
     * Scope FAQ sapces
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return Builder
     */
    public function scopeFaqs(Builder $query): ?Builder
    {
        return $query->where(TableEnum::Template->dbNameWithTable(DatabaseTablesEnum::PostGroups), TemplatesEnum::FAQ->name);
    }

    /**
     * Scope Photo Galleries sapces
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return Builder
     */
    public function scopePhotoGalleries(Builder $query): ?Builder
    {
        return $query->where(TableEnum::Template->dbNameWithTable(DatabaseTablesEnum::PostGroups), TemplatesEnum::PhotoGallery->name);
    }

    /**
     * Scope Video Galleries sapces
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return Builder
     */
    public function scopeVideoGalleries(Builder $query): ?Builder
    {
        return $query->where(TableEnum::Template->dbNameWithTable(DatabaseTablesEnum::PostGroups), TemplatesEnum::VideoGallery->name);
    }
    /**************** scopes Collection END ********************/

    /**************** scopes ********************/
    /**************** scopes END ********************/
}
