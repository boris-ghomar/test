<?php

namespace App\Models\Site\UserActions;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\CommentsTableEnum as TableEnum;
use App\Enums\Database\Tables\LikesTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\UserActions\CommentableTypesEnum;
use App\Enums\UserActions\LikableTypesEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\Models\BackOffice\Posts\Post;
use App\Models\SuperClasses\SuperModel;
use App\Models\User;
use App\Notifications\General\UserActions\YourCommentPublishedNotification;
use App\Notifications\General\UserActions\YourCommentRepliedNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use function Illuminate\Events\queueable;

class Comment extends SuperModel
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
        $this->fillable = [];

        parent::__construct($attributes);

        $this->casts = [
            TableEnum::IsApproved->dbName() => 'boolean',
            TableEnum::IsAdminAnswer->dbName() => 'boolean',
            TableEnum::IsNotifiedPublished->dbName() => 'boolean',
            TableEnum::IsNotifiedCommentableOwner->dbName() => 'boolean',
        ];
    }

    /**
     * @override parent boot
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::saving(function (self $model) {

            // Avoid of intering html and javascript tags in comment
            $commentKey = TableEnum::Comment->dbName();
            $comment = $model[$commentKey];
            $model[$commentKey] = strip_tags($comment);

            return $model;
        });

        self::saved(queueable(function (self $model) {

            $model->notifyCommentOwnerPublished();
            $model->notifyUserCommentReplied();
        }));
    }

    /**
     * Notify the commenter that the comment has been published
     *
     * @return void
     */
    public function notifyCommentOwnerPublished(): void
    {
        // Send notification if not notified before
        if ($this[TableEnum::IsNotifiedPublished->dbName()])
            return;

        // Send notification after comment approved
        if (!$this[TableEnum::IsApproved->dbName()])
            return;

        // Send Notification
        $owner = $this->user;
        if (!$owner->isPersonnel()) {

            $owner->notify(new YourCommentPublishedNotification($this[TableEnum::Id->dbName()]));
            $this[TableEnum::IsNotifiedPublished->dbName()] = 1;
            $this->save();
        }
    }

    /**
     * Notify the user that his comment has been replied
     *
     * @return void
     */
    public function notifyUserCommentReplied(): void
    {

        // Send notification if not notified before
        if ($this[TableEnum::IsNotifiedCommentableOwner->dbName()])
            return;

        // Send notification after comment approved
        if (!$this[TableEnum::IsApproved->dbName()])
            return;

        // Only replies to comments will be notified
        if ($this[TableEnum::CommentableType->dbName()] !== CommentableTypesEnum::Comment->name)
            return;

        // Send Notification
        $commentableOwner = $this->commentable->user;

        // If the user replies to his own comment, there is no need to send a notification
        if ($this->user->id != $commentableOwner->id) {

            $commentableOwner->notify(new YourCommentRepliedNotification($this[TableEnum::Id->dbName()]));
            $this[TableEnum::IsNotifiedCommentableOwner->dbName()] = 1;
            $this->save();
        }
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get the commentable that owns the comment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|null
     */
    public function commentable(): ?BelongsTo
    {
        $commentableClass = match ($this[TableEnum::CommentableType->dbName()]) {

            CommentableTypesEnum::Post->name => Post::class,
            CommentableTypesEnum::Comment->name => Comment::class,

            default => null
        };

        if (!is_null($commentableClass)) {

            return $this->belongsTo($commentableClass, TableEnum::CommentableId->dbName(), 'id');
        }

        return null;
    }

    /**
     * Get the post that owns the Comment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|null
     */
    public function post(): ?BelongsTo
    {
        // Do not use post_id for relation: it is not safe! it is just for search
        // Try to find relation by Post ID
        /* if($postId = $this[TableEnum::PostId->dbName()]){

            if(Post::find($postId)){
                return $this->belongsTo(Post::class, TableEnum::PostId->dbName(), 'id');
            }else{
                $this->delete();
            }
        } */

        $commentable = $this->commentable;

        if (is_null($commentable))
            return null;

        return ($commentable instanceof Post) ? $this->commentable() : $commentable->post();
    }

    /**
     * Get all of the likes for the Comment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class, LikesTableEnum::LikableId->dbName(), TableEnum::Id->dbName())
            ->where(LikesTableEnum::LikableType->dbName(), LikableTypesEnum::Comment->name);
    }

    /**
     * Get all of the comments for the Comment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, TableEnum::CommentableId->dbName(), TableEnum::Id->dbName())
            ->where(TableEnum::CommentableType->dbName(), CommentableTypesEnum::Comment->name);
    }

    /**
     * Get the user that owns the Comment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, TableEnum::UserId->dbName(), UsersTableEnum::Id->dbName())
            ->withTrashed();
    }

    /**
     * Get admin answer for comment
     *
     * @return HasOne
     */
    public function adminAnswer(): HasOne
    {
        return $this->hasOne(Comment::class, TableEnum::CommentableId->dbName(), TableEnum::Id->dbName())
            ->where(TableEnum::IsAdminAnswer->dbName(), 1)
            ->where(TableEnum::CommentableType->dbName(), CommentableTypesEnum::Comment->name)
            ->orderBy(TimestampsEnum::UpdatedAt->dbName(), 'desc');
    }
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/
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
    public function setIsApprovedAttribute(mixed $value): void
    {
        $this->attributes[TableEnum::IsApproved->dbName()] = CastEnum::Boolean->cast($value);
    }

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
    public function setIsAdminAnswerAttribute(mixed $value): void
    {
        $this->attributes[TableEnum::IsAdminAnswer->dbName()] = CastEnum::Boolean->cast($value);
    }

    /**
     * Get HTML view id of comment
     *
     * @return string
     */
    public function getHtmlViewIdAttribute(): string
    {
        return sprintf("comment_view_%s", $this[TableEnum::Id->dbName()]);
    }

    /**
     * Get the display comment URL
     *
     * @return ?string
     */
    public function getDisplayUrlAttribute(): ?string
    {
        $postUrl = $this->post->DisplayUrl;
        return sprintf('%s#%s_anchor_link', $postUrl, $this->HtmlViewId);
    }

    /**
     * Get the dispaly name of comment's owner
     *
     * @return string
     */
    public function getOwnerDispalyNameAttribute(): string
    {
        $owner = $this->user;
        $displayName = $owner[UsersTableEnum::DisplayName->dbName()];

        if ($owner->isPersonnel()) {
            $displayName = sprintf('%s [ %s ]', $displayName, __('thisApp.SiteAdmin'));
        }

        return $displayName;
    }
    /**************** Accessors & Mutators END ********************/

    /**************** scopes Collection ********************/

    /**
     * Get approved comments
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return ?Builder
     */
    public function scopeApproved(Builder $query)
    {
        return $query->where(TableEnum::IsApproved->dbName(), 1);
    }

    /**
     * Get not approved comments
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return ?Builder
     */
    public function scopeNotApproved(Builder $query)
    {
        return $query->where(TableEnum::IsApproved->dbName(), 0);
    }

    /**
     * Get comments of Posts
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return ?Builder
     */
    public function scopePostComments(Builder $query): ?Builder
    {
        return $query->where(TableEnum::CommentableType->dbName(), CommentableTypesEnum::Post->name);
    }

    /**
     * Get comments of Comment (Reply)
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return ?Builder
     */
    public function scopeCommentComments(Builder $query): ?Builder
    {
        return $query->where(TableEnum::CommentableType->dbName(), CommentableTypesEnum::Comment->name);
    }
    /**************** scopes Collection END ********************/

    /**************** scopes ********************/
    /**************** scopes END ********************/
}
