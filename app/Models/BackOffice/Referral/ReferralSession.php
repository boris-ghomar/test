<?php

namespace App\Models\BackOffice\Referral;

use App\Enums\Database\Tables\ReferralRewardPackagesTableEnum;
use App\Enums\Database\Tables\ReferralSessionsTableEnum as TableEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\Models\SuperClasses\SuperModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferralSession extends SuperModel
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
            // TableEnum::Status->dbName(), // Do not use status as fillable

            TableEnum::Name->dbName(),
            TableEnum::PackageId->dbName(),
            TableEnum::StartedAt->dbName(),
            TableEnum::FinishedAt->dbName(),
            TableEnum::PrivateNote->dbName(),

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
     * Get the referralRewardPackage that owns the ReferralSession
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
     * Interact with the ReferralSession's StartedAt.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function startedAt(): Attribute
    {
        $user = User::authUser();

        return Attribute::make(
            get: fn (?string $value) => is_null($user) ? $value : $user->convertUTCToLocalTime($value),
            set: fn (string $value) => is_null($user) ? $value : $user->convertLocalTimeToUTC($value),
        );
    }

    /**
     * Interact with the ReferralSession's FinishedAt.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function finishedAt(): Attribute
    {
        $user = User::authUser();

        return Attribute::make(
            get: fn (?string $value) => is_null($user) ? $value : $user->convertUTCToLocalTime($value),
            set: fn (string $value) => is_null($user) ? $value : $user->convertLocalTimeToUTC($value),
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
            ->Status($filter)
            ->StartedAt($filter)
            ->FinishedAt($filter)
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
                ->orderBy(TableEnum::Status->dbName(), 'asc')
                ->orderBy(TableEnum::StartedAt->dbName(), 'asc');
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
     * Scope a query to only include "Name" as request.
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
     * Scope a query to only include "Status" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeDropbox(TableEnum::Status->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "started_at" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStartedAt(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeDateRange(TableEnum::StartedAt->dbName(), $query, $filter, null, User::authUser()->getCalendarHelper());
    }

    /**
     * Scope a query to only include "finished_at" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFinishedAt(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeDateRange(TableEnum::FinishedAt->dbName(), $query, $filter, null, User::authUser()->getCalendarHelper());
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
