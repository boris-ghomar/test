<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bet;

use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\traits\Enums\EnumCastParams;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Traits\FilterableEnum;
use App\Interfaces\Castable;
use Carbon\Carbon;

enum FilterBetModelEnum implements Castable
{
    use EnumCastParams;
    use FilterableEnum {
        filter as protected traitFilter;
    }

    case BetId; // long?
    case BetIds; // List<long>
    case CashDeskId; // int?
    case ClientId; // int?
    case SportsbookProfileId; // int?
    case Date; // long? UTC date (timestamp in seconds)
    case DateEnd; // long? UTC date
    case MaxRows; // int? Maximum count of Bet Ids
    case IsCashdeskBetsOnly; // bool? True indicates only bets placed from cashdesks


    /**
     * Convert the variable cast to the actual cast type.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function cast(mixed $value): mixed
    {
        /**
         * Default cast is string,
         * so only register non string cases
         */

        /** @var CastEnum $castEnum */
        $castEnum = match ($this) {

            self::CashDeskId, self::ClientId, self::SportsbookProfileId, self::MaxRows, self::Date, self::DateEnd, self::BetId
            => CastEnum::Int,

            self::IsCashdeskBetsOnly => CastEnum::Boolean,

            default => CastEnum::String
        };

        return $castEnum->cast($value);
    }

    /**
     * @override FilterableEnum
     *
     * Prepare case for merge in filter array
     * with other cases.
     *
     * @param  mixed $value
     * @return array
     */
    public function filter(mixed $value)
    {
        if ($this == self::Date || $this == self::DateEnd) {
            // Convert date to timestamp format

            try {
                $timestamp = is_numeric($value) ? $value : Carbon::parse($value)->timestamp;
                $value = $timestamp;
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        return $this->traitFilter($value);
    }
}
