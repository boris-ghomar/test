<?php

namespace App\Models\BackOffice\Referral;

use App\Enums\Database\Tables\ReferralRewardItemsTableEnum as TableEnum;
use App\Enums\Database\Tables\ReferralRewardPackagesTableEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\Models\SuperClasses\SuperModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferralRewardItem extends SuperModel
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
            TableEnum::PackageId->dbName(),
            TableEnum::Name->dbName(),
            TableEnum::DisplayName->dbName(),
            TableEnum::Type->dbName(),
            TableEnum::BonusId->dbName(),
            TableEnum::Percentage->dbName(),
            TableEnum::IsActive->dbName(),
            TableEnum::DisplayPriority->dbName(),
            TableEnum::PaymentPriority->dbName(),
            TableEnum::PrivateNote->dbName(),
        ];

        $this->attributes = [
            TableEnum::IsActive->dbName() => 0,
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
     * Get the referralRewardPackage that owns the ReferralRewardItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referralRewardPackage(): BelongsTo
    {
        return $this->belongsTo(ReferralRewardPackage::class, TableEnum::PackageId->dbName(), ReferralRewardPackagesTableEnum::Id->dbName());
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
            ->PackageId($filter)
            ->Name($filter)
            ->DisplayName($filter)
            ->Type($filter)
            ->BonusId($filter)
            ->Percentage($filter)
            ->IsActive($filter)
            ->DisplayPriority($filter)
            ->PaymentPriority($filter)
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
                ->orderBy(TableEnum::PackageId->dbName(), 'desc')
                ->orderBy(TableEnum::DisplayPriority->dbName(), 'asc');
        }, $replaceSortFields);
    }

    /**
     * Scope a query to only include "package_id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePackageId(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeDropboxId(TableEnum::PackageId->dbName(), $query, $filter);
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
     * Scope a query to only include "type" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeType(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeDropbox(TableEnum::Type->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "bonus_id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBonusId(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeExactly(TableEnum::BonusId->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "percentage" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePercentage(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeNumberRange(TableEnum::Percentage->dbName(), $query, $filter);
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
     * Scope a query to only include "display_priority" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDisplayPriority(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeExactlyNumber(TableEnum::DisplayPriority->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "payment_priority" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaymentPriority(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeExactlyNumber(TableEnum::PaymentPriority->dbName(), $query, $filter);
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
