<?php

namespace App\Models\BackOffice\Referral;

use App\Enums\Database\Tables\ReferralBetsConclusionsTableEnum as TableEnum;
use App\Enums\Database\Tables\ReferralsTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Models\SuperClasses\SuperModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralBetsConclusion extends SuperModel
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
        $this->fillable = [
            TableEnum::Descr->dbName(),
        ];

        parent::__construct($attributes);
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/
    /**
     * Get the "referrer user" that owns the ReferralReward
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referrerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, TableEnum::ReferrerId->dbName(), UsersTableEnum::Id->dbName());
    }

    /**
     * Get the "referred user" that owns the ReferralReward
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, TableEnum::ReferredId->dbName(), UsersTableEnum::Id->dbName());
    }

    /**
     * Get the "referrer Referral" that owns the ReferralReward
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referrerReferral(): BelongsTo
    {
        return $this->belongsTo(Referral::class, TableEnum::ReferrerId->dbName(), ReferralsTableEnum::UserId->dbName());
    }

    /**
     * Get the "referred Referral" that owns the ReferralReward
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referredReferral(): BelongsTo
    {
        return $this->belongsTo(Referral::class, TableEnum::ReferredId->dbName(), ReferralsTableEnum::UserId->dbName());
    }
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/
    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/
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
            ->Description($filter);
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
        return $query
            ->AllItems($filter)
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
                ->orderBy(TableEnum::CalculatedAt->dbName(), 'desc');
        }, $replaceSortFields);
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
    /**************** scopes END ********************/
}
