<?php

namespace App\Models\BackOffice\PostGrouping;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\PostGroupsTableEnum as TableEnum;
use App\Enums\Routes\SitePublicRoutesEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\Enums\PregPatternEnum;
use App\HHH_Library\general\php\Enums\SeoMetaTagsEnum;
use App\Models\SuperClasses\SuperModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PostGroup extends SuperModel
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

        $this->fillable = [
            TableEnum::ParentId->dbName(),
            TableEnum::Title->dbName(),
            TableEnum::Description->dbName(),
            TableEnum::Template->dbName(),
            TableEnum::Photo->dbName(),
            TableEnum::IsActive->dbName(),
            TableEnum::IsPublicSpace->dbName(),
            TableEnum::PrivateNote->dbName(),
        ];

        $this->attributes = [
            TableEnum::IsActive->dbName()       => 1,
            TableEnum::IsSpace->dbName()        => 0,
            TableEnum::IsPublicSpace->dbName()  => 0,
        ];

        $this->casts = [
            TableEnum::IsSpace->dbName() => 'boolean',
            TableEnum::IsActive->dbName() => 'boolean',
        ];

        parent::__construct($attributes);
    }

    /**
     * @override parent boot
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::retrieved(function (PostGroup $model) {
            return self::castAttributes($model);
        });

        self::saving(function (PostGroup $model) {
            return self::castAttributes($model);
        });

        self::creating(function (PostGroup $model) {

            $model[TableEnum::Position->dbName()] = self::getposition($model);
            return self::castAttributes($model);
        });
    }

    /**
     * Get the attributes as a true format type
     *
     * @param self $model
     * @return self
     */
    private static function castAttributes(self $model): self
    {
        $attributes = $model->getAttributes();

        /**
         * @var TableEnum $case
         * @var CastEnum $cast
         */
        foreach (TableEnum::castableAttributes() as $dbName => $cast) {


            if (isset($attributes[$dbName])) {

                $model->$dbName = $cast->cast($attributes[$dbName]);
            }
        }

        return $model;
    }

    /**
     * Get position of new item
     *
     * @param  mixed $model
     * @return self
     */
    private static function getPosition(self $model): int
    {

        $idCol = TableEnum::Id->dbName();
        $positionCol = TableEnum::Position->dbName();
        $parentIdCol = TableEnum::ParentId->dbName();

        $parentId = $model->getAttribute($parentIdCol);

        $lastChild = PostGroupsDisplayPosition::where($parentIdCol, $parentId)
            ->select($idCol, $positionCol)
            ->orderBy($positionCol, 'desc') // 1th: Position->desc
            ->orderBy(TimestampsEnum::UpdatedAt->dbName(), 'asc') // 2th: update_at->asc
            ->first();

        return is_null($lastChild) ? 1 : $lastChild[$positionCol] + 1;
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get the "Parent Group" that owns the Group
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parentGroup(): BelongsTo
    {
        return $this->belongsTo(self::class, TableEnum::ParentId->dbName(), TableEnum::Id->dbName());
    }
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/

    /**
     * Get all model IDs as array
     *
     * @return array
     */
    public static function getAllIds(): array
    {
        return self::all()->pluck(TableEnum::Id->dbName())->toArray();
    }
    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/

    /**
     * Get the display post URL
     *
     * @return string
     */
    public function getDisplayUrlAttribute(): string
    {
        return SitePublicRoutesEnum::PostGroupContentDispaly->url([
            'postGroup' => $this[TableEnum::Id->dbName()],
            'slug' => $this->UrlSlug,
        ]);
    }

    /**
     * Get the slug of display post URL
     *
     * @return string
     */
    public function getUrlSlugAttribute(): string
    {

        $slug = $this[TableEnum::Title->dbName()];
        $slug = PregPatternEnum::UrlEncodeSpecialCharacters->pregReplace(" ", $slug);
        $slug = PregPatternEnum::WhiteSpaces->pregReplace("-", $slug);
        $slug = strtolower($slug);

        return $slug;
    }

    /**
     * Get the canonical Url of post
     *
     * @return ?string
     */
    public function getCanonicalUrlAttribute(): ?string
    {
        $appDomain = config('app.domain');
        $canonicalDomain = config('hhh_config.Domains.Canonical');

        if ($appDomain !== $canonicalDomain) {

            $canonicalUrl = Str::replaceFirst($appDomain, $canonicalDomain,  $this->DisplayUrl);
            return SeoMetaTagsEnum::Canonical->getHtmlTag($canonicalUrl);
        }

        return null;
    }

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
        return $query
            ->Title($filter)
            ->ParentId($filter)
            ->Description($filter)
            ->Template($filter)
            ->IsPublicSpace($filter)
            ->IsActive($filter)
            ->PrivateNote($filter);
    }

    /**
     * Scope a collection of scopes for the "Controller->apiIndex" function.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  array $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApiIndexCollection(Builder $query, array $filter): Builder
    {
        $tableName = DatabaseTablesEnum::PostGroups->tableName();

        return $query
            ->AllItems($filter)
            ->leftJoin($tableName . ' as parent', "parent." . TableEnum::Id->dbName(), "=", $tableName . '.' . TableEnum::ParentId->dbName())
            ->select(
                $tableName . '.*',

                sprintf('parent.%s as parent_title', TableEnum::Title->dbName()),

            )
            ->SortOrder($filter, [
                TableEnum::ParentId->dbName() => "parent_title",
            ]);
    }
    /**************** scopes Collection END ********************/

    /**************** scopes ********************/

    /**
     * Get IDs os model query  as array
     *
     * @return array
     */
    public function scopePluckIds(Builder $query): array
    {
        return $query->pluck(TableEnum::Id->dbName())->toArray();
    }

    /**
     * Scope a query to set SortOrder as request or defults.
     *
     * @param array $replaceSortFields
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOrder(Builder $query, ?array $filter = null, array $replaceSortFields = []): Builder
    {
        return $this->superScopeSortOrder($query, $filter, function ($query) {
            return $query
                ->orderBy(TableEnum::ParentId->dbName(), 'asc')
                ->orderBy(TableEnum::Position->dbName(), 'asc')
                ->orderBy(TableEnum::Id->dbName(), 'asc');
        }, $replaceSortFields);
    }

    /**
     * Scope a query to only include "title" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTitle(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeLikeAs(TableEnum::Title->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "parent_id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParentId(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeDropboxId(TableEnum::ParentId->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "description" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDescription(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeLikeAs(TableEnum::Description->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "template" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTemplate(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeDropbox(TableEnum::Template->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "is_public_space" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsPublicSpace(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeCheckbox(TableEnum::IsPublicSpace->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "is_active" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsActive(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeCheckbox(TableEnum::IsActive->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "private_note" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePrivateNote(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeLikeAs(TableEnum::PrivateNote->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "position" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePosition(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeNumberRange(TableEnum::Position->dbName(), $query, $filter);
    }
    /**************** scopes END ********************/
}
