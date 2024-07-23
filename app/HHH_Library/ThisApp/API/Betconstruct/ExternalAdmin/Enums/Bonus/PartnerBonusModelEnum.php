<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bonus;

use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\traits\Enums\EnumCastParams;
use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;
use App\Interfaces\Castable;

enum PartnerBonusModelEnum implements Castable
{
    /**
     * NOTICE:
     *
     * To add a new item to this model, if you want to store in the database,
     * the new item must be added to the database table.
     *
     * DB tabel: betconstruct_bets
     */

    use EnumCastParams;
    use EnumToDatabaseColumnName;

    case Id; // int Partner Bonus Id
    case Name; // string Bonus name
    case Description; // string Bonus description to be shown to clients
    case InternalDesc; // string Free text for internal usage
    case StartDateTS; // long A date when this bonus definition campaign starts
    case EndDateTS; // long? A date when this bonus definition campaign ends. If null, then never ends
    case ExpirationDays; // int? Number of days after which free bet token expires
    case Type; // int SportBonus = 1,WageringBonus = 2,FreeBet = 6 (Registered in App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bonus\BonusTypeEnum)
    case Triggertype; // int
    case ExternalId; // int
    case IsDisabled; // Boolean
    case PlayerMaxCount; // int?
    case Note; //string?
    case RequestHash; //string


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

            self::Id, self::StartDateTS, self::EndDateTS, self::ExpirationDays, self::Type,
            self::Triggertype, self::ExternalId, self::PlayerMaxCount
            => CastEnum::Int,

            self::IsDisabled => CastEnum::Boolean,

            default => CastEnum::String
        };

        return $castEnum->cast($value);
    }
}
