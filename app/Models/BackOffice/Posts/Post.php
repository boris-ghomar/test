<?php

namespace App\Models\BackOffice\Posts;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\CommentsTableEnum;
use App\Enums\Database\Tables\LikesTableEnum;
use App\Enums\Database\Tables\PostGroupsTableEnum;
use App\Enums\Database\Tables\PostsTableEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Posts\TemplatesEnum;
use App\Enums\Resources\ImageConfigEnum;
use App\Enums\Routes\SitePublicRoutesEnum;
use App\Enums\UserActions\CommentableTypesEnum;
use App\Enums\UserActions\LikableTypesEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\Enums\PregPatternEnum;
use App\HHH_Library\general\php\Enums\SeoMetaTagsEnum;
use App\HHH_Library\general\php\FileAssistant;
use App\Models\BackOffice\PeronnelManagement\Personnel;
use App\Models\BackOffice\PostGrouping\PostSpace;
use App\Models\Site\UserActions\Comment;
use App\Models\Site\UserActions\Like;
use App\Models\SuperClasses\SuperModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Post extends SuperModel
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
        $this->attributes = [
            TableEnum::IsPublished->dbName() => 0,
        ];

        $this->casts = [
            TableEnum::IsPublished->dbName() => 'boolean',
        ];

        parent::__construct($attributes);
    }

    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get the "PostSpace" that owns the Post
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function postSpace(): BelongsTo
    {
        return $this->belongsTo(PostSpace::class, TableEnum::PostSpaceId->dbName(), PostGroupsTableEnum::Id->dbName());
    }

    /**
     * Get the "Author Personnel" that owns the Post
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Personnel::class, TableEnum::AuthorId->dbName(), UsersTableEnum::Id->dbName());
    }

    /**
     * Get the "Editor Personnel" that owns the Post
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(Personnel::class, TableEnum::EditorId->dbName(), UsersTableEnum::Id->dbName());
    }

    /**
     * Get all of the likes for the Post
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class, LikesTableEnum::LikableId->dbName(), TableEnum::Id->dbName())
            ->where(LikesTableEnum::LikableType->dbName(), LikableTypesEnum::Post->name);
    }

    /**
     * Get all of the comments for the Post
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, CommentsTableEnum::CommentableId->dbName(), TableEnum::Id->dbName())
            ->where(CommentsTableEnum::CommentableType->dbName(), CommentableTypesEnum::Post->name);
    }

    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/

    /**
     * Get profile photo file config
     *
     * @return \App\Enums\Resources\ImageConfigEnum
     */
    public static function getMainPhotoFileConfig(): ImageConfigEnum
    {
        return ImageConfigEnum::PostMainPhoto;
    }

    /**
     * Get photo file assistant
     *
     * @param bool $useFallbackPhoto : true => if file not exists, it will be return fallback image (no profile)
     * @return \App\HHH_Library\general\php\FileAssistant
     */
    public function getMainPhotoFileAssistant(bool $useFallbackPhoto = true): FileAssistant
    {
        $fileConfig = $this->getMainPhotoFileConfig();

        $fileAssistant = new FileAssistant($fileConfig, $this[TableEnum::MainPhoto->dbName()]);

        if ($useFallbackPhoto && !$fileAssistant->isFileExists()) {

            $fileAssistant->setPath($fileConfig->defaultPath());
            $fileAssistant->setName($fileConfig->defaultImage());
        }

        return $fileAssistant;
    }

    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/

    /**
     * Convert string boolean to boolean when saving information.
     * When receiving information from the API client,
     * boolean values may be received in the form of strings.
     *
     * Example:
     *   "true" => true
     *   "false" => false
     *
     * @param mixed $value
     * @return void
     */
    public function setIsPublishedAttribute(mixed $value): void
    {
        $this->attributes[TableEnum::IsPublished->dbName()] = CastEnum::Boolean->cast($value);
    }

    /**
     * Get shortened content attribute to dispaly in management list table
     *
     * @return ?string
     */
    public function getShortenedContentForTableAttribute(): ?string
    {
        return $this->getShortenedContent(10);
    }

    /**
     * Get shortened content attribute to dispaly in management list table
     *
     * @return ?string
     */
    public function getShortenedContentForPostSpaceAttribute(): ?string
    {
        return $this->getShortenedContent(30);
    }

    /**
     * Get shortened content attribute
     *
     * @return ?string
     */
    public function getShortenedContent(int $words = 30): ?string
    {
        return TableEnum::Content->summarize($this->getAttribute(TableEnum::Content->dbName()), $words);
    }

    /**
     * All times are stored in the database based on UTC time(00:00),
     * so time is converted to user local timezone,
     * and selected calendar type
     * based on the user and admin panel settings.
     *
     * @param  mixed $value
     * @return ?string
     */
    public function getCreatedAtAttribute(mixed $value): ?string
    {
        $user = User::authUser();

        return is_null($user) ? $value : $user->convertUTCToLocalTime($value);
    }

    /**
     * All times are stored in the database based on UTC time(00:00),
     * so time is converted to user local timezone,
     * and selected calendar type
     * based on the user and admin panel settings.
     *
     * @param  mixed $value
     * @return ?string
     */
    public function getContentUpdatedAtAttribute(mixed $value): ?string
    {
        $user = User::authUser();

        return is_null($user) ? $value : $user->convertUTCToLocalTime($value);
    }

    /**
     * Get the post main photo Url
     *
     * @return string
     */
    public function getMainPhotoUrlAttribute(): string
    {
        return $this->getMainPhotoFileAssistant()->getUrl();
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

    /**
     * Get the display post URL
     *
     * @return ?string
     */
    public function getDisplayUrlAttribute(): ?string
    {
        $postSpace = $this->postSpace;

        return match ($postSpace[PostGroupsTableEnum::Template->dbName()]) {

            TemplatesEnum::Article->name => $this[TableEnum::DisplayUrlArticle->dbName()],
            TemplatesEnum::FAQ->name => $this[TableEnum::DisplayUrlFaq->dbName()],

            default => null
        };
    }

    /**
     * Get the display post URL
     *
     * @return string
     */
    public function getDisplayUrlArticleAttribute(): string
    {
        return SitePublicRoutesEnum::PostArticle->url([
            'articlePost' => $this[TableEnum::Id->dbName()],
            'slug' => $this->UrlSlug,
        ]);
    }

    /**
     * Get the display post URL
     *
     * @return string
     */
    public function getDisplayUrlFaqAttribute(): string
    {
        return SitePublicRoutesEnum::PostFaq->url([
            'faqPost' => $this[TableEnum::Id->dbName()],
            'slug' => $this->UrlSlug,
        ]);
    }
    /**************** Accessors & Mutators END ********************/

    /**************** scopes Collection ********************/

    /**
     * Get scope of published posts
     *
     * @param  mixed $query
     * @return Builder
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where(TableEnum::IsPublished->dbName(), 1);
    }

    /**
     * Get scope of published posts with sort
     *
     * @param  mixed $query
     * @return Builder
     */
    public function scopePublishedWithSort(Builder $query): Builder
    {
        return $query
            ->Published()
            ->orderBy(TableEnum::IsPinned->dbName(), 'desc')
            ->orderBy(TableEnum::PinNumber->dbName(), 'asc')
            ->orderBy(TimestampsEnum::UpdatedAt->dbName(), 'desc')
            ->orderBy(TimestampsEnum::CreatedAt->dbName(), 'desc');
    }

    /**
     * Scope a collection of scopes for get all items.
     *
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAllItems(Builder $query, array $filter): Builder
    {
        return $query
            ->Id($filter)
            ->PostSpaceId($filter)
            ->Title($filter)
            ->Template($filter)
            ->Content($filter)
            ->ShortenedContent($filter)
            ->IsPublished($filter)
            ->PrivateNote($filter)
            ->CreatedAt($filter)
            ->Author($filter)
            ->ContentUpdatedAt($filter)
            ->Views($filter)
            ->PinNumber($filter)
            ->Editor($filter);
    }

    /**
     * Scope a collection of scopes for the "Controller->apiIndex" function.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApiIndexCollection(Builder $query, array $filter): Builder
    {
        $postsTable = DatabaseTablesEnum::Posts;
        $postGroupsTable = DatabaseTablesEnum::PostGroups;
        $usersTabel = DatabaseTablesEnum::Users;

        $postSpaceIdKey = TableEnum::PostSpaceId->dbName();
        $authorIdKey = TableEnum::AuthorId->dbName();
        $editorIdKey = TableEnum::EditorId->dbName();
        $usersIdKey = UsersTableEnum::Id->dbName();
        $usernameKey = UsersTableEnum::Username->dbName();

        return $query
            ->AllItems($filter)
            ->join($postGroupsTable->tableName(), PostGroupsTableEnum::Id->dbNameWithTable($postGroupsTable), "=", $postSpaceIdKey)
            ->leftJoin($usersTabel->tableName() . " as author", "author." . $usersIdKey, "=", $authorIdKey)
            ->leftJoin($usersTabel->tableName() . " as editor", "editor." . $usersIdKey, "=", $editorIdKey)
            ->select(
                $postsTable->tableName() . '.*',

                PostGroupsTableEnum::Title->dbNameWithTable($postGroupsTable) . ' as space_title',
                PostGroupsTableEnum::Template->dbNameWithTable($postGroupsTable),

                'author.' . $usernameKey . ' as autor_username',
                'editor.' . $usernameKey . ' as editor_username',

            )
            ->SortOrder($filter, [
                $postSpaceIdKey    => 'space_title',
                $authorIdKey       => 'autor_username',
                $editorIdKey       => 'editor_username',
            ]);
    }
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
                ->orderBy(TableEnum::Id->dbName(), 'desc');
        }, $replaceSortFields);
    }


    /**
     * Scope a query to only include "id" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeId(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeExactlyNumber(TableEnum::Id->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "post_space_id" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePostSpaceId(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeDropboxId(TableEnum::PostSpaceId->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "title" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTitle(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeLikeAs(TableEnum::Title->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "content" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeContent(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeLikeAs(TableEnum::Content->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "content" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShortenedContent(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeLikeAs(TableEnum::Content->dbName(), $query, $filter, TableEnum::ShortenedContentForTable->dbName());
    }

    /**
     * Scope a query to only include "is_published" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsPublished(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeCheckbox(TableEnum::IsPublished->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "private_note" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePrivateNote(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeLikeAs(TableEnum::PrivateNote->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "created_at" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedAt(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeDateRange(TimestampsEnum::CreatedAt->dbName(), $query, $filter, null, User::authUser()->getCalendarHelper());
    }

    /**
     * Scope a query to only include "author_id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAuthor(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeDropboxId(TableEnum::AuthorId->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "content_updated_at" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeContentUpdatedAt(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeDateRange(TableEnum::ContentUpdatedAt->dbName(), $query, $filter, null, User::authUser()->getCalendarHelper());
    }

    /**
     * Scope a query to only include "views" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeViews(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeNumberRange(TableEnum::Views->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "pin_number" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePinNumber(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeNumberRange(TableEnum::PinNumber->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "editor_id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEditor(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeDropboxId(TableEnum::EditorId->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "template" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTemplate(Builder $query, array $filter = null): Builder
    {
        $filterKey = PostGroupsTableEnum::Template->dbName();
        $dbCol = PostGroupsTableEnum::Template->dbNameWithTable(DatabaseTablesEnum::PostGroups);

        return $this->superScopeDropbox($dbCol, $query, $filter, $filterKey);
    }
    /**************** scopes END ********************/
}
