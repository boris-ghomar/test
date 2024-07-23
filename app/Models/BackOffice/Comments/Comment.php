<?php

namespace App\Models\BackOffice\Comments;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\CommentsTableEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\Models\Site\UserActions\Comment as UserActionsComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class Comment extends UserActionsComment
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
        parent::__construct($attributes);

        $this->fillable = [
            TableEnum::CommentableId->dbName(),
            TableEnum::UserId->dbName(),
            TableEnum::Comment->dbName(),
            TableEnum::IsApproved->dbName(),
        ];

        $this->casts = [
            TableEnum::IsApproved->dbName() => 'boolean',
            TableEnum::IsAdminAnswer->dbName() => 'boolean',
            TableEnum::IsNotifiedPublished->dbName() => 'boolean',
            TableEnum::IsNotifiedCommentableOwner->dbName() => 'boolean',
        ];
    }


    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/
    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/

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
    public function getUpdatedAtAttribute(mixed $value): ?string
    {
        return User::authUser()->convertUTCToLocalTime($value);
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
            ->Id($filter)
            ->PostId($filter)
            ->UserId($filter)
            ->Comment($filter)
            ->IsApproved($filter)
            ->CreatedAt($filter)
            ->ApprovedBy($filter)
            ->UpdatedAt($filter)
            ->OwnerUsername($filter);
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
        $commentsTabel = DatabaseTablesEnum::Comments;
        $usersTabel = DatabaseTablesEnum::Users;
        $betconstructClientsTabel = DatabaseTablesEnum::BetconstructClients;

        $isAdminAnswerKey = TableEnum::IsAdminAnswer->dbName();
        $approvedByKey = TableEnum::ApprovedBy->dbName();
        $ownerKey = TableEnum::UserId->dbNameWithTable($commentsTabel);
        $usersIdKey = UsersTableEnum::Id->dbName();
        $usernameKey = UsersTableEnum::Username->dbName();
        $clientUserIdKey = ClientModelEnum::UserId->dbName();
        $clientUsernameKey = ClientModelEnum::Login->dbName();

        return $query
            ->AllItems($filter)
            ->where($isAdminAnswerKey, 0)
            ->leftJoin($betconstructClientsTabel->tableName() . " as client_extra", "client_extra." . $clientUserIdKey, "=", $ownerKey)
            ->leftJoin($usersTabel->tableName() . " as owner", "owner." . $usersIdKey, "=", $ownerKey)
            ->leftJoin($usersTabel->tableName() . " as approver", "approver." . $usersIdKey, "=", $approvedByKey)
            ->select(
                $commentsTabel->tableName() . '.*',

                'owner.' . $usernameKey . ' as owner_username',
                'approver.' . $usernameKey . ' as approver_username',
                'client_extra.' . $clientUsernameKey . ' as client_username',
            )
            ->SortOrder($filter, [
                $approvedByKey  => 'approver_username',
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
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOrder(Builder $query, ?array $filter = null, array $replaceSortFields = []): Builder
    {
        return $this->superScopeSortOrder($query, $filter, function ($query) {
            return $query
                ->orderBy(TableEnum::IsApproved->dbName(), 'asc')
                ->orderBy(TimestampsEnum::CreatedAt->dbName(), 'asc');
        }, $replaceSortFields);
    }

    /**
     * Scope a query to only include "id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeId(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeExactlyNumber(TableEnum::Id->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "user_id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUserId(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeExactlyNumber(TableEnum::UserId->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "post_id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePostId(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeExactlyNumber(TableEnum::PostId->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "comment" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeComment(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeLikeAs(TableEnum::Comment->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "is_approved" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsApproved(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeCheckbox(TableEnum::IsApproved->dbName(), $query, $filter);
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
     * Scope a query to only include "approved_by" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApprovedBy(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeDropboxId(TableEnum::ApprovedBy->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "updated_at" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpdatedAt(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeDateRange(TimestampsEnum::UpdatedAt->dbName(), $query, $filter, null, User::authUser()->getCalendarHelper());
    }

    /**
     * Scope a query to only include "OwnerUsername" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOwnerUsername(Builder $query, array $filter = null): Builder
    {
        $filterKey = "OwnerUsername";

        if (isset($filter[$filterKey])) {

            $username = $filter[$filterKey];

            if (!empty($username)) {

                $usernameKey = UsersTableEnum::Username->dbName();
                $clientUsernameKey = ClientModelEnum::Login->dbName();

                return $query->where('owner.' . $usernameKey, 'like', '%' . $username . '%')
                    ->orWhere('client_extra.' . $clientUsernameKey, 'like', '%' . $username . '%');
            }
        }

        return $query;
    }
    /**************** scopes END ********************/
}
