<?php

namespace App\Models\BackOffice\Referral;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\ReferralsTableEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\Models\SuperClasses\SuperModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Referral extends SuperModel
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
            TableEnum::UserId->dbName(),
            TableEnum::ReferredBy->dbName(),
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

        self::creating(function (self $model) {

            $model[TableEnum::ReferralId->dbName()] = self::getNewReferralId();
            return $model;
        });
    }

    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get the user that owns the Referral
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, TableEnum::UserId->dbName(), UsersTableEnum::Id->dbName());
    }

    /**
     * Get the user that referred this user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referredByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, TableEnum::ReferredBy->dbName(), UsersTableEnum::Id->dbName());
    }

    /**
     * Get all of the referrals for the referrer user referral
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(self::class, TableEnum::ReferredBy->dbName(), TableEnum::UserId->dbName());
    }
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/

    /**
     * Get new unique referral ID
     *
     * @return string
     */
    private static function getNewReferralId(): string
    {
        $referralId = strtolower(Str::random(16));

        if (!is_null(self::where(TableEnum::ReferralId->dbName(), $referralId)->first()))
            return self::getNewReferralId();

        return $referralId;
    }
    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/
    /**
     * Interact with the ReferralSession's StartedAt.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function createdAt(): Attribute
    {
        $user = User::authUser();

        return Attribute::make(
            get: fn (?string $value) => is_null($user) ? $value : $user->convertUTCToLocalTime($value),
            // set: fn (string $value) => is_null($user) ? $value : $user->convertLocalTimeToUTC($value), No need to set
        );
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
            ->UserId($filter)
            ->ReferredBy($filter)
            ->CreatedAt($filter);
    }

    /**
     * Scope a collection of scopes for get all items for controller list.
     *
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param array $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeControllerAllItems(Builder $query, array $filter): Builder
    {
        return $query
            ->AllItems($filter)

            ->ReferredBcId($filter)
            ->ReferredUsername($filter)

            ->ReferrerBcId($filter)
            ->ReferrerUsername($filter);
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
        $thisTable = DatabaseTablesEnum::Referrals;
        $userExtraTable = DatabaseTablesEnum::BetconstructClients;

        return $query
            ->ControllerAllItems($filter)
            ->join($userExtraTable->tableName() . ' as referred', 'referred.' . ClientModelEnum::UserId->dbName(), '=', TableEnum::UserId->dbNameWithTable($thisTable))
            ->leftJoin($userExtraTable->tableName() . ' as referrer', 'referrer.' . ClientModelEnum::UserId->dbName(), '=', TableEnum::ReferredBy->dbNameWithTable($thisTable))
            ->select(
                TableEnum::Id->dbNameWithTable($thisTable),
                TableEnum::UserId->dbNameWithTable($thisTable),
                TableEnum::ReferredBy->dbNameWithTable($thisTable),
                TimestampsEnum::CreatedAt->dbNameWithTable($thisTable),

                sprintf('referred.%s as referred_bc_id', ClientModelEnum::Id->dbName()),
                sprintf('referred.%s as referred_username', ClientModelEnum::Login->dbName()),

                sprintf('referrer.%s as referrer_bc_id', ClientModelEnum::Id->dbName()),
                sprintf('referrer.%s as referrer_username', ClientModelEnum::Login->dbName()),

            )
            ->SortOrder($filter);
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
                ->orderBy(TimestampsEnum::CreatedAt->dbName(), 'desc');
        }, $replaceSortFields);
    }

    /**
     * Scope a query to only include "user_id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUserId(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeExactlyNumber(TableEnum::UserId->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "referred_by" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReferredBy(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeExactlyNumber(TableEnum::ReferredBy->dbName(), $query, $filter);
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

    /**************** Joined Items ********************/
    /**
     * Scope a query to only include "referred_bc_id" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReferredBcId(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'referred_bc_id';
        $dbCol = 'referred.' . ClientModelEnum::Id->dbName();

        return $this->superScopeExactly($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "referred_username" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReferredUsername(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'referred_username';
        $dbCol = 'referred.' . ClientModelEnum::Login->dbName();

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "referrer_bc_id" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReferrerBcId(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'referrer_bc_id';
        $dbCol = 'referrer.' . ClientModelEnum::Id->dbName();

        return $this->superScopeExactly($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "referrer_username" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReferrerUsername(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'referrer_username';
        $dbCol = 'referrer.' . ClientModelEnum::Login->dbName();

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }
    /**************** Joined Items END ********************/

    /**************** scopes END ********************/
}
