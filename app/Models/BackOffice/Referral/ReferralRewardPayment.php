<?php

namespace App\Models\BackOffice\Referral;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\ReferralRewardConclusionsTableEnum;
use App\Enums\Database\Tables\ReferralRewardItemsTableEnum;
use App\Enums\Database\Tables\ReferralRewardPaymentsTableEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\Models\SuperClasses\SuperModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralRewardPayment extends SuperModel
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
            TableEnum::Descr->dbName(),
        ];

        parent::__construct($attributes);
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get the referralRewardConclusion that owns the ReferralRewardPayment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, TableEnum::UserId->dbName(), UsersTableEnum::Id->dbName());
    }

    /**
     * Get the referralRewardConclusion that owns the ReferralRewardPayment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referralRewardConclusion(): BelongsTo
    {
        return $this->belongsTo(ReferralRewardConclusion::class, TableEnum::RewardConclusionsId->dbName(), ReferralRewardConclusionsTableEnum::Id->dbName());
    }

    /**
     * Get the referralRewardItem that owns the ReferralRewardPayment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referralRewardItem(): BelongsTo
    {
        return $this->belongsTo(ReferralRewardItem::class, TableEnum::RewardItemId->dbName(), ReferralRewardItemsTableEnum::Id->dbName());
    }
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/
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
            ->Id($filter)
            ->UserId($filter)
            ->Amount($filter)
            ->IsDone($filter)
            ->IsSuccessful($filter)
            ->CreatedAt($filter)
            ->SystemMessage($filter)
            ->Description($filter);
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
            ->BcId($filter)
            ->BcUsername($filter)
            ->CurrencyId($filter)

            ->RewardName($filter);
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
        $thisTable = DatabaseTablesEnum::ReferralRewardPayments;
        $userExtraTable = DatabaseTablesEnum::BetconstructClients;
        $referralRewardItemsTable = DatabaseTablesEnum::ReferralRewardItems;

        return $query
            ->ControllerAllItems($filter)
            ->join($userExtraTable->tableName(), ClientModelEnum::UserId->dbNameWithTable($userExtraTable), '=', TableEnum::UserId->dbNameWithTable($thisTable))
            ->join($referralRewardItemsTable->tableName(), ReferralRewardItemsTableEnum::Id->dbNameWithTable($referralRewardItemsTable), '=', TableEnum::RewardItemId->dbNameWithTable($thisTable))
            ->select(
                $thisTable->tableName() . '.*',

                ClientModelEnum::Id->dbNameWithTable($userExtraTable) . ' as bc_id',
                ClientModelEnum::Login->dbNameWithTable($userExtraTable) . ' as bc_username',
                ClientModelEnum::CurrencyId->dbNameWithTable($userExtraTable),

                ReferralRewardItemsTableEnum::Name->dbNameWithTable($referralRewardItemsTable) . ' as reward_name',
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
     * Scope a query to only include "id" as request.
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
     * Scope a query to only include "amount" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAmount(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeNumberRange(TableEnum::Amount->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "is_done" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsDone(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeCheckbox(TableEnum::IsDone->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "is_successful" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsSuccessful(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeCheckbox(TableEnum::IsSuccessful->dbName(), $query, $filter);
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
     * Scope a query to only include "system_message" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSystemMessage(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeLikeAs(TableEnum::SystemMessage->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "descr" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDescription(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeLikeAs(TableEnum::Descr->dbName(), $query, $filter);
    }

    /**************** Joined Items ********************/
    /**
     * Scope a query to only include "bc_id" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBcId(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'bc_id';
        $dbCol = ClientModelEnum::Id->dbNameWithTable(DatabaseTablesEnum::BetconstructClients);

        return $this->superScopeExactly($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "bc_username" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBcUsername(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'bc_username';
        $dbCol = ClientModelEnum::Login->dbNameWithTable(DatabaseTablesEnum::BetconstructClients);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "currency_id" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrencyId(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = ClientModelEnum::CurrencyId->dbName();
        $dbCol = ClientModelEnum::CurrencyId->dbNameWithTable(DatabaseTablesEnum::BetconstructClients);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "reward_name" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRewardName(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'reward_name';
        $dbCol = ReferralRewardItemsTableEnum::Name->dbNameWithTable(DatabaseTablesEnum::ReferralRewardItems);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }
    /**************** Joined Items END********************/

    /**************** scopes END ********************/
}
