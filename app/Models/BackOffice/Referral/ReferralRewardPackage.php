<?php

namespace App\Models\BackOffice\Referral;

use App\Enums\Database\Tables\ReferralRewardItemsTableEnum;
use App\Enums\Database\Tables\ReferralRewardPackagesTableEnum as TableEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\Models\SuperClasses\SuperModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferralRewardPackage extends SuperModel
{
    use SoftDeletes;

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
            TableEnum::Name->dbName(),
            TableEnum::DisplayName->dbName(),
            TableEnum::ClaimCount->dbName(),
            TableEnum::Descr->dbName(),
            TableEnum::IsActive->dbName(),

            TableEnum::MinBetCountReferrer->dbName(),
            TableEnum::MinBetOddsReferrer->dbName(),
            TableEnum::MinBetAmountUsdReferrer->dbName(),
            TableEnum::MinBetAmountIrrReferrer->dbName(),

            TableEnum::MinBetCountReferred->dbName(),
            TableEnum::MinBetOddsReferred->dbName(),
            TableEnum::MinBetAmountUsdReferred->dbName(),
            TableEnum::MinBetAmountIrrReferred->dbName(),

            TableEnum::PrivateNote->dbName(),
        ];

        $this->attributes = [
            TableEnum::IsActive->dbName() => 0,
            TableEnum::ClaimCount->dbName() => 1,

            TableEnum::MinBetCountReferrer->dbName()        => 0,
            TableEnum::MinBetOddsReferrer->dbName()         => 1,
            TableEnum::MinBetAmountUsdReferrer->dbName()    => 0,
            TableEnum::MinBetAmountIrrReferrer->dbName()    => 0,

            TableEnum::MinBetCountReferred->dbName()        => 0,
            TableEnum::MinBetOddsReferred->dbName()         => 1,
            TableEnum::MinBetAmountUsdReferred->dbName()    => 0,
            TableEnum::MinBetAmountIrrReferred->dbName()    => 0,
        ];

        $this->casts = [
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

        self::softDeleted(function (self $model) {

            // Rename the soft deleted item
            $nameCol = TableEnum::Name->dbName();

            $deletedName = sprintf("%s[deleted-%s]", $model->$nameCol, $model->id);

            $model[$nameCol] = $deletedName;
            $model->save();

            return $model;
        });
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get all of the referralRewardItems for the ReferralRewardPackage
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function referralRewardItems(): HasMany
    {
        return $this->hasMany(ReferralRewardItem::class, ReferralRewardItemsTableEnum::PackageId->dbName(), TableEnum::Id->dbName());
    }

    /**
     * Get all of the active referralRewardItems for the ReferralRewardPackage
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function referralRewardItemsActive(): HasMany
    {
        return $this->referralRewardItems()
            ->where(ReferralRewardItemsTableEnum::IsActive->dbName(), 1);
    }
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/
    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/

    /**
     * Interact with the verification's IsVerified.
     *
     * Convert string boolean to boolean when saving information.
     * When receiving information from the API client,
     * boolean values may be received in the form of strings.
     *
     * Example:
     *   "true" => true
     *   "false" => false
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function isActive(): Attribute
    {
        return Attribute::make(
            get: fn (bool|int|string $value)    => CastEnum::Boolean->cast($value),
            set: fn (bool|int|string $value)    => CastEnum::Boolean->cast($value),
        );
    }

    /********************************** Referrer Items **********************************/
    /**
     * Interact with the ReferralSession's MinBetCountReferrer.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function minBetCountReferrer(): Attribute
    {
        return Attribute::make(
            get: fn (int|null $value)           => CastEnum::Int->cast(empty($value) ? 0 : $value),
            set: fn (int|string|null $value)    => CastEnum::Int->cast(empty($value) ? 0 : $value),
        );
    }

    /**
     * Interact with the ReferralSession's MinBetOddsReferrer.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function minBetOddsReferrer(): Attribute
    {
        return Attribute::make(
            get: fn (float|null $value)         => round(CastEnum::Float->cast(empty($value) ? 1 : $value), 2),
            set: fn (float|string|null $value)  => round(CastEnum::Float->cast(empty($value) ? 1 : $value), 2),
        );
    }

    /**
     * Interact with the ReferralSession's MinBetAmountUsdReferrer.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function minBetAmountUsdReferrer(): Attribute
    {
        return Attribute::make(
            get: fn (float|null $value)         => round(CastEnum::Float->cast(empty($value) ? 0 : $value), 2),
            set: fn (float|string|null $value)  => round(CastEnum::Float->cast(empty($value) ? 0 : $value), 2),
        );
    }

    /**
     * Interact with the ReferralSession's MinBetAmountIrrReferrer.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function minBetAmountIrrReferrer(): Attribute
    {
        return Attribute::make(
            get: fn (float|null $value)         => round(CastEnum::Float->cast(empty($value) ? 0 : $value), 2),
            set: fn (float|string|null $value)  => round(CastEnum::Float->cast(empty($value) ? 0 : $value), 2),
        );
    }

    /********************************** Referrer Items END **********************************/

    /********************************** Referred Items **********************************/

    /**
     * Interact with the ReferralSession's MinBetCountReferred.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function minBetCountReferred(): Attribute
    {
        return Attribute::make(
            get: fn (int|null $value)           => CastEnum::Int->cast(empty($value) ? 0 : $value),
            set: fn (int|string|null $value)    => CastEnum::Int->cast(empty($value) ? 0 : $value),
        );
    }

    /**
     * Interact with the ReferralSession's MinBetOddsReferred.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function minBetOddsReferred(): Attribute
    {
        return Attribute::make(
            get: fn (float|null $value)         => round(CastEnum::Float->cast(empty($value) ? 1 : $value), 2),
            set: fn (float|string|null $value)  => round(CastEnum::Float->cast(empty($value) ? 1 : $value), 2),
        );
    }

    /**
     * Interact with the ReferralSession's MinBetAmountUsdReferred.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function minBetAmountUsdReferred(): Attribute
    {
        return Attribute::make(
            get: fn (float|null $value)         => round(CastEnum::Float->cast(empty($value) ? 0 : $value), 2),
            set: fn (float|string|null $value)  => round(CastEnum::Float->cast(empty($value) ? 0 : $value), 2),
        );
    }

    /**
     * Interact with the ReferralSession's MinBetAmountIrrReferred.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function minBetAmountIrrReferred(): Attribute
    {
        return Attribute::make(
            get: fn (float|null $value)         => round(CastEnum::Float->cast(empty($value) ? 0 : $value), 2),
            set: fn (float|string|null $value)  => round(CastEnum::Float->cast(empty($value) ? 0 : $value), 2),
        );
    }
    /********************************** Referred Items END **********************************/

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
            ->Name($filter)
            ->DisplayName($filter)
            ->ClaimCount($filter)
            ->Description($filter)
            ->IsActive($filter)

            ->MinBetCountReferrer($filter)
            ->MinBetOddsReferrer($filter)
            ->MinBetAmountUsdReferrer($filter)
            ->MinBetAmountIrrReferrer($filter)

            ->MinBetCountReferred($filter)
            ->MinBetOddsReferred($filter)
            ->MinBetAmountUsdReferred($filter)
            ->MinBetAmountIrrReferred($filter)

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
                ->orderBy(TableEnum::IsActive->dbName(), 'desc')
                ->orderBy(TableEnum::Name->dbName(), 'asc');
        }, $replaceSortFields);
    }

    /**
     * Scope a query to only include "name" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeName(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeLikeAs(TableEnum::Name->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "display_name" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDisplayName(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeLikeAs(TableEnum::DisplayName->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "claim_count" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeClaimCount(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeNumberRange(TableEnum::ClaimCount->dbName(), $query, $filter);
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

    /**
     * Scope a query to only include "is_active" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsActive(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeCheckbox(TableEnum::IsActive->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "min_bet_count_referrer" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMinBetCountReferrer(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeNumberRange(TableEnum::MinBetCountReferrer->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "min_bet_odds_referrer" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMinBetOddsReferrer(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeNumberRange(TableEnum::MinBetOddsReferrer->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "min_bet_amount_usd_referrer" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMinBetAmountUsdReferrer(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeNumberRange(TableEnum::MinBetAmountUsdReferrer->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "min_bet_amount_irr_referrer" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMinBetAmountIrrReferrer(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeNumberRange(TableEnum::MinBetAmountIrrReferrer->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "min_bet_count_referred" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMinBetCountReferred(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeNumberRange(TableEnum::MinBetCountReferred->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "min_bet_odds_referred" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMinBetOddsReferred(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeNumberRange(TableEnum::MinBetOddsReferred->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "min_bet_amount_usd_referred" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMinBetAmountUsdReferred(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeNumberRange(TableEnum::MinBetAmountUsdReferred->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "min_bet_amount_irr_referred" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMinBetAmountIrrReferred(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeNumberRange(TableEnum::MinBetAmountIrrReferred->dbName(), $query, $filter);
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
    /**************** scopes END ********************/
}
