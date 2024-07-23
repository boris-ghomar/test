<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bonus;

use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\traits\Enums\EnumCastParams;
use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;
use App\Interfaces\Castable;

enum AddClientToBonusModelEnum implements Castable
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

    case ClientId; // int Client/Player Id to assign a free bet token
    case PartnerBonusId; // int Id field of GetBonuses API call
    case Amount; // decimal Amount in Client’s currency
    case ExternalId; // int? (optional) API partner’s corresponding bonus on their platform
    case ExternalClientId; // string (optional) If this field is present ClientId should be 0
    case ExternalBonusId; // int? (optional) If this field is present PartnerBonusId should be 0
    case AutoAccept; // bool? (optional) If this field is true the bonus will be accepted on player’s behalf
    case RequestHash; //string

    // Only used in API response in case of success
    case Id; // int Assigned bonus Id (Unique ID for assigned bonus to player)
    case Name; // string (Defined name of bonus in backoffice)
    case Description; // string (Defined description of bonus in backoffice)
    case CreatedStamp; // int timestamp
    case ExpirationDateStamp; // int timestamp

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

            self::ClientId, self::PartnerBonusId, self::ExternalId, self::ExternalBonusId,
            self::Id, self::CreatedStamp, self::ExpirationDateStamp
            => CastEnum::Int,

            self::Amount => CastEnum::Float,

            self::AutoAccept => CastEnum::Boolean,

            default => CastEnum::String
        };

        return $castEnum->cast($value);
    }
}
