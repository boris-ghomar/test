<?php

namespace App\Models\BackOffice\Bets;

use App\Enums\Database\Tables\BetSelectionsTableEnum as TableEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\Models\SuperClasses\SuperModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BetSelection extends SuperModel
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
            // TableEnum::BetId->dbName(), // Disabled for do not auto fill by api keys
            TableEnum::SelectionId->dbName(),
            TableEnum::SelectionName->dbName(),
            TableEnum::MarketId->dbName(),
            TableEnum::MarketName->dbName(),
            TableEnum::MatchId->dbName(),
            TableEnum::MatchShortId->dbName(),
            TableEnum::MatchName->dbName(),
            TableEnum::MatchStartDate->dbName(),
            TableEnum::RegionId->dbName(),
            TableEnum::RegionName->dbName(),
            TableEnum::CompetitionId->dbName(),
            TableEnum::CompetitionName->dbName(),
            TableEnum::SportId->dbName(),
            TableEnum::SportName->dbName(),
            TableEnum::SportAlias->dbName(),
            TableEnum::Odds->dbName(),
            TableEnum::IsLive->dbName(),
            TableEnum::Basis->dbName(),
            TableEnum::MatchInfo->dbName(),
            TableEnum::SelectionScore->dbName(),
            TableEnum::IsOutright->dbName(),
            TableEnum::ResettlementReason->dbName(),
            TableEnum::Status->dbName(),
        ];

        $this->attributes = [
            TableEnum::IsLive->dbName() => 0,
            TableEnum::IsOutright->dbName() => 0,
        ];

        $this->casts = [
            TableEnum::IsLive->dbName() => 'boolean',
            TableEnum::IsOutright->dbName() => 'boolean',
        ];

        parent::__construct($attributes);
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/
    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/

    /**
     * Interact with the BetSelection's IsLive.
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
    public function isLive(): Attribute
    {
        return Attribute::make(
            get: fn (bool|int|string $value)    => CastEnum::Boolean->cast($value),
            set: fn (bool|int|string $value)    => CastEnum::Boolean->cast($value),
        );
    }

    /**
     * Interact with the BetSelection's IsOutright.
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
    public function IsOutright(): Attribute
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
                ->orderBy(TableEnum::Status->dbName(), 'asc');
        }, $replaceSortFields);
    }


    /**
     * Scope a query to only include "is_live" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsLive(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeCheckbox(TableEnum::IsLive->dbName(), $query, $filter);
    }


    /**************** scopes END ********************/
}
