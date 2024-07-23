<?php

namespace App\Models\BackOffice\Bets;

use App\Enums\Bets\BetContextEnum;
use App\Enums\Bets\BetStatusEnum;
use App\Enums\Bets\BetTypeEnum;
use App\Enums\Database\Tables\BetsTableEnum as TableEnum;
use App\Enums\General\PartnerEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\LogCreator;
use App\Models\SuperClasses\SuperModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bet extends SuperModel
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
            TableEnum::Partner->dbName(),
            TableEnum::Context->dbName(),
            TableEnum::PartnerBetId->dbName(),
            TableEnum::BetType->dbName(),
            TableEnum::TransactionId->dbName(),
            TableEnum::Amount->dbName(),
            TableEnum::WinAmount->dbName(),
            TableEnum::Odds->dbName(),
            TableEnum::BonusId->dbName(),
            TableEnum::BonusBetAmount->dbName(),
            TableEnum::Status->dbName(),
            TableEnum::CashoutAmount->dbName(),
            TableEnum::IsLive->dbName(),
            TableEnum::Currency->dbName(),
            TableEnum::ExternalId->dbName(),
            TableEnum::Barcode->dbName(),
            TableEnum::ParentBetId->dbName(),
            TableEnum::AcceptType->dbName(),
            TableEnum::Descr->dbName(),
            TableEnum::PlacedAt->dbName(),
            TableEnum::CalculatedAt->dbName(),
            TableEnum::PaidAt->dbName(),
        ];

        $this->attributes = [
            TableEnum::IsLive->dbName() => 0,
        ];

        $this->casts = [
            TableEnum::IsLive->dbName() => 'boolean',
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

        self::saving(function (self $model) {

            $model[TableEnum::IsReferralBet->dbName()] = $model->isValidForReferral();

            return $model;
        });
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Add actual bet amount
     *
     * When the player withdraws some of the bet amount,
     * BetConstruct will misrepresent the bet amount and
     * this is to correct the bet to reflect the actual bet amount.
     *
     * @return bool
     */
    private function isValidForReferral(): bool
    {
        try {

            if ($this[TableEnum::Context->dbName()] != BetContextEnum::Sport->name)
                return false;

            if (!is_null($this[TableEnum::BonusId->dbName()]))
                return false;

            if (!is_null($this[TableEnum::BonusBetAmount->dbName()]))
                if ($this[TableEnum::BonusBetAmount->dbName()] > 0)
                    return false;

            if (!is_null($this[TableEnum::CashoutAmount->dbName()]))
                if ($this[TableEnum::CashoutAmount->dbName()] > 0)
                    return false;

            $betType = $this[TableEnum::BetType->dbName()];
            if ($betType != BetTypeEnum::Singel->name && $betType != BetTypeEnum::Multiple->name)
                return false;

            $status = $this[TableEnum::Status->dbName()];
            if ($status != BetStatusEnum::Won->name && $status != BetStatusEnum::Lost->name)
                return false;

            if ($status == BetStatusEnum::Won->name) {

                $amount = $this[TableEnum::Amount->dbName()];
                $odds = $this[TableEnum::Odds->dbName()];
                $winAmount = $this[TableEnum::WinAmount->dbName()];

                $calculatedWinAmount = round($amount * $odds, 4);

                if ($winAmount < $calculatedWinAmount) {
                    // if $winAmount > $calculatedWinAmount It's not partly cashed out, it's wrong calculation from Partner

                    $diff = $calculatedWinAmount - $winAmount;

                    if ($diff > $winAmount * 0.03) // Ignore 3% for calculation round decimals
                        return false;
                }
            }

            return true;
        } catch (\Throwable $th) {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                $th->getMessage(),
                'Diagnosis of the is referral bet issue!'
            );

            return false;
        }
    }

    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/
    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/

    /**
     * Interact with the bet's IsLive.
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
    /**************** Accessors & Mutators END ********************/

    /**************** scopes Collection ********************/

    /**
     * Get scope of desire partner bets
     *
     * @param  mixed $query
     * @param  mixed $partner
     * @return Builder
     */
    public function scopePartnerBets(Builder $query, PartnerEnum $partner): Builder
    {

        return $query->where(TableEnum::Partner->dbName(), $partner->name);
    }

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
                ->orderBy(TableEnum::PlacedAt->dbName(), 'desc');
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
