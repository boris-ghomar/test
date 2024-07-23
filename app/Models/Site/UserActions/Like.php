<?php

namespace App\Models\Site\UserActions;

use App\Enums\Database\Tables\LikesTableEnum as TableEnum;
use App\Enums\Database\Tables\PostsTableEnum;
use App\Enums\UserActions\LikableTypesEnum;
use App\Models\BackOffice\Posts\Post;
use App\Models\SuperClasses\SuperModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Like extends SuperModel
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

        $this->timestamps = false;

        parent::__construct($attributes);
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get the likable that owns the Like
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function likable(): ?BelongsTo
    {
        $likableClass = match ($this[TableEnum::LikableType->dbName()]) {

            LikableTypesEnum::Post->name    => Post::class,
            LikableTypesEnum::Comment->name => Comment::class,

            default => null
        };

        if (!is_null($likableClass)) {

            return $this->belongsTo($likableClass, TableEnum::LikableId->dbName(), PostsTableEnum::Id->dbName());
        }

        return null;
    }

    /**
     * Get the post that owns the Like
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|null
     */
    public function post(): ?BelongsTo
    {
        $likable = $this->likable;

        if (is_null($likable))
            return null;

        return ($likable instanceof Post) ? $this->likable() : $likable->post();
    }
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/
    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/
    /**************** Accessors & Mutators END ********************/

    /**************** scopes Collection ********************/

    /**
     * Get likes of Posts
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return ?Builder
     */
    public function scopePostLikes(Builder $query): ?Builder
    {
        return $query->where(TableEnum::LikableType->dbName(), LikableTypesEnum::Post->name);
    }

    /**
     * Get likes of Comments
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return ?Builder
     */
    public function scopeCommentLikes(Builder $query): ?Builder
    {
        return $query->where(TableEnum::LikableType->dbName(), LikableTypesEnum::Comment->name);
    }
    /**************** scopes Collection END ********************/

    /**************** scopes ********************/
    /**************** scopes END ********************/
}
